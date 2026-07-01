<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationRejected extends Notification implements ShouldQueue
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
            ->subject('Reserva Rechazada — ' . $this->reservation->book->title)
            ->greeting('Hola, ' . $notifiable->name . '.')
            ->line('Lamentablemente tu reserva del libro **' . $this->reservation->book->title . '** fue rechazada.')
            ->line('Puedes intentar reservar otro ejemplar o consultar con el bibliotecario.')
            ->action('Ver catálogo', url('/libros'))
            ->line('Gracias por tu comprensión.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'reservation_rejected',
            'message' => 'Tu reserva de "' . $this->reservation->book->title . '" fue rechazada.',
            'book_id' => $this->reservation->book_id,
            'url'     => '/mi-cuenta/reservas',
        ];
    }
}