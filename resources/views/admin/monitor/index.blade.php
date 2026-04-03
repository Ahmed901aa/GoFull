@extends('admin.layouts.app')

@section('title', 'المراقبة المباشرة')
@section('page-title', 'المراقبة المباشرة')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">مراقبة النظام</h2>
        <p class="page-text">الحالة المباشرة للخدمات الرئيسية وصحتها التشغيلية.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>الخدمة</th>
                        <th>الحالة</th>
                        <th>آخر فحص</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>بوابة API</td>
                        <td>يعمل بشكل طبيعي</td>
                        <td>2 دقيقة مضت</td>
                    </tr>
                    <tr>
                        <td>معالج الطوابير</td>
                        <td>يعمل بشكل طبيعي</td>
                        <td>1 دقيقة مضت</td>
                    </tr>
                    <tr>
                        <td>الإشعارات</td>
                        <td>أداء منخفض</td>
                        <td>5 دقائق مضت</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
