@extends('admin.layouts.app')

@section('title', 'الموظفون')
@section('page-title', 'الموظفون')

@section('content')
    <div class="page-actions">
        <a class="btn btn-primary" href="{{ url('/admin/employees/create') }}">إضافة موظف</a>
    </div>

    <section class="page-card">
        <h2 class="page-heading">دليل الموظفين</h2>
        <p class="page-text">إدارة حسابات الموظفين الداخلية وصلاحياتهم.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>المعرّف</th>
                        <th>الاسم</th>
                        <th>الدور</th>
                        <th>البريد الإلكتروني</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#301</td>
                        <td>Lina Haddad</td>
                        <td>موظف دعم</td>
                        <td>lina@example.com</td>
                    </tr>
                    <tr>
                        <td>#302</td>
                        <td>Hany Salem</td>
                        <td>مشرف</td>
                        <td>hany@example.com</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
