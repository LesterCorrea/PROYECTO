<?php

namespace App\Services\DataStructures;

class DoublyNode
{
    public mixed      $data;
    public ?DoublyNode $prev = null;
    public ?DoublyNode $next = null;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }
}

/**
 * Lista Doblemente Enlazada
 * Aplicación: libros dentro de una saga/colección.
 * Permite navegar al libro anterior y siguiente desde la página de detalle.
 */
class DoublyLinkedList
{
    private ?DoublyNode $head = null;
    private ?DoublyNode $tail = null;
    private int         $size = 0;

    // ── Insertar al final ───────────────────────────────────────────
    public function append(mixed $data): void
    {
        $node = new DoublyNode($data);

        if ($this->head === null) {
            $this->head = $node;
            $this->tail = $node;
        } else {
            $node->prev       = $this->tail;
            $this->tail->next = $node;
            $this->tail       = $node;
        }
        $this->size++;
    }

    // ── Buscar nodo por ID del libro ────────────────────────────────
    public function findById(int $id): ?DoublyNode
    {
        $current = $this->head;
        while ($current !== null) {
            if ($current->data->id === $id) {
                return $current;
            }
            $current = $current->next;
        }
        return null;
    }

    // ── Obtener libro anterior y siguiente dado un ID ───────────────
    public function getNeighbors(int $bookId): array
    {
        $node = $this->findById($bookId);
        if ($node === null) return ['prev' => null, 'next' => null];

        return [
            'prev' => $node->prev?->data,
            'next' => $node->next?->data,
        ];
    }

    // ── Recorrer ────────────────────────────────────────────────────
    public function toArray(): array
    {
        $result  = [];
        $current = $this->head;
        while ($current !== null) {
            $result[] = $current->data;
            $current  = $current->next;
        }
        return $result;
    }

    public function size(): int { return $this->size; }
    public function isEmpty(): bool { return $this->head === null; }

    // ── Construir desde libros de una saga ordenados ────────────────
    public static function fromSaga(iterable $books): self
    {
        $list = new self();
        $sorted = collect($books)->sortBy('saga_order');
        foreach ($sorted as $book) {
            $list->append($book);
        }
        return $list;
    }
}