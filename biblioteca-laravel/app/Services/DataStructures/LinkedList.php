<?php

namespace App\Services\DataStructures;

/**
 * Lista Enlazada Simple
 * Aplicación: historial cronológico de préstamos por usuario.
 * Cada préstamo apunta al anterior (previous_loan_id en BD).
 */
class LinkedList
{
    private ?LinkedListNode $head = null;
    private int             $size = 0;

    // ── Insertar al inicio ──────────────────────────────────────────
    public function prepend(mixed $data): void
    {
        $node       = new LinkedListNode($data);
        $node->next = $this->head;
        $this->head = $node;
        $this->size++;
    }

    // ── Insertar al final ───────────────────────────────────────────
    public function append(mixed $data): void
    {
        $node = new LinkedListNode($data);

        if ($this->head === null) {
            $this->head = $node;
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $node;
        }
        $this->size++;
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

    // ── Utilidades ──────────────────────────────────────────────────
    public function size(): int
    {
        return $this->size;
    }

    public function isEmpty(): bool
    {
        return $this->head === null;
    }

    // ── Construir desde historial de préstamos del usuario ──────────
    public static function fromLoanHistory(iterable $loans): self
    {
        $list = new self();
        // Los préstamos vienen ordenados por fecha descendente
        foreach ($loans as $loan) {
            $list->append($loan);
        }
        return $list;
    }
}