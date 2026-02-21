<?php

namespace App\Http\Requests;

use App\DTO\Books\CreateBookDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
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
            'content' => ['nullable', 'string', 'required_without:file'],
            'file' => ['nullable', 'file', 'mimes:txt', 'required_without:content'],
        ];
    }

    public function toDto(): CreateBookDTO
    {
        return new CreateBookDTO(
            userId: $this->user()->id,
            title: $this->input('title'),
            content: $this->getContentFromInputOrFile(),
        );
    }

    private function getContentFromInputOrFile(): string
    {
        if ($this->hasFile('file')) {
            return file_get_contents($this->file('file')->getRealPath());
        }

        return $this->input('content', '');
    }
}
