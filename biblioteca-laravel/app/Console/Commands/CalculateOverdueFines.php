<?php

namespace App\Console\Commands;

use App\Models\Fine;
use App\Models\Loan;
use App\Notifications\LoanOverdue;
use Illuminate\Console\Command;

class CalculateOverdueFines extends Command
{
    protected $signature   = 'biblioteca:calcular-multas';
    protected $description = 'Calcula multas automáticas por préstamos vencidos y notifica a usuarios';

    public function handle(): void
    {
        $this->info('Calculando préstamos vencidos...');

        // Préstamos activos vencidos sin multa generada
        $overdueLoans = Loan::where('status', 'active')
            ->where('due_date', '<', now())
            ->whereDoesntHave('fine')
            ->with(['user', 'book'])
            ->get();

        $count = 0;

        foreach ($overdueLoans as $loan) {
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

            $loan->update(['status' => 'overdue']);

            // Notificar al usuario
            $loan->user->notify(new LoanOverdue($loan));

            $count++;
        }

        $this->info("{$count} multas generadas correctamente.");
    }
}