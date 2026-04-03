@extends('admin.layouts.app')

@section('title', 'السائقون')
@section('page-title', 'السائقون')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">قائمة السائقين</h2>
        <p class="page-text">إدارة جميع السائقين المسجلين من هذا الجدول.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>المعرّف</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#1</td>
                        <td>Sarah Malik</td>
                        <td>sarah@example.com</td>
                        <td>نشط</td>
                    </tr>
                    <tr>
                        <td>#2</td>
                        <td>Omar Khaled</td>
                        <td>omar@example.com</td>
                        <td>نشط</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
