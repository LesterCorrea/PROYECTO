<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Magazine;
use App\Models\Reservation;
use App\Services\DataStructures\FifoQueue;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::where('user_id', auth()->id())
            ->with(['book.author', 'magazine', 'loan'])
            ->orderBy('queue_position')
            ->get();

        $queue = FifoQueue::fromReservations(
            $reservations->whereIn('status', ['pending', 'confirmed'])
        );

        return view('student.reservations.index', compact('reservations', 'queue'));
    }

    // Reservar un LIBRO
    public function store(Request $request, Book $book)
    {
        $user = auth()->user();

        if ($user->hasPendingFines()) {
            return back()->with('error', 'Tienes multas pendientes. Debes pagarlas antes de reservar.');
        }

        $existing = Reservation::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Ya tienes una reserva activa para este libro.');
        }

        $activeLoan = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'active')
            ->exists();

        if ($activeLoan) {
            return back()->with('error', 'Ya tienes este libro en préstamo activo.');
        }

        $lastPosition = Reservation::where('book_id', $book->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->max('queue_position') ?? 0;

        Reservation::create([
            'user_id'        => $user->id,
            'book_id'        => $book->id,
            'magazine_id'    => null,
            'status'         => 'pending',
            'queue_position' => $lastPosition + 1,
            'reserved_at'    => now(),
            'expires_at'     => now()->addDays(3),
        ]);

        return back()->with('success', "Reserva realizada. Tu posición en la cola es #" . ($lastPosition + 1));
    }

    // Reservar una REVISTA
    public function storeMagazine(Request $request, Magazine $magazine)
    {
        $user = auth()->user();

        if ($user->hasPendingFines()) {
            return back()->with('error', 'Tienes multas pendientes. Debes pagarlas antes de reservar.');
        }

        $existing = Reservation::where('user_id', $user->id)
            ->where('magazine_id', $magazine->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Ya tienes una reserva activa para esta revista.');
        }

        // Verificar que la revista tiene copias
        if ($magazine->available_copies < 1) {
            return back()->with('error', 'No hay copias disponibles de esta revista en este momento.');
        }

        $lastPosition = Reservation::where('magazine_id', $magazine->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->max('queue_position') ?? 0;

        Reservation::create([
            'user_id'        => $user->id,
            'book_id'        => null,
            'magazine_id'    => $magazine->id,
            'status'         => 'pending',
            'queue_position' => $lastPosition + 1,
            'reserved_at'    => now(),
            'expires_at'     => now()->addDays(3),
        ]);

        return back()->with('success', "Reserva de revista realizada. Tu posición en la cola es #" . ($lastPosition + 1));
    }

    public function cancel(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$reservation->isPending()) {
            return back()->with('error', 'Solo puedes cancelar reservas pendientes.');
        }

        $reservation->update(['status' => 'cancelled']);
        $this->reorderQueue($reservation->book_id, $reservation->magazine_id);

        return back()->with('success', 'Reserva cancelada correctamente.');
    }

    private function reorderQueue(?int $bookId, ?int $magazineId): void
    {
        $query = Reservation::whereIn('status', ['pending', 'confirmed'])
            ->orderBy('queue_position');

        if ($bookId) {
            $query->where('book_id', $bookId);
        } elseif ($magazineId) {
            $query->where('magazine_id', $magazineId);
        } else {
            return;
        }

        $pending = $query->get();
        $queue   = FifoQueue::fromReservations($pending);

        foreach ($queue->toArray() as $index => $res) {
            $res->update(['queue_position' => $index + 1]);
        }
    }
}
