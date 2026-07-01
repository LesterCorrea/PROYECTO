<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Fine;

class FineController extends Controller
{
    public function index()
    {
        $fines = Fine::with(['user', 'loan.book'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalPending = Fine::where('status', 'pending')->sum('total_amount');

        return view('librarian.fines.index', compact('fines', 'totalPending'));
    }

    public function markAsPaid(Fine $fine)
    {
        $fine->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Multa marcada como pagada.');
    }
}