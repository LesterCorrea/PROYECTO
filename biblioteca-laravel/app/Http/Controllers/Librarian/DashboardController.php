<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Magazine;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas del dashboard
        $stats = [
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'active_loans'         => Loan::where('status', 'active')->count(),
            'overdue_loans'        => Loan::where('status', 'overdue')->count(),
            'pending_fines'        => Fine::where('status', 'pending')->count(),
            'total_books'          => Book::count(),
            'total_magazines'      => Magazine::count(),
        ];

        // Reservas pendientes recientes
        $pendingReservations = Reservation::with(['user', 'book'])
            ->where('status', 'pending')
            ->orderBy('queue_position')
            ->take(10)
            ->get();

        // Préstamos vencidos
        $overdueLoans = Loan::with(['user', 'book'])
            ->where('status', 'active')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->take(10)
            ->get();

        return view('librarian.dashboard', compact(
            'stats',
            'pendingReservations',
            'overdueLoans'
        ));
    }
}