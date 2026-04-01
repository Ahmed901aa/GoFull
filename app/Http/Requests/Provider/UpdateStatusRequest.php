<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isProvider();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:en_route,arrived,in_progress,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be one of: en_route, arrived, in_progress, completed.',
        ];
    }

    /**
     * Validate status chain — cannot skip steps
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $request = $this->route('request');

            if (! $request) return;

            $chain = [
                'accepted'    => 'en_route',
                'en_route'    => 'arrived',
                'arrived'     => 'in_progress',
                'in_progress' => 'completed',
            ];

            $expectedNext = $chain[$request->status] ?? null;

            if ($expectedNext !== $this->input('status')) {
                $validator->errors()->add(
                    'status',
                    "Cannot change status from '{$request->status}' to '{$this->input('status')}'. Expected: '{$expectedNext}'."
                );
            }
        });
    }
}