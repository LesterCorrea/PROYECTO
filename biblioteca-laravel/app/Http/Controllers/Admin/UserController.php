<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DataStructures\SearchAlgorithms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query  = $request->get('q');
        $role   = $request->get('rol');
        $sortBy = $request->get('ordenar', 'name');
        $sortDir = $request->get('direccion', 'asc');

        $users = User::with('roles')
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%"))
            ->when($role, fn($q) => $q->whereHas(
                'roles',
                fn($q) => $q->where('name', $role)
            ))
            ->get()
            ->toArray();

        // Ordenar con MergeSort
        $allowed = ['name', 'email', 'created_at'];
        $sortKey = in_array($sortBy, $allowed) ? $sortBy : 'name';
        $users   = SearchAlgorithms::mergeSort($users, $sortKey, $sortDir === 'asc');

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles', 'query', 'role'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
            'role'       => 'required|exists:roles,name',
            'student_id' => 'nullable|string|unique:users,student_id',
            'phone'      => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'student_id'        => $request->student_id,
            'phone'             => $request->phone,
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $usuario)
    {
        $usuario->load(['roles', 'loans.book', 'fines', 'reservations.book']);
        return view('admin.users.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $usuario->id,
            'password'   => 'nullable|string|min:8|confirmed',
            'student_id' => 'nullable|string|unique:users,student_id,' . $usuario->id,
            'phone'      => 'nullable|string|max:20',
        ]);

        $usuario->update([
            'name'       => $request->name,
            'email'      => $request->email,
            'student_id' => $request->student_id,
            'phone'      => $request->phone,
            'password'   => $request->password
                ? Hash::make($request->password)
                : $usuario->password,
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete(); // Soft delete

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleActive(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['is_active' => !$usuario->is_active]);

        $status = $usuario->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Usuario {$status} correctamente.");
    }

    public function changeRole(Request $request, User $usuario)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $usuario->syncRoles([$request->role]);

        return back()->with('success', 'Rol actualizado correctamente.');
    }
}
