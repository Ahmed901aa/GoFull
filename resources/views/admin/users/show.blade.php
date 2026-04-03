@extends('admin.layouts.app')

@section('title', 'تفاصيل المستخدم')
@section('page-title', 'تفاصيل المستخدم')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">ملف المستخدم</h2>
        <p class="page-text">تفاصيل الملف الشخصي وحالة الحساب.</p>

        <div class="table-wrap">
            <table class="table">
                <tbody>
                    <tr>
                        <th>المعرّف</th>
                        <td>#1</td>
                    </tr>
                    <tr>
                        <th>الاسم</th>
                        <td>Sarah Malik</td>
                    </tr>
                    <tr>
                        <th>البريد الإلكتروني</th>
                        <td>sarah@example.com</td>
                    </tr>
                    <tr>
                        <th>تاريخ التسجيل</th>
                        <td>02 أبريل 2026</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
