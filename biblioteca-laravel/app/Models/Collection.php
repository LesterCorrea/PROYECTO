<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Collection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image',
        'views',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($collection) {
            $collection->slug = Str::slug($collection->name);
        });
    }

    public function books()
    {
        return $this->hasMany(Book::class)->orderBy('saga_order');
    }

    // Nodo inicial de la lista doblemente enlazada
    public function firstBook()
    {
        return $this->hasOne(Book::class)->whereNull('previous_book_id')->orderBy('saga_order');
    }
}