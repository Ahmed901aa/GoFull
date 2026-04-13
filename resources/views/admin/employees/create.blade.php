@extends('admin.layouts.app')

@section('title', 'إضافة موظف')
@section('page-title', 'إضافة موظف')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="card-title">إنشاء حساب موظف جديد</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ url('/admin/employees') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">اسم الموظف</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required placeholder="أدخل اسم الموظف">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">رقم الجوال</label>
                    <input id="phone" name="phone" type="tel" class="form-control" value="{{ old('phone') }}" required placeholder="09XXXXXXXX">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="employee_type">نوع الموظف</label>
                    <select id="employee_type" name="employee_type" class="form-control" required>
                        <option value="" disabled {{ old('employee_type') ? '' : 'selected' }}>اختر نوع الموظف</option>
                        <option value="fuel" {{ old('employee_type') === 'fuel' ? 'selected' : '' }}>⛽ وقود</option>
                        <option value="towing" {{ old('employee_type') === 'towing' ? 'selected' : '' }}>🚛 سحب</option>
                    </select>
                    @error('employee_type') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">كلمة المرور</label>
                    <input id="password" name="password" type="password" class="form-control" required minlength="8" placeholder="8 أحرف على الأقل">
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">تأكيد كلمة المرور</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required minlength="8" placeholder="أعد إدخال كلمة المرور">
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary">إضافة الموظف</button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@endsection
