<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class TowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isDriver();
    }

    public function rules(): array
    {
        return [
            'driver_latitude'        => ['required', 'numeric', 'between:-90,90'],
            'driver_longitude'       => ['required', 'numeric', 'between:-180,180'],
            'driver_address'         => ['nullable', 'string', 'max:255'],
            'destination_latitude'   => ['nullable', 'numeric', 'between:-90,90'],
            'destination_longitude'  => ['nullable', 'numeric', 'between:-180,180'],
            'destination_address'    => ['nullable', 'string', 'max:255'],
            'plate_number'           => ['required', 'string', 'max:20'],
            'notes'                  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'driver_latitude.required'  => 'Location latitude is required.',
            'driver_latitude.between'   => 'Invalid latitude value.',
            'driver_longitude.required' => 'Location longitude is required.',
            'driver_longitude.between'  => 'Invalid longitude value.',
            'plate_number.required'     => 'Vehicle plate number is required.',
            'plate_number.max'          => 'Plate number cannot exceed 20 characters.',
            'notes.max'                 => 'Notes cannot exceed 500 characters.',
        ];
    }
}