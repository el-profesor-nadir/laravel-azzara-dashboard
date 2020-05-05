<?php

namespace App\Http\Controllers\Dashboard;

use Image;
use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Dashboard\Profile\ProfileStoreRequest;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:View users profile', ['only' => ['edit']]);
        $this->middleware('permission:Edit users profile', ['only' => ['update']]);
    }

    public function edit(User $user)
    {
        return view('dashboard.users.profile.edit', compact('user'));
    }

    public function update(ProfileStoreRequest $request, User $user)
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

        $user->fill($data);

        if(!$user->isDirty() && !$request->has('avatar'))
            return back()->withError(__('You should change something'));

        $user->save();

        if($request->has('avatar')){

            if($user->avatar === null){
                $this->addAvatarToProfile($request, $user);
            }else{
                $this->updateAvatarToProfile($request, $user);
            }

        }

        return  back()->withSuccess(__('User profile successfully updated.'));
    }

    private function addAvatarToProfile(ProfileStoreRequest $request, User $user)
    {
        $path = $request->file('avatar')->storeAs(
            config('image.avatar.dir'), $r = uniqid(true).'.jpg', config('image.avatar.disk')
        );

        $user->avatar()->create([
            'path'=>$r,
        ]);

        $image = Image::make(public_path('storage/'.$path))->resize(128,128);
        $image->save();

    }

    private function updateAvatarToProfile(ProfileStoreRequest $request, User $user)
    {
        Storage::disk(config('image.avatar.disk'))->delete(config('image.avatar.dir').'/'.$user->avatar->path);

        $path = $request->file('avatar')->storeAs(
            config('image.avatar.dir'), $r = uniqid(true).'.jpg', config('image.avatar.disk')
        );

        $user->avatar()->update([
            'path'=>$r,
        ]);

        $image = Image::make(public_path('storage/'.$path))->resize(128,128);
        $image->save();

    }

}
