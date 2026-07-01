<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionItem extends Model
{
    protected $fillable = [
        'collection_id',
        'book_id',
        'order',
        'previous_item_id',
        'next_item_id',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function previousItem()
    {
        return $this->belongsTo(CollectionItem::class, 'previous_item_id');
    }

    public function nextItem()
    {
        return $this->belongsTo(CollectionItem::class, 'next_item_id');
    }
}