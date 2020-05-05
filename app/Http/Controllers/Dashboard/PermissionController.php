<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:View permissions', ['only' => ['index','show']]);
        $this->middleware('permission:Add permissions', ['only' => ['create','store']]);
        $this->middleware('permission:Edit permissions', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete permissions', ['only' => ['destroy']]);
    }

    public function index()
    {
        $permissions = Permission::with('roles')->latest()->paginate(10);
        return view('dashboard.permissions.index', compact('permissions'));
    }

    public function create()
    {

        $roles = Role::get();

        return view('dashboard.permissions.create', compact('roles'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name'=>'required|unique:permissions,name|max:60',
            'roles' => 'required|min:1'
        ]);

        $permission = new Permission([ 'name' => $request->name ]);
        $permission->save();

        $roles = $request['roles'];

        if (!empty($request['roles'])) {
            foreach ($roles as $role) {
                $r = Role::where('id', '=', $role)->firstOrFail();
                $r->givePermissionTo($permission);
            }
        }

        return redirect()
                ->route('dashboard.permissions.index')
                ->withSuccess(__('Permission successfully added.'));

    }

    public function show($id)
    {
        return redirect()->route('admin.permissions.index');
    }

    public function edit(Permission $permission)
    {
        $roles = Role::all();
        return view('dashboard.permissions.edit', compact('permission','roles'));
    }

    public function update(Request $request, Permission $permission)
    {

        $this->validate($request, [
            'name'=>'required|unique:permissions,name,'.$permission->id.'|max:60',
            'roles' => 'required'
        ]);

        $permission->fill([ 'name' => $request->name])->save();

        $roles = $request['roles'];

        $allRoles = Role::all();

        foreach ($allRoles as $role) {
            $permission->removeRole($role);
        }

        foreach ($roles as $role) {
            $r = Role::where('id', '=', $role)->firstOrFail();
            $permission->assignRole($r);
        }

        return redirect()
                ->route('dashboard.permissions.index')
                ->withSuccess(__('Permission successfully updated.'));

    }

    public function destroy(Permission $permission)
    {

        //Make it impossible to delete this specific permission
        if ($permission->name == "Administer roles & permissions") {
            return redirect()
                    ->route('dashboard.permissions.index')
                    ->withError('Cannot delete this Permission!');
        }

        $permission->delete();

        return redirect()
                ->route('dashboard.permissions.index')
                ->withSuccess(__('Permission successfully deleted.'));

    }
}
