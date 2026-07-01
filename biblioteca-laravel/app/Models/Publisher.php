<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'country',
        'website',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function magazines()
    {
        return $this->hasMany(Magazine::class);
    }
}