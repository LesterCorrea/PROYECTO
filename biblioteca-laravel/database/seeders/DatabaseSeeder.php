<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Primero roles y permisos
        $this->call(RolesAndPermissionsSeeder::class);

        // Categorías base
        $this->call(CategorySeeder::class);

        // ── Usuario Administrador ───────────────────────────────────
        $admin = User::create([
            'name'              => 'Administrador',
            'email'             => 'admin@biblioteca.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $admin->assignRole('admin');

        // ── Usuario Bibliotecario ───────────────────────────────────
        $librarian = User::create([
            'name'              => 'Bibliotecario',
            'email'             => 'librarian@biblioteca.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $librarian->assignRole('librarian');

        // ── Usuario Estudiante ──────────────────────────────────────
        $student = User::create([
            'name'              => 'Estudiante Demo',
            'email'             => 'student@biblioteca.com',
            'student_id'        => 'EST-2024-001',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $student->assignRole('student');
    }
}