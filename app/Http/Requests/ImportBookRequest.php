<?php

namespace App\Http\Requests;

use App\DTO\Books\ImportBookDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportBookRequest extends FormRequest
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
            'external_id' => ['required', 'string', 'max:255'],
            'source' => ['required', 'string', 'in:google,mif'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    public function toDto(): ImportBookDTO
    {
        return new ImportBookDTO(
            userId: $this->user()->id,
            externalId: $this->input('external_id'),
            source: $this->input('source'),
            title: $this->input('title'),
            description: $this->input('description'),
            url: $this->input('url'),
        );
    }
}
