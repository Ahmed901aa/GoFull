@extends('admin.layouts.app')

@section('title', 'التحليلات')
@section('page-title', 'التحليلات')

@section('content')
    {{-- Summary Stats --}}
    <div class="stats-grid">
        <article class="stat-card">
            <div class="stat-icon primary">📋</div>
            <div>
                <p class="stat-value">{{ $byStatus->sum() }}</p>
                <p class="stat-label">إجمالي الطلبات</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon primary">✅</div>
            <div>
                <p class="stat-value">{{ $byStatus->get('completed', 0) }}</p>
                <p class="stat-label">مكتملة</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon primary">⏳</div>
            <div>
                <p class="stat-value">{{ $byStatus->get('pending', 0) }}</p>
                <p class="stat-label">قيد الانتظار</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon primary">❌</div>
            <div>
                <p class="stat-value">{{ $byStatus->get('cancelled', 0) }}</p>
                <p class="stat-label">ملغاة</p>
            </div>
        </article>
    </div>

    {{-- By Service Type --}}
    <section class="page-card" style="margin-top:20px;">
        <h2 class="page-heading">حسب نوع الخدمة</h2>
        <div class="stats-grid">
            <article class="stat-card">
                <div class="stat-icon primary">⛽</div>
                <div>
                    <p class="stat-value">{{ $byType->get('fuel_delivery', 0) }}</p>
                    <p class="stat-label">توصيل وقود</p>
                </div>
            </article>
            <article class="stat-card">
                <div class="stat-icon primary">🚛</div>
                <div>
                    <p class="stat-value">{{ $byType->get('towing', 0) }}</p>
                    <p class="stat-label">خدمة سحب</p>
                </div>
            </article>
        </div>
    </section>

    {{-- Users by Role --}}
    <section class="page-card" style="margin-top:20px;">
        <h2 class="page-heading">المستخدمون حسب الدور</h2>
        <div class="stats-grid">
            <article class="stat-card">
                <div class="stat-icon primary">👤</div>
                <div>
                    <p class="stat-value">{{ $usersByRole->get('driver', 0) }}</p>
                    <p class="stat-label">سائقون</p>
                </div>
            </article>
            <article class="stat-card">
                <div class="stat-icon primary">🚗</div>
                <div>
                    <p class="stat-value">{{ $usersByRole->get('provider', 0) }}</p>
                    <p class="stat-label">مزودو خدمة</p>
                </div>
            </article>
            <article class="stat-card">
                <div class="stat-icon primary">🛡️</div>
                <div>
                    <p class="stat-value">{{ $usersByRole->get('admin', 0) + $usersByRole->get('employee', 0) }}</p>
                    <p class="stat-label">مشرفون</p>
                </div>
            </article>
        </div>
    </section>

    {{-- Top Providers --}}
    @if($topProviders->isNotEmpty())
    <section class="page-card" style="margin-top:20px;">
        <h2 class="page-heading">أفضل مزودي الخدمة</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>نوع الخدمة</th>
                        <th>التقييم</th>
                        <th>عدد التقييمات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProviders as $i => $provider)
                    <tr>
                        <td class="td-muted">{{ $i + 1 }}</td>
                        <td>{{ $provider->user->name ?? '—' }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $provider->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                            </span>
                        </td>
                        <td>{{ number_format($provider->average_rating, 1) }} ⭐</td>
                        <td class="td-muted">{{ $provider->total_ratings }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- Daily Trends (last 30 days) --}}
    @if($perDay->isNotEmpty())
    <section class="page-card" style="margin-top:20px;">
        <h2 class="page-heading">الطلبات اليومية (آخر 30 يوم)</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>عدد الطلبات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($perDay as $day)
                    <tr>
                        <td>{{ $day->date }}</td>
                        <td>{{ $day->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif
@endsection
