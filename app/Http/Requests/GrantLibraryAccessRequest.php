<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrantLibraryAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'viewer_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
