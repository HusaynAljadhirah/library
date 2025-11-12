<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filter.name' => 'sometimes|string|max:255',
            'filter.status' => 'sometimes|boolean',
            'sort' => 'sometimes|string|in:name,created_at,-name,-created_at',
            'page' => 'sometimes|integer|min:1',
        ];
    }
}
