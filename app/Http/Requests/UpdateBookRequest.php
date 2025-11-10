<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('librarian');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'          => 'nullable|string|max:255',
            'published_date' => 'nullable|date',
            'description'    => 'nullable|string',
            'cover_image'    => 'nullable|image|max:2048',
            'author_id'      => 'nullable|exists:authors,id',
        ];
    }
}
