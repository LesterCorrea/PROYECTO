<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;
use App\Services\DataStructures\FifoQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class LoanController extends Controller
{
    // ── Reservas ────────────────────────────────────────────────────
    public function reservations(Request $request)
    {
        $status = $request->get('status', 'pending');

        $reservations = Reservation::with(['user', 'book.author', 'magazine'])
            ->where('status', $status)
            ->orderBy('queue_position')
            ->paginate(20);

        return view('librarian.loans.reservations', compact('reservations', 'status'));
    }

    public function confirmReservation(Reservation $reservation)
    {
        if (!$reservation->isPending()) {
            return back()->with('error', 'Esta reserva ya fue procesada.');
        }

        if ($reservation->book->available_copies < 1) {
            return back()->with('error', 'No hay copias disponibles para este libro.');
        }

        $reservation->update([
            'status'     => 'confirmed',
            'expires_at' => now()->addDays(2), // 2 días para ir a recogerlo
        ]);

        // Notificar al estudiante
        $reservation->user->notify(
            new \App\Notifications\ReservationConfirmed($reservation)
        );

        return back()->with('success', 'Reserva confirmada. El estudiante fue notificado.');
    }

    public function rejectReservation(Reservation $reservation)
    {
        $reservation->update(['status' => 'rejected']);

        // Reordenar cola FIFO
        $pending = Reservation::where('book_id', $reservation->book_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('queue_position')
            ->get();

        $queue = FifoQueue::fromReservations($pending);
        foreach ($queue->toArray() as $index => $res) {
            $res->update(['queue_position' => $index + 1]);
        }

        $reservation->user->notify(
            new \App\Notifications\ReservationRejected($reservation)
        );

        return back()->with('success', 'Reserva rechazada. Cola FIFO reordenada.');
    }

    // ── Préstamos ────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');

        $loans = Loan::with(['user', 'book.author', 'magazine', 'librarian'])
            ->where('status', $status)
            ->orderByDesc('loan_date')
            ->paginate(20);

        return view('librarian.loans.index', compact('loans', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'        => 'required|exists:users,id',
            'book_id'        => 'nullable|required_without_all:magazine_id,reservation_id|exists:books,id',
            'magazine_id'    => 'nullable|required_without_all:book_id,reservation_id|exists:magazines,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'due_date'       => 'required|date|after:today',
        ]);

        return DB::transaction(function () use ($request) {
            $user = \App\Models\User::findOrFail($request->user_id);

            if ($user->hasPendingFines()) {
                return back()->with('error', 'El usuario tiene multas pendientes.');
            }

            $bookId = null;
            $magazineId = null;
            $item = null;

            // FLUJO 1: Préstamo desde RESERVA CONFIRMADA
            if ($request->reservation_id) {
                $reservation = \App\Models\Reservation::findOrFail($request->reservation_id);

                if (!$reservation->isConfirmed()) {
                    return back()->with('error', 'Solo se pueden convertir reservas confirmadas en préstamos.');
                }

                $bookId     = $reservation->book_id;
                $magazineId = $reservation->magazine_id;

                // Bloqueamos la fila del ítem correspondiente usando lockForUpdate
                if ($bookId) {
                    $item = \App\Models\Book::where('id', $bookId)->lockForUpdate()->first();
                } elseif ($magazineId) {
                    $item = \App\Models\Magazine::where('id', $magazineId)->lockForUpdate()->first();
                }

                if (!$item) {
                    return back()->with('error', 'No se encontró el ítem reservado.');
                }

                // Marcar reserva como COMPLETADA
                $reservation->update(['status' => 'completed']);
            } else {
                // FLUJO 2: Préstamo directo sin reserva
                if ($request->book_id) {
                    $bookId = $request->book_id;
                    $item = \App\Models\Book::where('id', $bookId)->lockForUpdate()->findOrFail($bookId);
                } elseif ($request->magazine_id) {
                    $magazineId = $request->magazine_id;
                    $item = \App\Models\Magazine::where('id', $magazineId)->lockForUpdate()->findOrFail($magazineId);
                } else {
                    return back()->with('error', 'Debe indicar un libro o una revista.');
                }
            }

            // Validación de copias (Segura gracias al lockForUpdate previo)
            if ($item->available_copies < 1) {
                return back()->with('error', "No hay copias disponibles de este ítem.");
            }

            // Obtener último préstamo del usuario para la lista enlazada
            $lastLoan = \App\Models\Loan::where('user_id', $user->id)->latest()->first();

            // Crear el préstamo (ahora soporta book_id o magazine_id)
            \App\Models\Loan::create([
                'user_id'          => $user->id,
                'book_id'          => $bookId,
                'magazine_id'      => $magazineId, // Asegúrate de tener esta columna en tu tabla loans
                'reservation_id'   => $request->reservation_id,
                'librarian_id'     => auth()->id(),
                'loan_date'        => now(),
                'due_date'         => $request->due_date,
                'status'           => 'active',
                'previous_loan_id' => $lastLoan?->id,
            ]);

            // Actualizar inventario
            $item->decrement('available_copies');
            $item->increment('loan_count');

            // REINCORPORADO: Limpieza de caché obligatoria al alterar inventario
            Cache::forget('home_stats');

            return back()->with('success', 'Préstamo registrado correctamente.');
        });
    }

    public function returnBook(Loan $loan)
    {
        if ($loan->status === 'returned') {
            return back()->with('error', 'Este préstamo ya fue devuelto.');
        }

        $loan->update([
            'return_date' => now(),
            'status'      => 'returned',
        ]);

        //  NUEVA LÓGICA: Restaurar inventario dependiendo de si es libro o revista
        if ($loan->book_id) {
            $loan->book->increment('available_copies');
        } elseif ($loan->magazine_id) {
            $loan->magazine->increment('available_copies');
        }

        // Generar multa si hay retraso
        if (now()->isAfter($loan->due_date)) {
            $overdueDays  = now()->diffInDays($loan->due_date);
            $amountPerDay = 1.00;

            Fine::create([
                'loan_id'        => $loan->id,
                'user_id'        => $loan->user_id,
                'overdue_days'   => $overdueDays,
                'amount_per_day' => $amountPerDay,
                'total_amount'   => $overdueDays * $amountPerDay,
                'status'         => 'pending',
            ]);

            $loan->user->notify(
                new \App\Notifications\FineGenerated($loan, $overdueDays)
            );

            return back()->with('warning', "Libro devuelto con {$overdueDays} días de retraso. Se generó una multa.");
        }

        return back()->with('success', 'Libro devuelto correctamente.');
    }

    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');

        $users = \App\Models\User::role('student')
            ->where(fn($q) => $q
                ->where('name',       'like', "%{$query}%")
                ->orWhere('email',    'like', "%{$query}%")
                ->orWhere('student_id', 'like', "%{$query}%"))
            ->where('is_active', true)
            ->limit(10)
            ->get(['id', 'name', 'email', 'student_id']);

        return response()->json($users);
    }

    // Nuevos metodos -- > Help to admin search 

    public function searchLoanItems(Request $request)
    {
        $query = $request->get('q', '');

        $books = \App\Models\Book::with('author')
            ->where(fn($q) => $q
                ->where('title', 'like', "%{$query}%")
                ->orWhere('isbn',  'like', "%{$query}%")
                ->orWhereHas('author', fn($q) => $q->where('name', 'like', "%{$query}%")))
            ->limit(8)
            ->get()
            ->map(fn($b) => [
                'id'       => $b->id,
                'type'     => 'book',
                'title'    => $b->title,
                'subtitle' => $b->author?->name ?? '',
                'cover'    => $b->cover_url,
                'available' => $b->available_copies,
            ]);

        $magazines = \App\Models\Magazine::with('authors')
            ->where(fn($q) => $q
                ->where('title', 'like', "%{$query}%")
                ->orWhere('issn',  'like', "%{$query}%"))
            ->limit(8)
            ->get()
            ->map(fn($m) => [
                'id'       => $m->id,
                'type'     => 'magazine',
                'title'    => $m->title,
                'subtitle' => $m->authors->pluck('name')->join(', '),
                'cover'    => $m->cover_url,
                'available' => $m->available_copies,
            ]);

        return response()->json(
            $books->concat($magazines)->sortBy('title')->values()
        );
    }
}
