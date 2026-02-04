<?php


namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')
            ->latest()
            ->paginate(10);

        $columns = [
            [
                'key' => 'roleName',
                'label' => 'Role Name',
                'type' => 'text',
            ],
            [
                'key' => 'guard_name',
                'label' => 'Guard Name',
                'type' => 'tag',
            ],

        ];

        $rolesData = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'roleName' => $role->name,
                'guard_name' => $role->guard_name ?? 'web',
                'actions' => [
                    'show' => route('management.roles.show', $role->id),
                    'edit' => route('management.roles.edit', $role->id),
                    'delete' => route('management.roles.destroy', $role->id),
                ]
            ];
        })->toArray();

        return view('pages.roles.index', [
            'roles' => $roles,
            'rolesData' => $rolesData,
            'columns' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.roles.create', [
            'permissions' => Permission::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'required|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'],
        ]);

        $role->syncPermissions($validated['permissions']);

        return redirect()
            ->route('management.roles.index')
            ->with('success', 'Role berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions');

        return view('pages.roles.show', [
            'role' => $role,
            'permissions' => $role->permissions,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('pages.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'guard_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('management.roles.index')
            ->with('success', 'Role berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()
            ->route('management.roles.index')
            ->with('success', 'Role berhasil dihapus');
    }
}
