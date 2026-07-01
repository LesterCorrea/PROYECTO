<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use App\Services\DataStructures\SearchAlgorithms;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function loans(Request $request)
    {
        $from   = $request->get('desde', now()->startOfMonth()->toDateString());
        $to     = $request->get('hasta', now()->toDateString());
        $sortBy = $request->get('ordenar', 'loan_date');
        $sortDir = $request->get('direccion', 'desc');

        $loans = Loan::with(['user', 'book.author'])
            ->whereBetween('loan_date', [$from, $to])
            ->get()
            ->toArray();

        // Ordenar reporte con MergeSort
        $allowed = ['loan_date', 'due_date', 'return_date', 'status'];
        $sortKey = in_array($sortBy, $allowed) ? $sortBy : 'loan_date';
        $loans   = SearchAlgorithms::mergeSort($loans, $sortKey, $sortDir === 'asc');

        $summary = [
            'total'    => count($loans),
            'active'   => collect($loans)->where('status', 'active')->count(),
            'returned' => collect($loans)->where('status', 'returned')->count(),
            'overdue'  => collect($loans)->where('status', 'overdue')->count(),
        ];

        return view('admin.reports.loans', compact('loans', 'summary', 'from', 'to'));
    }

    public function fines(Request $request)
    {
        $from    = $request->get('desde', now()->startOfMonth()->toDateString());
        $to      = $request->get('hasta', now()->toDateString());
        $status  = $request->get('status', 'all');
        $sortBy  = $request->get('ordenar', 'created_at');
        $sortDir = $request->get('direccion', 'desc');

        $fines = Fine::with(['user', 'loan.book'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->get()
            ->toArray();

        // MergeSort para ordenar reporte de multas
        $allowed = ['total_amount', 'overdue_days', 'created_at', 'status'];
        $sortKey = in_array($sortBy, $allowed) ? $sortBy : 'created_at';
        $fines   = SearchAlgorithms::mergeSort($fines, $sortKey, $sortDir === 'asc');

        $summary = [
            'total_amount'  => collect($fines)->sum('total_amount'),
            'paid_amount'   => collect($fines)->where('status', 'paid')->sum('total_amount'),
            'pending_amount' => collect($fines)->where('status', 'pending')->sum('total_amount'),
            'total_count'   => count($fines),
        ];

        return view('admin.reports.fines', compact('fines', 'summary', 'from', 'to', 'status'));
    }

    public function users(Request $request)
    {
        $sortBy  = $request->get('ordenar', 'loans_count');
        $sortDir = $request->get('direccion', 'desc');

        $users = User::withCount(['loans', 'reservations', 'fines'])
            ->with('roles')
            ->get()
            ->toArray();

        // MergeSort por actividad
        $allowed = ['name', 'loans_count', 'fines_count', 'created_at'];
        $sortKey = in_array($sortBy, $allowed) ? $sortBy : 'loans_count';
        $users   = SearchAlgorithms::mergeSort($users, $sortKey, $sortDir === 'asc');

        // Libros más prestados — MergeSort
        $topBooks = Book::orderByDesc('loan_count')
            ->take(10)
            ->get()
            ->toArray();
        $topBooks = SearchAlgorithms::mergeSort($topBooks, 'loan_count', false);

        return view('admin.reports.users', compact('users', 'topBooks'));
    }

    public function export(Request $request, string $type)
    {
        $allowedTypes = ['loans', 'fines', 'users'];

        if (!in_array($type, $allowedTypes)) {
            abort(404);
        }

        $data     = match ($type) {
            'loans' => Loan::with(['user', 'book'])->get(),
            'fines' => Fine::with(['user', 'loan.book'])->get(),
            'users' => User::with(['roles', 'loans', 'fines'])->get(),
        };

        $filename = "reporte_{$type}_" . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $type) {
            $file = fopen('php://output', 'w');

            // Cabeceras CSV según tipo
            match ($type) {
                'loans' => fputcsv($file, ['ID', 'Usuario', 'Libro', 'Fecha préstamo', 'Vence', 'Devuelto', 'Estado']),
                'fines' => fputcsv($file, ['ID', 'Usuario', 'Libro', 'Días retraso', 'Monto', 'Estado', 'Fecha']),
                'users' => fputcsv($file, ['ID', 'Nombre', 'Email', 'Rol', 'Préstamos', 'Multas', 'Registro']),
            };

            foreach ($data as $row) {
                match ($type) {
                    'loans' => fputcsv($file, [
                        $row->id,
                        $row->user->name,
                        $row->book->title,
                        $row->loan_date,
                        $row->due_date,
                        $row->return_date ?? 'Pendiente',
                        $row->status,
                    ]),
                    'fines' => fputcsv($file, [
                        $row->id,
                        $row->user->name,
                        $row->loan->book->title,
                        $row->overdue_days,
                        '$' . number_format($row->total_amount, 2),
                        $row->status,
                        $row->created_at->format('Y-m-d'),
                    ]),
                    'users' => fputcsv($file, [
                        $row->id,
                        $row->name,
                        $row->email,
                        $row->roles->pluck('name')->join(', '),
                        $row->loans->count(),
                        $row->fines->count(),
                        $row->created_at->format('Y-m-d'),
                    ]),
                };
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
