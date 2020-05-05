<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Avatar extends Model
{
    protected $fillable = ['path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function path()
    {

        $dir =config('image.avatar.dir');
        $disk =config('image.avatar.disk');

        $exists = Storage::disk($disk)->exists($dir.'/'.$this->path);

        return $exists ? Storage::disk($disk)->url($dir.'/'.$this->path) : config('image.image_no_available_url');
    }
}
