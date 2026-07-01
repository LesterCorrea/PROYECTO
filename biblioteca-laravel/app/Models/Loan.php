<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Loan extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'book_id',
        'magazine_id',
        'reservation_id',
        'librarian_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'previous_loan_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'loan_date'   => 'date',
            'due_date'    => 'date',
            'return_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status', 'return_date']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function librarian()
    {
        return $this->belongsTo(User::class, 'librarian_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function fine()
    {
        return $this->hasOne(Fine::class);
    }

    // Lista enlazada simple → préstamo anterior del usuario
    public function previousLoan()
    {
        return $this->belongsTo(Loan::class, 'previous_loan_id');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->due_date->isPast();
    }

    public function overdueDays(): int
    {
        if (!$this->isOverdue()) return 0;
        return now()->diffInDays($this->due_date);
    }

    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    // Helper: devuelve el ítem prestado (libro o revista)
    public function getItemAttribute()
    {
        return $this->book ?? $this->magazine;
    }

    public function getItemTitleAttribute(): string
    {
        return $this->book?->title ?? $this->magazine?->title ?? '—';
    }
}
