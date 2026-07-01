<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Reservation;
use App\Services\DataStructures\LinkedList;
use App\Services\DataStructures\FifoQueue;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Reservas: Precargamos libro Y revista
        $pendingReservations = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('queue_position')
            ->with(['book', 'magazine']) 
            ->get();

        $queue = FifoQueue::fromReservations($pendingReservations);

        // 2. Préstamos: Precargamos ambos
        $loans = Loan::where('user_id', $user->id)
            ->orderByDesc('loan_date')
            ->with(['book', 'magazine'])
            ->get();

        $loanHistory = LinkedList::fromLoanHistory($loans);

        $recentLoans = $loans->take(4);

        // 3. Multas: Precargamos la relación anidada
        $pendingFines = $user->pendingFines()->with(['loan.book', 'loan.magazine'])->get();

        // 4. Préstamo activo: Buscamos ambos
        $activeLoans = Loan::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['book', 'magazine'])
            ->get();

        return view('student.dashboard', compact(
            'queue',
            'loanHistory',
            'recentLoans',
            'pendingFines',
            'activeLoans',
            'pendingReservations'
        ));
    }

    public function fines()
    {
        $fines = auth()->user()
            ->fines()
            ->with(['loan.book', 'loan.magazine'])
            ->orderByDesc('created_at')
            ->get();

        return view('student.fines', compact('fines'));
    }

    public function history()
    {
        // Precargamos 'book.author' y 'magazine.authors'
        $loans = Loan::where('user_id', auth()->id())
            ->orderByDesc('loan_date')
            ->with(['book.author', 'magazine.authors'])
            ->get();

        $loanHistory = LinkedList::fromLoanHistory($loans);
        $history     = $loanHistory->toArray();

        return view('student.history', compact('history'));
    }
}