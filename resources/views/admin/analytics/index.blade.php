@extends('admin.layouts.app')

@section('title', 'التحليلات')
@section('page-title', 'التحليلات')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">التحليلات</h2>
        <p class="page-text">تتبّع أداء المنصة من خلال مؤشرات ملخصة.</p>

        <div class="stats-grid">
            <article class="stat-card">
                <p class="stat-label">الطلبات اليوم</p>
                <p class="stat-value">238</p>
            </article>

            <article class="stat-card">
                <p class="stat-label">إجمالي الإيرادات (شهريًا)</p>
                <p class="stat-value">$12,460</p>
            </article>

            <article class="stat-card">
                <p class="stat-label">متوسط زمن الاستجابة</p>
                <p class="stat-value">8.4 د</p>
            </article>
        </div>
    </section>
@endsection
