@extends('admin.layouts.app')

@section('title', 'تفاصيل مزود الخدمة')
@section('page-title', 'تفاصيل مزود الخدمة')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">ملف مزود الخدمة</h2>
        <p class="page-text">تفاصيل الحساب والأداء وحالة التوثيق.</p>

        <div class="table-wrap">
            <table class="table">
                <tbody>
                    <tr>
                        <th>المعرّف</th>
                        <td>#11</td>
                    </tr>
                    <tr>
                        <th>الاسم</th>
                        <td>Ahmad Nasr</td>
                    </tr>
                    <tr>
                        <th>نوع الخدمة</th>
                        <td>كهرباء</td>
                    </tr>
                    <tr>
                        <th>حالة التوثيق</th>
                        <td>موثّق</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
