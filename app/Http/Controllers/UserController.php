<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->latest()
            ->paginate(10);

        $columns = [
            [
                'key' => 'name',
                'label' => 'Nama',
                'type' => 'text',
            ],
            [
                'key' => 'email',
                'label' => 'Email',
                'type' => 'text',
            ],
            [
                'key' => 'roles',
                'label' => 'Role',
                'type' => 'tag',
            ],
        ];

        $usersData = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(),
                'actions' => [
                    'show' => route('users.show', $user->id),
                    'edit' => route('users.edit', $user->id),
                    'delete' => route('users.destroy', $user->id),
                ]
            ];
        })->toArray();

        return view('pages.user.index', compact(
            'users',
            'usersData',
            'columns'
        ));
    }

    public function create()
    {
        $roles = Role::all();
        return view('pages.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'roles'    => 'required|array'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->roles);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('pages.user.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $selectedRoles = $user->roles->pluck('name')->toArray();

        return view('pages.user.edit', compact(
            'user',
            'roles',
            'selectedRoles'
        ));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'password' => 'nullable|min:6|confirmed'
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->syncRoles($request->roles);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
