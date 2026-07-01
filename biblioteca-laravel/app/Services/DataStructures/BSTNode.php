<?php

namespace App\Services\DataStructures;

class BSTNode
{
    public int $key;
    public mixed $data;
    public ?BSTNode $left  = null;
    public ?BSTNode $right = null;

    public function __construct(int $key, mixed $data)
    {
        $this->key  = $key;
        $this->data = $data;
    }
}