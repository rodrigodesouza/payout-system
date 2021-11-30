<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    CONST MIN_NAME_SIZE = 2;
    CONST MAX_NAME_SIZE = 255;
    CONST MAX_EMAIL_SIZE = 255;
    CONST MIN_PASSWORD_SIZE = 8;
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
            'name' => 'required|min:' . self::MIN_NAME_SIZE . '|max:' . self::MAX_NAME_SIZE,
            'email' => 'required|string|email|max:' . self::MAX_EMAIL_SIZE . '|unique:users',
            'password' => 'required|string|min:' . self::MIN_PASSWORD_SIZE,
        ];
    }
}
