<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'title'          => 'required|string|max:255',
            'author_id'      => 'required|exists:authors,id',
            'published_date' => 'nullable|date',
            'description'    => 'nullable|string',
            'cover_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'pdf'            => 'required|file|mimes:pdf|max:51200',
            'pages'          => 'nullable|integer|min:1',
            'category_id'    => 'nullable|exists:categories,id',
        ];
    }
}
