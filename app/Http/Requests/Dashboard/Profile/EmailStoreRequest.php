<?php

namespace App\Http\Requests\Dashboard\Profile;

use App\Rules\CurrentPassword;
use Illuminate\Foundation\Http\FormRequest;

class EmailStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password_current' => ['required', new CurrentPassword()],
            'email' => 'required|string|email|max:255|confirmed|unique:users,email',
        ];
    }
}
