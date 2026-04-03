@extends('admin.layouts.app')

@section('title', 'تفاصيل المراقبة')
@section('page-title', 'تفاصيل المراقبة')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">تشخيص الخدمة</h2>
        <p class="page-text">سجلات ومؤشرات تفصيلية للخدمة المحددة.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>التوقيت</th>
                        <th>المستوى</th>
                        <th>الرسالة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2026-04-03 10:12</td>
                        <td>تحذير</td>
                        <td>تأخر طابور الإشعارات وصل إلى 30 ثانية.</td>
                    </tr>
                    <tr>
                        <td>2026-04-03 10:14</td>
                        <td>معلومات</td>
                        <td>تم تفعيل توسيع العمال بنجاح.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
