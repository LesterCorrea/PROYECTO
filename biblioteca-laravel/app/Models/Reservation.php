<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Reservation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'book_id',
        'magazine_id',
        'status',
        'queue_position',
        'reserved_at',
        'expires_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reserved_at' => 'datetime',
            'expires_at'  => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status', 'queue_position']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    // Devuelve el libro O la revista reservada
    public function getItemAttribute()
    {
        return $this->book ?? $this->magazine;
    }

    public function getItemTypeAttribute(): string
    {
        return $this->magazine_id ? 'magazine' : 'book';
    }

    public function loan()
    {
        return $this->hasOne(Loan::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}
