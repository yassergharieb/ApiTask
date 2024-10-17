<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use function Laravel\Prompts\password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name"         =>  ["required", "string" ,  "min:10" , "max:30"],
            "phone_number" =>  ['required','unique:users,phone_number', 'regex:/^01[0-2,5][0-9]{7}$/'],
            "password"     =>  [Password::default()->letters()->mixedCase()->symbols()]

        ];
    }
}
