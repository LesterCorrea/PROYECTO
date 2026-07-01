<?php

namespace App\Services\DataStructures;

/**
 * Árbol Binario de Búsqueda (BST)
 * Aplicación: organizar y buscar libros por ID en el catálogo.
 */
class BinarySearchTree
{
    private ?BSTNode $root = null;

    // ── Insertar ────────────────────────────────────────────────────
    public function insert(int $key, mixed $data): void
    {
        $this->root = $this->insertNode($this->root, $key, $data);
    }

    private function insertNode(?BSTNode $node, int $key, mixed $data): BSTNode
    {
        if ($node === null) {
            return new BSTNode($key, $data);
        }

        if ($key < $node->key) {
            $node->left  = $this->insertNode($node->left, $key, $data);
        } elseif ($key > $node->key) {
            $node->right = $this->insertNode($node->right, $key, $data);
        } else {
            // Clave duplicada → actualizar datos
            $node->data = $data;
        }

        return $node;
    }

    // ── Buscar ──────────────────────────────────────────────────────
    public function search(int $key): mixed
    {
        return $this->searchNode($this->root, $key);
    }

    private function searchNode(?BSTNode $node, int $key): mixed
    {
        if ($node === null) return null;
        if ($key === $node->key) return $node->data;

        return $key < $node->key
            ? $this->searchNode($node->left, $key)
            : $this->searchNode($node->right, $key);
    }

    // ── Recorrido inorden (resultado ordenado por clave) ────────────
    public function inorder(): array
    {
        $result = [];
        $this->inorderTraversal($this->root, $result);
        return $result;
    }

    private function inorderTraversal(?BSTNode $node, array &$result): void
    {
        if ($node === null) return;
        $this->inorderTraversal($node->left, $result);
        $result[] = $node->data;
        $this->inorderTraversal($node->right, $result);
    }

    // ── Construir BST desde colección de libros ─────────────────────
    public static function fromCollection(iterable $books): self
    {
        $bst = new self();
        foreach ($books as $book) {
            $bst->insert($book->id, $book);
        }
        return $bst;
    }

    // ── Obtener todos los libros ordenados por ID ───────────────────
    public function getSorted(): array
    {
        return $this->inorder();
    }
}