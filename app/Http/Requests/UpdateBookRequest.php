<?php

namespace App\Http\Requests;

use App\DTO\Books\UpdateBookDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }

    public function toDto(): UpdateBookDTO
    {
        return new UpdateBookDTO(
            id: $this->route('book'),
            userId: $this->user()->id,
            title: $this->input('title'),
            content: $this->input('content'),
        );
    }
}
