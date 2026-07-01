<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FineGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Loan $loan,
        public int  $overdueDays
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->overdueDays * 1.00, 2);

        return (new MailMessage)
            ->subject('Multa Generada — ' . $this->loan->book->title)
            ->greeting('Hola, ' . $notifiable->name . '.')
            ->line('Se ha generado una multa por la devolución tardía del libro **' . $this->loan->book->title . '**.')
            ->line("Días de retraso: **{$this->overdueDays} días**")
            ->line("Monto total: **\${$amount}**")
            ->line('Por favor, acércate a la biblioteca para regularizar tu situación.')
            ->action('Ver mis multas', url('/mi-cuenta/multas'))
            ->line('Gracias por tu comprensión.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'fine_generated',
            'message'      => "Multa de \${$this->overdueDays}.00 por devolución tardía de \"{$this->loan->book->title}\".",
            'loan_id'      => $this->loan->id,
            'overdue_days' => $this->overdueDays,
            'url'          => '/mi-cuenta/multas',
        ];
    }
}