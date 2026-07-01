<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Magazine;
use App\Models\Reservation;
use App\Models\User;
use App\Services\DataStructures\SearchAlgorithms;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'          => User::count(),
            'total_books'          => Book::count(),
            'total_magazines'      => Magazine::count(),
            'active_loans'         => Loan::where('status', 'active')->count(),
            'overdue_loans'        => Loan::where('status', 'overdue')->count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'pending_fines'        => Fine::where('status', 'pending')->count(),
            'total_fines_amount'   => Fine::where('status', 'pending')->sum('total_amount'),
        ];

        // Usuarios más activos (más préstamos)
        $topUsers = User::withCount('loans')
            ->orderByDesc('loans_count')
            ->take(5)
            ->get();

        // Libros más prestados — MergeSort
        $topBooks = Book::orderByDesc('loan_count')
            ->take(5)
            ->get()
            ->toArray();

        $topBooks = SearchAlgorithms::mergeSort($topBooks, 'loan_count', false);

        // Préstamos vencidos recientes
        $overdueLoans = Loan::with(['user', 'book'])
            ->where('status', 'active')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'topUsers',
            'topBooks',
            'overdueLoans'
        ));
    }
}