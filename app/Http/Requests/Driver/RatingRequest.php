<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isDriver();
    }

    public function rules(): array
    {
        return [
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Rating is required.',
            'rating.integer'  => 'Rating must be a whole number.',
            'rating.min'      => 'Rating must be at least 1.',
            'rating.max'      => 'Rating cannot exceed 5.',
            'comment.max'     => 'Comment cannot exceed 1000 characters.',
        ];
    }
}