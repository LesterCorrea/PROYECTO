<?php

namespace App\Services\DataStructures;

class LinkedListNode
{
    public mixed         $data;
    public ?LinkedListNode $next = null;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }
}