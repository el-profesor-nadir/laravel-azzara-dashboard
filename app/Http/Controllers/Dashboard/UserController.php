<?php

namespace App\Http\Controllers\Dashboard;

use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct() {

        $this->middleware('permission:View users', ['only' => ['index']]);
        $this->middleware('permission:Add users', ['only' => ['create','store']]);
        $this->middleware('permission:Edit users', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete users', ['only' => ['destroy']]);

    }

    public function index()
    {
        $users = User::with('roles')->latest()->paginate(10);
        return view('dashboard.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::get();

        return view('dashboard.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' =>  now()
        ]);

        $role = Role::whereId($request->role_id)->firstOrFail();
        $user->assignRole($role);

        if (isset($request->need_verification)) {
            $user->update(['email_verified_at' =>  null]);
            $user->sendEmailVerificationNotification();
        }

        return redirect()
                ->route('dashboard.users.index')
                ->withSuccess(__('User successfully added.'));
    }

    public function edit(User $user)
    {
        $roles = Role::get();
        return view('dashboard.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => $request->password != null ? 'sometimes|required|string|min:6|confirmed' : '',
            'role_id' => 'required',
        ]);

        $input = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password != null ? Hash::make($request->password) : $user->password,
        ];

        $user->fill($input)->save();

        $user->roles()->detach();
        $role = Role::whereId($request->role_id)->firstOrFail();
        $user->assignRole($role);


        if(isset($request->need_verification)){
            $user->update(['email_verified_at' => null]);
            $user->sendEmailVerificationNotification();
        }

        return redirect()
                ->route('dashboard.users.index')
                ->withSuccess(__('User successfully updated.'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
                ->route('dashboard.users.index')
                ->withSuccess(__('User successfully deleted.'));
    }
}
