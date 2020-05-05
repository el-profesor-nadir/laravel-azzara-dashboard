<?php

namespace App\Http\Controllers\Dashboard;

use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:View roles', ['only' => ['index','show']]);
        $this->middleware('permission:Add roles', ['only' => ['create','store']]);
        $this->middleware('permission:Edit roles', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete roles', ['only' => ['destroy']]);
    }

    public function index()
    {
        $roles = Role::with('permissions')->latest()->paginate(10);
        return view('dashboard.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('dashboard.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'name'=>'required|unique:roles,name|max:60',
                'permissions' =>'required',
            ]
        );

        $role = new Role([ 'name' => $request->name]);
        $role->save();

        $permissions = $request['permissions'];

        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            $role->givePermissionTo($p);
        }

        return redirect()
            ->route('dashboard.roles.index')
            ->withSuccess(__('Role successfully added.'));
    }

    public function show($id)
    {
        return redirect()->route('dashboard.roles.index');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('dashboard.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->validate($request,
            [
                'name'=>'required|max:60|unique:roles,name,'.$role->id,
                'permissions' =>'required',
            ]
        );

        $role->fill([ 'name' => $request->name])->save();

        $permissions = $request['permissions'];

        $allPermissions = Permission::all();

        foreach ($allPermissions as $permission) {
            $role->revokePermissionTo($permission);
        }

        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            $role->givePermissionTo($p);
        }

        return redirect()
                ->route('dashboard.roles.index')
                ->withSuccess(__('Role successfully updated.'));
    }

    public function destroy(Role $role)
    {
         //Make it impossible to delete this specific role
         if ($role->name == "super-admin" || $role->name == "admin" || $role->name == "client") {
            return redirect()
                    ->route('dashboard.roles.index')
                    ->withError('Cannot delete this role!');
        }

        $role->delete();

        return redirect()
                ->route('dashboard.roles.index')
                ->withSuccess(__('Role successfully deleted.'));

    }
}
