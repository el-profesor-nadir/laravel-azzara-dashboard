<?php

namespace App\Http\Requests\Dashboard\Profile;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileStoreRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:male,female',
            'address' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'zip_code' => 'required|string',
            'phone' => 'required|string',
            'avatar' => 'image|max:1024|dimensions:min_width=100,min_height=100',
        ];

        return $rules;
    }
}
