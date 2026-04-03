@extends('admin.layouts.app')

@section('title', 'مزودو الخدمة')
@section('page-title', 'مزودو الخدمة')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">قائمة مزودي الخدمة</h2>
        <p class="page-text">استعراض ومراجعة نشاط مزودي الخدمة.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>المعرّف</th>
                        <th>الاسم</th>
                        <th>نوع الخدمة</th>
                        <th>التقييم</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#11</td>
                        <td>Ahmad Nasr</td>
                        <td>كهرباء</td>
                        <td>4.8</td>
                    </tr>
                    <tr>
                        <td>#12</td>
                        <td>Rana Fares</td>
                        <td>سباكة</td>
                        <td>4.6</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
