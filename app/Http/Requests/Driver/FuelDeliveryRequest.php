<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class FuelDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isDriver();
    }

    public function rules(): array
    {
        return [
            'driver_latitude'  => ['required', 'numeric', 'between:-90,90'],
            'driver_longitude' => ['required', 'numeric', 'between:-180,180'],
            'driver_address'   => ['nullable', 'string', 'max:255'],
            'fuel_type'        => ['required', 'in:petrol,diesel'],
            'fuel_quantity'    => ['required', 'numeric', 'min:1', 'max:200'],
            'notes'            => ['nullable', 'string', 'max:500'],
            'is_emergency'     => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'driver_latitude.required'   => 'Location latitude is required.',
            'driver_latitude.between'    => 'Invalid latitude value.',
            'driver_longitude.required'  => 'Location longitude is required.',
            'driver_longitude.between'   => 'Invalid longitude value.',
            'fuel_type.required'         => 'Fuel type is required.',
            'fuel_type.in'               => 'Fuel type must be petrol or diesel.',
            'fuel_quantity.required'     => 'Fuel quantity is required.',
            'fuel_quantity.min'          => 'Fuel quantity must be at least 1 liter.',
            'fuel_quantity.max'          => 'Fuel quantity cannot exceed 200 liters.',
            'notes.max'                  => 'Notes cannot exceed 500 characters.',
        ];
    }
}