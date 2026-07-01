<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reserva Confirmada — ' . $this->reservation->book->title)
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line('Tu reserva del libro **' . $this->reservation->book->title . '** ha sido confirmada.')
            ->line('Tienes **2 días** para pasar a recogerlo a la biblioteca.')
            ->line('Si no lo recoges en ese plazo, la reserva será cancelada automáticamente.')
            ->action('Ver mi reserva', url('/mi-cuenta/reservas'))
            ->line('Gracias por usar el Sistema de Biblioteca.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'reservation_confirmed',
            'message' => 'Tu reserva de "' . $this->reservation->book->title . '" fue confirmada.',
            'book_id' => $this->reservation->book_id,
            'url'     => '/mi-cuenta/reservas',
        ];
    }
}