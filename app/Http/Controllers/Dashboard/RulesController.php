<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;

class RulesController extends Controller
{
    //
    public function index()
    {
        $rules = Role::withCount('users')->get();

        $totalRoles = $rules->count();

        $sortedRoles = $rules->sortByDesc('users_count')->values();

        $topRole = $sortedRoles->first();

        $secondTopRole = $sortedRoles->skip(1)->first();

        $adminRole = $rules->firstWhere('name', 'admin');

        return view('dashboard.rules.index', compact(
            'rules',
            'totalRoles',
            'topRole',
            'secondTopRole',
            'adminRole'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'guard_name' => 'required|in:web,api',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => $request->guard_name]);
        session()->flash('Add', 'تم إنشاء الدور بنجاح');

        // $role->syncPermissions($request->permissions);

        return redirect()->route('rules.index');
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('dashboard.rules.edit', compact('role', 'permissions', 'rolePermissions'));
    }


    public function analysis($id)
    {
        $role = Role::findOrFail($id);

        $users = $role->users()->paginate(5);

        $usersData = $users->map(function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
                'assigned_at' => $user->created_at
                    ? $user->created_at->locale('ar')->diffForHumans()
                    : '-',
            ];
        });

        return response()->json([
            'role' => $role->name,
            'users' => $usersData,
            'links' => [
                'next' => $users->nextPageUrl(),
                'prev' => $users->previousPageUrl(),
            ],
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
        ]);
    }


    public function updateRole(Request $request)
    {
        $role = Role::findOrFail($request->id);
        $role->update($request->only('name', 'guard_name'));
        session()->flash('edit', 'تم تحديث الصلاحيات بنجاح');
        return redirect()->back();
    }

    // ?todo update role permissions
    public function update(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);
        session()->flash('edit', 'تم تحديث الصلاحيات بنجاح');
        Artisan::call('optimize:clear');

        return redirect()->back();
    }



    public function destroy(Request $request)
    {
        $role = Role::findOrFail($request->id);
        if ($role->name === 'admin') {
            session()->flash('delete', 'لا يمكن حذف دور الادمن');
            return redirect()->route('rules.index');
        }
        // $role->delete();
        session()->flash('delete', 'تم حذف الدور بنجاح');

        return redirect()->route('rules.index');
    }


}
