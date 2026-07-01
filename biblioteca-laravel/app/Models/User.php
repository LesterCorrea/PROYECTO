<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'student_id',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'email', 'is_active']);
    }

    // Relaciones
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    // Helpers de roles
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isLibrarian(): bool
    {
        return $this->hasRole('librarian');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    // Multas pendientes
    public function pendingFines()
    {
        return $this->fines()->where('status', 'pending');
    }

    public function hasPendingFines(): bool
    {
        return $this->pendingFines()->exists();
    }
}