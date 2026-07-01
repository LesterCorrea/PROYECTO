<?php

namespace App\Services\DataStructures;

class CircularNode
{
    public mixed        $data;
    public CircularNode $prev;
    public CircularNode $next;

    public function __construct(mixed $data)
    {
        $this->data = $data;
        $this->prev = $this;
        $this->next = $this;
    }
}

/**
 * Lista Circular Doblemente Enlazada
 * Aplicación: carruseles de la página pública.
 * El último elemento apunta al primero → navegación infinita.
 */
class CircularDoublyLinkedList
{
    private ?CircularNode $head = null;
    private int           $size = 0;

    // ── Insertar al final ───────────────────────────────────────────
    public function append(mixed $data): void
    {
        $node = new CircularNode($data);

        if ($this->head === null) {
            $this->head = $node;
        } else {
            $tail       = $this->head->prev;
            $tail->next = $node;
            $node->prev = $tail;
            $node->next = $this->head;
            $this->head->prev = $node;
        }
        $this->size++;
    }

    // ── Obtener siguiente elemento (navegación carrusel) ────────────
    public function getNext(int $currentIndex): mixed
    {
        if ($this->head === null) return null;
        $node = $this->getNodeAt($currentIndex);
        return $node?->next->data;
    }

    // ── Obtener anterior (navegación carrusel) ──────────────────────
    public function getPrev(int $currentIndex): mixed
    {
        if ($this->head === null) return null;
        $node = $this->getNodeAt($currentIndex);
        return $node?->prev->data;
    }

    // ── Obtener nodo por índice ──────────────────────────────────────
    private function getNodeAt(int $index): ?CircularNode
    {
        if ($this->head === null) return null;
        $current = $this->head;
        for ($i = 0; $i < $index; $i++) {
            $current = $current->next;
            if ($current === $this->head) return null;
        }
        return $current;
    }

    // ── Convertir a array (para renderizar el carrusel) ─────────────
    public function toArray(): array
    {
        if ($this->head === null) return [];
        $result  = [];
        $current = $this->head;
        do {
            $result[] = $current->data;
            $current  = $current->next;
        } while ($current !== $this->head);
        return $result;
    }

    public function size(): int { return $this->size; }
    public function isEmpty(): bool { return $this->head === null; }

    // ── Construir desde items de una lista destacada ─────────────────
    public static function fromFeaturedList(iterable $items): self
    {
        $list   = new self();
        $sorted = collect($items)->sortBy('order');
        foreach ($sorted as $item) {
            $list->append($item->itemable);
        }
        return $list;
    }
}