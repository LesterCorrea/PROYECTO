<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permisos de libros ──────────────────────────────────────
        Permission::create(['name' => 'view books']);
        Permission::create(['name' => 'create books']);
        Permission::create(['name' => 'edit books']);
        Permission::create(['name' => 'delete books']);
        Permission::create(['name' => 'read pdf']);

        // ── Permisos de revistas ────────────────────────────────────
        Permission::create(['name' => 'view magazines']);
        Permission::create(['name' => 'create magazines']);
        Permission::create(['name' => 'edit magazines']);
        Permission::create(['name' => 'delete magazines']);

        // ── Permisos de autores ─────────────────────────────────────
        Permission::create(['name' => 'view authors']);
        Permission::create(['name' => 'create authors']);
        Permission::create(['name' => 'edit authors']);
        Permission::create(['name' => 'delete authors']);

        // ── Permisos de colecciones ─────────────────────────────────
        Permission::create(['name' => 'view collections']);
        Permission::create(['name' => 'create collections']);
        Permission::create(['name' => 'edit collections']);
        Permission::create(['name' => 'delete collections']);

        // ── Permisos de reservas ────────────────────────────────────
        Permission::create(['name' => 'create reservations']);
        Permission::create(['name' => 'view reservations']);
        Permission::create(['name' => 'manage reservations']);

        // ── Permisos de préstamos ───────────────────────────────────
        Permission::create(['name' => 'view loans']);
        Permission::create(['name' => 'manage loans']);

        // ── Permisos de multas ──────────────────────────────────────
        Permission::create(['name' => 'view fines']);
        Permission::create(['name' => 'manage fines']);

        // ── Permisos de carruseles ──────────────────────────────────
        Permission::create(['name' => 'manage featured lists']);

        // ── Permisos de usuarios ────────────────────────────────────
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);

        // ── Permisos de reportes ────────────────────────────────────
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'generate reports']);

        // ── Permisos de categorías ──────────────────────────────────
        Permission::create(['name' => 'manage categories']);

        // ── Permisos de editoriales ─────────────────────────────────
        Permission::create(['name' => 'manage publishers']);

        // ── Permisos de logs ────────────────────────────────────────
        Permission::create(['name' => 'view logs']);

        // ── Permisos de comentarios ─────────────────────────────────
        Permission::create(['name' => 'manage comments']);

        // ────────────────────────────────────────────────────────────
        // ROL: student
        // ────────────────────────────────────────────────────────────
        $student = Role::create(['name' => 'student']);
        $student->givePermissionTo([
            'view books',
            'view magazines',
            'view authors',
            'view collections',
            'read pdf',
            'create reservations',
            'view reservations',
            'view fines',
        ]);

        // ────────────────────────────────────────────────────────────
        // ROL: librarian
        // ────────────────────────────────────────────────────────────
        $librarian = Role::create(['name' => 'librarian']);
        $librarian->givePermissionTo([
            'view books',
            'create books',
            'edit books',
            'delete books',
            'read pdf',
            'view magazines',
            'create magazines',
            'edit magazines',
            'delete magazines',
            'view authors',
            'create authors',
            'edit authors',
            'delete authors',
            'view collections',
            'create collections',
            'edit collections',
            'delete collections',
            'view reservations',
            'manage reservations',
            'create reservations',
            'view loans',
            'manage loans',
            'view fines',
            'manage fines',
            'manage featured lists',
            'manage categories',
            'manage publishers',
            'manage comments',
        ]);

        // ────────────────────────────────────────────────────────────
        // ROL: admin
        // ────────────────────────────────────────────────────────────
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
    }
}