@extends('admin.layouts.app')

@section('title', 'التحليلات')
@section('page-title', 'التحليلات والتقارير')

@section('content')

{{-- Summary Stats --}}
<div class="stats-grid">
    <article class="stat-card">
        <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">📋</div>
        <div>
            <p class="stat-value">{{ $byStatus->sum() }}</p>
            <p class="stat-label">إجمالي الطلبات</p>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">✅</div>
        <div>
            <p class="stat-value">{{ $byStatus->get('completed', 0) }}</p>
            <p class="stat-label">مكتملة</p>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-icon" style="background:#fff3e0;color:#e65100;">⏳</div>
        <div>
            <p class="stat-value">{{ $byStatus->get('pending', 0) }}</p>
            <p class="stat-label">قيد الانتظار</p>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-icon" style="background:#fce4ec;color:#c62828;">❌</div>
        <div>
            <p class="stat-value">{{ $byStatus->get('cancelled', 0) }}</p>
            <p class="stat-label">ملغاة</p>
        </div>
    </article>
</div>

{{-- Revenue --}}
<div class="stats-grid">
    <article class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">💰</div>
        <div>
            <p class="stat-value">{{ number_format($revenue['total'] ?? 0, 2) }}</p>
            <p class="stat-label">إجمالي الإيرادات (د.ل)</p>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;">📅</div>
        <div>
            <p class="stat-value">{{ number_format($revenue['today'] ?? 0, 2) }}</p>
            <p class="stat-label">إيرادات اليوم (د.ل)</p>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-icon" style="background:#fff3e0;color:#e65100;">📊</div>
        <div>
            <p class="stat-value">{{ number_format($revenue['this_month'] ?? 0, 2) }}</p>
            <p class="stat-label">إيرادات الشهر (د.ل)</p>
        </div>
    </article>
</div>

{{-- Charts --}}
<div class="analytics-charts">
    {{-- Service Type --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">توزيع الخدمات</span>
        </div>
        <div class="card-body" style="display:flex;justify-content:center;padding:24px;">
            <div style="width:280px;height:280px;">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Status Distribution --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">حالة الطلبات</span>
        </div>
        <div class="card-body" style="padding:24px;">
            <canvas id="statusChart" height="240"></canvas>
        </div>
    </div>
</div>

{{-- Daily Trends --}}
@if($perDay->isNotEmpty())
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <span class="card-title">الطلبات اليومية — آخر 30 يوم</span>
    </div>
    <div class="card-body" style="padding:24px;">
        <canvas id="dailyChart" height="80"></canvas>
    </div>
</div>
@endif

{{-- Users by Role --}}
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <span class="card-title">المستخدمون حسب الدور</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="stats-grid" style="margin:20px;margin-bottom:12px;">
            <article class="stat-card">
                <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;">👤</div>
                <div>
                    <p class="stat-value">{{ $usersByRole->get('driver', 0) }}</p>
                    <p class="stat-label">عملاء</p>
                </div>
            </article>
            <article class="stat-card">
                <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">🚗</div>
                <div>
                    <p class="stat-value">{{ $usersByRole->get('provider', 0) }}</p>
                    <p class="stat-label">مزودو خدمة</p>
                </div>
            </article>
            <article class="stat-card">
                <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">🛡️</div>
                <div>
                    <p class="stat-value">{{ $usersByRole->get('admin', 0) + $usersByRole->get('employee', 0) }}</p>
                    <p class="stat-label">مشرفون</p>
                </div>
            </article>
        </div>
    </div>
</div>

{{-- Top Providers --}}
@if($topProviders->isNotEmpty())
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <span class="card-title">أفضل مزودي الخدمة</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>نوع الخدمة</th>
                    <th>الطلبات المكتملة</th>
                    <th>التقييم</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProviders as $i => $provider)
                <tr>
                    <td class="td-muted">{{ $i + 1 }}</td>
                    <td style="font-weight:600;">{{ $provider->user->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-primary">
                            {{ $provider->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background:#e8f5e9;color:#2e7d32;font-weight:700;">{{ $provider->completed_orders }}</span>
                    </td>
                    <td>
                        @if(($provider->real_total_ratings ?? 0) > 0)
                            <span style="font-weight:700;color:#e65100;">{{ number_format($provider->real_avg_rating, 1) }}</span>
                            <span style="font-size:11px;color:var(--text-muted);">⭐ ({{ $provider->real_total_ratings }})</span>
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .analytics-charts {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 0;
    }
    @media (max-width: 768px) {
        .analytics-charts { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Cairo', sans-serif";
    Chart.defaults.font.size = 12;

    // Doughnut — Service Types
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: ['توصيل وقود', 'خدمة سحب'],
            datasets: [{
                data: [{{ $byType->get('fuel_delivery', 0) }}, {{ $byType->get('towing', 0) }}],
                backgroundColor: ['#004B3B', '#D4A843'],
                borderWidth: 0,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', rtl: true, labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 } }
            }
        }
    });

    // Horizontal Bar — Status
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: ['مكتمل', 'ملغي', 'قيد الانتظار', 'في الطريق', 'مقبول', 'وصل', 'جاري التنفيذ'],
            datasets: [{
                data: [
                    {{ $byStatus->get('completed', 0) }},
                    {{ $byStatus->get('cancelled', 0) }},
                    {{ $byStatus->get('pending', 0) }},
                    {{ $byStatus->get('en_route', 0) }},
                    {{ $byStatus->get('accepted', 0) }},
                    {{ $byStatus->get('arrived', 0) }},
                    {{ $byStatus->get('in_progress', 0) }}
                ],
                backgroundColor: ['#004B3B', '#c62828', '#e65100', '#1565c0', '#0c5460', '#4a148c', '#6a1b9a'],
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { display: false }, ticks: { stepSize: 1 } },
                y: { grid: { display: false } }
            }
        }
    });

    // Line — Daily Trends
    @if($perDay->isNotEmpty())
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($perDay->pluck('date')->toArray()) !!},
            datasets: [{
                label: 'الطلبات',
                data: {!! json_encode($perDay->pluck('total')->toArray()) !!},
                borderColor: '#004B3B',
                backgroundColor: 'rgba(0,75,59,0.08)',
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#004B3B',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 10, maxRotation: 0 } },
                y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { stepSize: 1 } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
    @endif
</script>
@endpush
