<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Fine extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'loan_id',
        'user_id',
        'overdue_days',
        'amount_per_day',
        'total_amount',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at'        => 'datetime',
            'total_amount'   => 'decimal:2',
            'amount_per_day' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status', 'total_amount']);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}