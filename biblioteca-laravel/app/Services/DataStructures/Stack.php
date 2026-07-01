<?php

namespace App\Services\DataStructures;

/**
 * Pila (Stack) — LIFO (Last In, First Out)
 * Aplicación: historial de navegación del usuario.
 * Permite volver a la página anterior con el botón "atrás".
 */
class Stack
{
    private array $items   = [];
    private int   $maxSize;

    public function __construct(int $maxSize = 50)
    {
        $this->maxSize = $maxSize;
    }

    // ── Apilar ──────────────────────────────────────────────────────
    public function push(mixed $item): void
    {
        if ($this->size() >= $this->maxSize) {
            // Eliminar el elemento más antiguo (fondo de la pila)
            array_shift($this->items);
        }
        $this->items[] = $item;
    }

    // ── Desapilar ───────────────────────────────────────────────────
    public function pop(): mixed
    {
        if ($this->isEmpty()) {
            throw new \UnderflowException('La pila está vacía.');
        }
        return array_pop($this->items);
    }

    // ── Ver cima sin eliminar ───────────────────────────────────────
    public function peek(): mixed
    {
        if ($this->isEmpty()) {
            throw new \UnderflowException('La pila está vacía.');
        }
        return end($this->items);
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
        return array_reverse($this->items); // más reciente primero
    }

    // ── Guardar en sesión ───────────────────────────────────────────
    public function saveToSession(string $key = 'nav_history'): void
    {
        session([$key => $this->items]);
    }

    public static function loadFromSession(string $key = 'nav_history'): self
    {
        $stack        = new self();
        $stack->items = session($key, []);
        return $stack;
    }
}