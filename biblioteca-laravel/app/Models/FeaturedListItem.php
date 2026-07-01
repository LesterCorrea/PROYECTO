<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedListItem extends Model
{
    protected $fillable = [
        'featured_list_id',
        'itemable_id',
        'itemable_type',
        'order',
        'previous_item_id',
        'next_item_id',
    ];

    public function featuredList()
    {
        return $this->belongsTo(FeaturedList::class);
    }

    // Morph → puede ser Book, Magazine, Author o Collection
    public function itemable()
    {
        return $this->morphTo();
    }

    // Lista circular doblemente enlazada
    public function previousItem()
    {
        return $this->belongsTo(FeaturedListItem::class, 'previous_item_id');
    }

    public function nextItem()
    {
        return $this->belongsTo(FeaturedListItem::class, 'next_item_id');
    }
}