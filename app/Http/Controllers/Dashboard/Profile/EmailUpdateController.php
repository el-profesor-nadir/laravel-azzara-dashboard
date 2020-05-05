<?php

namespace App\Http\Controllers\Dashboard\Profile;

use Auth;
use Illuminate\Http\Request;
use App\Mail\Dashboard\Profile\EmailUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Dashboard\Profile\EmailStoreRequest;

class EmailUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:View email', ['only' => ['index']]);
        $this->middleware('permission:Edit email', ['only' => ['store']]);
    }

    public function index()
    {
        return view('dashboard.email.index');
    }

    public function store(EmailStoreRequest $request)
    {
        $oldEmail = $request->user()->email;

        $request->user()->update([
            'email' => $request->email,
            'email_verified_at' => null,
        ]);

        $request->user()->sendEmailVerificationNotification();

        Mail::to($oldEmail)->send(new EmailUpdated());

        Auth::logout();

        return redirect()->route('login')
                    ->withSuccess(__('Email successfully updated. Please check your new email for an activation link.'));
    }
}
