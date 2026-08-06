<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'     => ['required', 'string', 'max:100'],
            'phone'    => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:driver,provider'],
            'otp_code' => ['required', 'digits:6'],
        ];

        if ($this->input('role') === 'provider') {
            $rules = array_merge($rules, [
                'service_type'  => ['required', 'in:fuel_delivery,towing'],
                'vehicle_make'  => ['required', 'string', 'max:100'],
                'vehicle_model' => ['required', 'string', 'max:100'],
                'vehicle_year'  => ['required', 'integer', 'min:1990', 'max:' . date('Y')],
                'vehicle_plate' => ['required', 'string', 'max:20', 'unique:provider_profiles,vehicle_plate'],
                'vehicle_color' => ['nullable', 'string', 'max:50'],

                'documents'              => ['required', 'array', 'min:1'],
                'documents.*.type'       => ['required', 'in:national_id,driving_license,vehicle_license,vehicle_photo,insurance'],
                'documents.*.file'       => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Name is required.',
            'otp_code.required'         => 'The SMS verification code is required.',
            'otp_code.digits'           => 'The verification code must be 6 digits.',
            'phone.required'            => 'Phone number is required.',
            'phone.unique'              => 'This phone number is already registered.',
            'password.required'         => 'Password is required.',
            'password.min'              => 'Password must be at least 8 characters.',
            'password.confirmed'        => 'Password confirmation does not match.',
            'role.required'             => 'Role is required.',
            'role.in'                   => 'Role must be either driver or provider.',
            'service_type.required'     => 'Service type is required for providers.',
            'service_type.in'           => 'Service type must be fuel_delivery or towing.',
            'vehicle_make.required'     => 'Vehicle make is required.',
            'vehicle_model.required'    => 'Vehicle model is required.',
            'vehicle_year.required'     => 'Vehicle year is required.',
            'vehicle_year.min'          => 'Vehicle year must be 1990 or later.',
            'vehicle_plate.required'    => 'Vehicle plate number is required.',
            'vehicle_plate.unique'      => 'This plate number is already registered.',
            'documents.required'        => 'At least one document is required.',
            'documents.*.type.required' => 'Each document must have a type.',
            'documents.*.type.in'       => 'Invalid document type.',
            'documents.*.file.required' => 'Each document must have a file.',
            'documents.*.file.mimes'    => 'Documents must be jpg, jpeg, png, or pdf.',
            'documents.*.file.max'      => 'Each document must not exceed 5MB.',
        ];
    }
}