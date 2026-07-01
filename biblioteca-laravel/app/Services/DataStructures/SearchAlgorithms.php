<?php

namespace App\Services\DataStructures;

/**
 * Algoritmos de búsqueda y ordenamiento.
 *
 * Búsqueda Binaria → catálogo de libros (requiere array ordenado).
 * QuickSort        → ordenar catálogo por título, año, etc.
 * MergeSort        → ordenar datos de reportes.
 */
class SearchAlgorithms
{
    // ══════════════════════════════════════════════════════════════
    // BÚSQUEDA BINARIA
    // Precondición: el array debe estar ordenado por la clave dada.
    // ══════════════════════════════════════════════════════════════
    public static function binarySearch(array $items, mixed $target, string $key = 'id'): int
    {
        $low  = 0;
        $high = count($items) - 1;

        while ($low <= $high) {
            $mid   = intdiv($low + $high, 2);
            $value = is_array($items[$mid])
                ? $items[$mid][$key]
                : $items[$mid]->$key;

            if ($value === $target) {
                return $mid; // índice encontrado
            } elseif ($value < $target) {
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }
        return -1; // no encontrado
    }

    // ══════════════════════════════════════════════════════════════
    // QUICKSORT
    // Aplicación: ordenar el catálogo de libros por cualquier campo.
    // ══════════════════════════════════════════════════════════════
    public static function quickSort(array $items, string $key = 'title', bool $ascending = true): array
    {
        if (count($items) <= 1) return $items;

        $pivot  = $items[0];
        $left   = [];
        $right  = [];

        for ($i = 1; $i < count($items); $i++) {
            $itemValue  = is_array($items[$i]) ? $items[$i][$key] : $items[$i]->$key;
            $pivotValue = is_array($pivot) ? $pivot[$key] : $pivot->$key;

            $comparison = is_string($itemValue)
                ? strcasecmp($itemValue, $pivotValue)
                : ($itemValue <=> $pivotValue);

            if ($ascending ? $comparison <= 0 : $comparison > 0) {
                $left[] = $items[$i];
            } else {
                $right[] = $items[$i];
            }
        }

        return array_merge(
            self::quickSort($left,  $key, $ascending),
            [$pivot],
            self::quickSort($right, $key, $ascending)
        );
    }

    // ══════════════════════════════════════════════════════════════
    // MERGESORT
    // Aplicación: ordenar datos de reportes (préstamos, multas, etc.)
    // Más estable que QuickSort para conjuntos de datos grandes.
    // ══════════════════════════════════════════════════════════════
    public static function mergeSort(array $items, string $key = 'created_at', bool $ascending = true): array
    {
        $count = count($items);
        if ($count <= 1) return $items;

        $mid   = intdiv($count, 2);
        $left  = self::mergeSort(array_slice($items, 0, $mid),   $key, $ascending);
        $right = self::mergeSort(array_slice($items, $mid),       $key, $ascending);

        return self::merge($left, $right, $key, $ascending);
    }

    private static function merge(array $left, array $right, string $key, bool $ascending): array
    {
        $result = [];
        $i      = 0;
        $j      = 0;

        while ($i < count($left) && $j < count($right)) {
            $leftVal  = is_array($left[$i])  ? $left[$i][$key]  : $left[$i]->$key;
            $rightVal = is_array($right[$j]) ? $right[$j][$key] : $right[$j]->$key;

            $comparison = is_string($leftVal)
                ? strcasecmp($leftVal, $rightVal)
                : ($leftVal <=> $rightVal);

            if ($ascending ? $comparison <= 0 : $comparison > 0) {
                $result[] = $left[$i++];
            } else {
                $result[] = $right[$j++];
            }
        }

        while ($i < count($left))  $result[] = $left[$i++];
        while ($j < count($right)) $result[] = $right[$j++];

        return $result;
    }
}