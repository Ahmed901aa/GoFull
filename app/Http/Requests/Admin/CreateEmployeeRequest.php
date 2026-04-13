<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:100'],
            'phone'         => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'employee_type' => ['required', 'string', 'in:fuel,towing'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'اسم الموظف مطلوب.',
            'phone.required'         => 'رقم الجوال مطلوب.',
            'phone.unique'           => 'رقم الجوال مسجل مسبقاً.',
            'password.required'      => 'كلمة المرور مطلوبة.',
            'password.min'           => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed'     => 'تأكيد كلمة المرور غير متطابق.',
            'employee_type.required' => 'نوع الموظف مطلوب.',
            'employee_type.in'       => 'نوع الموظف يجب أن يكون وقود أو سحب.',
        ];
    }
}