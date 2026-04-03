@extends('admin.layouts.app')

@section('title', 'إضافة موظف')
@section('page-title', 'إضافة موظف')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">موظف جديد</h2>
        <p class="page-text">إنشاء حساب موظف جديد للوصول إلى لوحة التحكم.</p>

        <form method="POST" action="{{ url('/admin/employees') }}" class="form-grid">
            @csrf

            <div class="field">
                <label class="label" for="name">اسم الموظف</label>
                <input id="name" name="name" type="text" class="input" required>
            </div>

            <div class="field">
                <label class="label" for="email">البريد الإلكتروني</label>
                <input id="email" name="email" type="email" class="input" required>
            </div>

            <div class="field">
                <label class="label" for="role">الدور</label>
                <select id="role" name="role" class="select" required>
                    <option value="">اختر الدور</option>
                    <option value="support">موظف دعم</option>
                    <option value="supervisor">مشرف</option>
                    <option value="manager">مدير</option>
                </select>
            </div>

            <div class="field">
                <label class="label" for="password">كلمة المرور</label>
                <input id="password" name="password" type="password" class="input" required>
            </div>

            <div class="field field-full">
                <label class="label" for="notes">ملاحظات</label>
                <textarea id="notes" name="notes" class="textarea" placeholder="ملاحظات داخلية اختيارية"></textarea>
            </div>

            <div class="field field-full">
                <button type="submit" class="btn btn-primary">إضافة موظف</button>
            </div>
        </form>
    </section>
@endsection
