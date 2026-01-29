<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display role management page
     */
    public function index(Request $request)
    {
        $query = User::with(['member', 'roleModel']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        // Get all roles from database
        $roles = Role::withCount('users')->with('permissions')->get();
        
        return view('roles.index', compact('users', 'roles'));
    }

    /**
     * Show form to create a new role
     */
    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('label')->get()->groupBy('group');
        
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a new role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|max:20',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.regex' => 'Nama role hanya boleh huruf kecil dan underscore (contoh: kasir_senior)',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'label' => $request->label,
            'description' => $request->description,
            'color' => $request->color,
            'is_system' => false,
        ]);

        $role->permissions()->sync($request->permissions);

        \App\Models\AuditLog::log('create', "Membuat role baru: {$role->label}");

        return redirect()->route('roles.index')->with('success', "Role '{$role->label}' berhasil dibuat!");
    }

    /**
     * Show form to edit a role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('label')->get()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update a role
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|max:20',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Don't allow editing system role name
        $updateData = [
            'label' => $request->label,
            'description' => $request->description,
            'color' => $request->color,
        ];

        if (!$role->is_system) {
            $request->validate([
                'name' => 'required|string|max:50|unique:roles,name,' . $role->id . '|regex:/^[a-z_]+$/',
            ]);
            $updateData['name'] = $request->name;
        }

        $role->update($updateData);
        $role->permissions()->sync($request->permissions);

        \App\Models\AuditLog::log('update', "Mengupdate role: {$role->label}");

        return redirect()->route('roles.index')->with('success', "Role '{$role->label}' berhasil diupdate!");
    }

    /**
     * Delete a role
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'Role sistem tidak dapat dihapus!');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Role '{$role->label}' masih digunakan oleh " . $role->users()->count() . " user!");
        }

        $label = $role->label;
        $role->delete();

        \App\Models\AuditLog::log('delete', "Menghapus role: {$label}");

        return redirect()->route('roles.index')->with('success', "Role '{$label}' berhasil dihapus!");
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $role = Role::find($request->role_id);

        $user->update([
            'role' => $role->name,
            'role_id' => $role->id,
        ]);

        \App\Models\AuditLog::log(
            'update', 
            "Mengubah role user {$user->name} menjadi {$role->label}"
        );

        return redirect()->back()->with('success', "Role user berhasil diubah menjadi {$role->label}");
    }

    /**
     * Get role permissions (for AJAX)
     */
    public function getPermissions(Role $role)
    {
        return response()->json([
            'role' => $role,
            'permissions' => $role->permissions->pluck('label')->toArray()
        ]);
    }
}
