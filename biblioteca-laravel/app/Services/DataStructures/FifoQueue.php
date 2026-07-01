<?php

namespace App\Services\DataStructures;

/**
 * Cola FIFO (First In, First Out)
 * Aplicación: gestionar el orden de reservas de un libro.
 * El primero en reservar es el primero en ser atendido.
 */
class FifoQueue
{
    private array $items = [];

    // ── Encolar ─────────────────────────────────────────────────────
    public function enqueue(mixed $item): void
    {
        $this->items[] = $item;
    }

    // ── Desencolar ──────────────────────────────────────────────────
    public function dequeue(): mixed
    {
        if ($this->isEmpty()) {
            throw new \UnderflowException('La cola está vacía.');
        }
        return array_shift($this->items);
    }

    // ── Ver el frente sin eliminar ──────────────────────────────────
    public function peek(): mixed
    {
        if ($this->isEmpty()) {
            throw new \UnderflowException('La cola está vacía.');
        }
        return $this->items[0];
    }

    // ── Utilidades ──────────────────────────────────────────────────
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function size(): int
    {
        return count($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    // ── Construir cola desde reservas pendientes de un libro ────────
    public static function fromReservations(iterable $reservations): self
    {
        $queue = new self();
        // Ordenar por queue_position garantiza el orden FIFO
        $sorted = collect($reservations)->sortBy('queue_position');
        foreach ($sorted as $reservation) {
            $queue->enqueue($reservation);
        }
        return $queue;
    }

    // ── Obtener posición de un usuario en la cola ───────────────────
    public function getPosition(int $userId): int
    {
        foreach ($this->items as $index => $reservation) {
            if ($reservation->user_id === $userId) {
                return $index + 1; // posición 1-based
            }
        }
        return -1; // no está en la cola
    }
}