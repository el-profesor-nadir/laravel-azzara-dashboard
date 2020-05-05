<?php

namespace App\Http\Controllers\Dashboard\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\Dashboard\Profile\PasswordUpdated;
use App\Http\Requests\Dashboard\Profile\PasswordStoreRequest;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:View password', ['only' => ['index']]);
        $this->middleware('permission:Edit password', ['only' => ['store']]);
    }

    public function index()
    {
        return view('dashboard.password.index');
    }

    public function store(PasswordStoreRequest $request)
    {
        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        Mail::to($request->user())->send(new PasswordUpdated());

        return redirect()->back()->withSuccess(__('Password successfully updated.'));
    }
}
