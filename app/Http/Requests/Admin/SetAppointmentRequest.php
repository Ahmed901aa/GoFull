<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SetAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() &&
               in_array(auth()->user()->role, ['admin', 'employee']);
    }

    public function rules(): array
    {
        return [
            'appointment_date'  => ['required', 'date', 'after:now'],
            'appointment_notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_date.required' => 'Appointment date is required.',
            'appointment_date.date'     => 'Appointment date must be a valid date.',
            'appointment_date.after'    => 'Appointment date must be in the future.',
            'appointment_notes.max'     => 'Notes cannot exceed 500 characters.',
        ];
    }
}