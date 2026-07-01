<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'viewable_id',
        'viewable_type',
        'user_id',
        'ip_address',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    public function viewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}