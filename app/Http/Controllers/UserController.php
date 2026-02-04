<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $branchs = Branch::all();

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
                    'show' => route('management.users.show', $user->id),
                    'edit' => route('management.users.edit', $user->id),
                    'delete' => route('management.users.destroy', $user->id),
                ]
            ];
        })->toArray();

        return view('pages.user.index', compact(
            'users',
            'usersData',
            'columns',
            'branchs'
        ));
    }

    public function create()
    {
        $roles = Role::all();
        $branchs = Branch::active()->get();

        return view('pages.user.create', compact('roles', 'branchs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'roles'    => 'required|array',
            'branches' => 'required|array|min:1',
            'default_branch_id' => 'required|integer',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'current_branch_id' => $request->default_branch_id,
        ]);

        // 🔐 Assign roles
        $user->syncRoles($request->roles);

        // 🔗 Attach branches
        $pivotData = [];
        foreach ($request->branches as $branchId) {
            $pivotData[$branchId] = [
                'is_default' => $branchId == $request->default_branch_id,
            ];
        }

        $user->branches()->sync($pivotData);

        return redirect()
            ->route('management.users.index')
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
        $branchs = Branch::active()->get();

        $selectedRoles = $user->roles->pluck('name')->toArray();

        return view('pages.user.edit', compact(
            'user',
            'roles',
            'branchs',
            'selectedRoles'
        ));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'roles'    => 'required|array',
            'branches' => 'required|array|min:1',
            'default_branch_id' => 'required|integer',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'current_branch_id' => $request->default_branch_id,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Roles
        $user->syncRoles($request->roles);

        // Branches
        $pivotData = [];
        foreach ($request->branches as $branchId) {
            $pivotData[$branchId] = [
                'is_default' => $branchId == $request->default_branch_id,
            ];
        }

        $user->branches()->sync($pivotData);

        return redirect()
            ->route('management.users.index')
            ->with('success', 'User berhasil diperbarui');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('management.users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
