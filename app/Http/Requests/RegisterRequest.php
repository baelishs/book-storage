<?php

namespace App\Http\Requests;

use App\DTO\AuthDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:255', 'unique:users,login'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function toDto(): AuthDTO
    {
        return new AuthDTO(
            login: $this->input('login'),
            password: $this->input('password')
        );
    }
}
