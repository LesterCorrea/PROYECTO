<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanOverdue extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Loan $loan) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = now()->diffInDays($this->loan->due_date, false);
        $message  = $daysLeft >= 0
            ? "Tu préstamo vence en **{$daysLeft} día(s)**."
            : "Tu préstamo venció hace **" . abs($daysLeft) . " día(s)**.";

        return (new MailMessage)
            ->subject('Aviso de Préstamo — ' . $this->loan->book->title)
            ->greeting('Hola, ' . $notifiable->name . '.')
            ->line($message)
            ->line('Libro: **' . $this->loan->book->title . '**')
            ->line('Fecha límite: **' . $this->loan->due_date->format('d/m/Y') . '**')
            ->line('Por favor devuelve el libro a tiempo para evitar multas.')
            ->action('Ver mi historial', url('/mi-cuenta/historial'))
            ->line('Gracias por usar el Sistema de Biblioteca.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'loan_overdue',
            'message' => 'Tu préstamo de "' . $this->loan->book->title . '" está por vencer.',
            'loan_id' => $this->loan->id,
            'url'     => '/mi-cuenta/historial',
        ];
    }
}