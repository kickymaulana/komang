<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::query()
                ->with('roles')
                ->orderBy('id')
                ->get(),
            'roles' => Role::query()->orderBy('name')->pluck('name'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->assignRole($validated['role']);

        return back()->with('flash', [
            'success' => 'User "'.$user->name.'" berhasil ditambahkan.',
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'Tidak dapat menghapus akun sendiri.');

        $user->delete();

        return back()->with('flash', [
            'success' => 'User "'.$user->name.'" dihapus.',
        ]);
    }
}
