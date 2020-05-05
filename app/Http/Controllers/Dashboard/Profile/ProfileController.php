<?php

namespace App\Http\Controllers\Dashboard\Profile;

use Image;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Dashboard\Profile\ProfileStoreRequest;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:View profile', ['only' => ['index']]);
        $this->middleware('permission:Edit profile', ['only' => ['store']]);
    }

    public function index()
    {
        return view('dashboard.profile.index');
    }

    public function store(ProfileStoreRequest $request)
    {

        $data = $request->only(
            'name',
            'first_name',
            'last_name',
            'phone',
            'gender',
            'address',
            'country',
            'city',
            'zip_code'
        );

        $request->user()->fill($data);

        if(!$request->user()->isDirty() && !$request->has('avatar'))
            return back()->withError(__('You should change something'));

        $request->user()->save();

        if($request->has('avatar')){

            if($request->user()->avatar === null){
                $this->addAvatarToProfile($request);
            }else{
                $this->updateAvatarToProfile($request);
            }

        }

        return back()->withSuccess(__('Account profile updated.'));
    }

    private function addAvatarToProfile(ProfileStoreRequest $request)
    {
        $path = $request->file('avatar')->storeAs(
            config('image.avatar.dir'), $r = uniqid(true).'.jpg', config('image.avatar.disk')
        );

        $request->user()->avatar()->create([
            'path'=>$r,
        ]);

        $image = Image::make(public_path('storage/'.$path))->resize(128,128);
        $image->save();

    }

    private function updateAvatarToProfile(ProfileStoreRequest $request)
    {
        Storage::disk(config('image.avatar.disk'))->delete(config('image.avatar.dir').'/'.$request->user()->avatar->path);

        $path = $request->file('avatar')->storeAs(
            config('image.avatar.dir'), $r = uniqid(true).'.jpg', config('image.avatar.disk')
        );

        $request->user()->avatar()->update([
            'path'=>$r,
        ]);

        $image = Image::make(public_path('storage/'.$path))->resize(128,128);
        $image->save();

    }

}
