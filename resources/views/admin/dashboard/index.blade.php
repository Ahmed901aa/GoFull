@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')
@section('content')

<div id="dashboard-content">

    {{-- ══════════════════════════════════════════════════════════
         ROW 1 — Welcome Banner + Quick Stats
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-welcome" id="dash-welcome">
        <div class="dash-welcome-text">
            <h2>مرحباً، {{ auth()->user()->name }}</h2>
            <p>{{ now()->locale('ar')->translatedFormat('l، d F Y') }}</p>
        </div>
        <div class="dash-welcome-stats">
            <div class="dash-live-stat">
                <div class="dash-live-dot"></div>
                <span>{{ $stats['active_requests'] }} طلب نشط</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 2 — Revenue Cards
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-row" id="stats-section" style="grid-template-columns:repeat(4,1fr);">
        <div class="dash-metric-card dash-metric-green">
            <div class="dash-metric-top">
                <span class="dash-metric-icon">💰</span>
                <span class="dash-metric-label">إيرادات اليوم</span>
            </div>
            <div class="dash-metric-value">{{ number_format($stats['revenue_today'], 2) }}</div>
            <div class="dash-metric-unit">د.ل</div>
        </div>
        <div class="dash-metric-card dash-metric-blue">
            <div class="dash-metric-top">
                <span class="dash-metric-icon">📅</span>
                <span class="dash-metric-label">إيرادات الأسبوع</span>
            </div>
            <div class="dash-metric-value">{{ number_format($stats['revenue_week'], 2) }}</div>
            <div class="dash-metric-unit">د.ل</div>
        </div>
        <div class="dash-metric-card dash-metric-purple">
            <div class="dash-metric-top">
                <span class="dash-metric-icon">📊</span>
                <span class="dash-metric-label">إيرادات الشهر</span>
            </div>
            <div class="dash-metric-value">{{ number_format($stats['revenue_month'], 2) }}</div>
            <div class="dash-metric-unit">د.ل</div>
        </div>
        <div class="dash-metric-card dash-metric-gold">
            <div class="dash-metric-top">
                <span class="dash-metric-icon">🏦</span>
                <span class="dash-metric-label">إجمالي الإيرادات</span>
            </div>
            <div class="dash-metric-value">{{ number_format($stats['revenue_total'], 2) }}</div>
            <div class="dash-metric-unit">د.ل</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 3 — Activity Stats
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-row" style="grid-template-columns:repeat(5,1fr);">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce4ec;color:#c62828;">🔴</div>
            <div>
                <div class="stat-value" id="active-count">{{ $stats['active_requests'] }}</div>
                <div class="stat-label">طلبات نشطة</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">✅</div>
            <div>
                <div class="stat-value">{{ $stats['completed_today'] }}</div>
                <div class="stat-label">مكتملة اليوم</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">📋</div>
            <div>
                <div class="stat-value">{{ $stats['total_completed'] }}</div>
                <div class="stat-label">إجمالي المكتملة</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;">👥</div>
            <div>
                <div class="stat-value">{{ $stats['total_drivers'] }}</div>
                <div class="stat-label">العملاء</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;">🚗</div>
            <div>
                <div class="stat-value">{{ $stats['total_providers'] }}</div>
                <div class="stat-label">مزودو الخدمة</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 4 — Fuel vs Towing + Completion Rate + Service Fees
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-row" id="breakdown-section" style="grid-template-columns:1fr 1fr 1fr;">
        {{-- Fuel vs Towing --}}
        <div class="card">
            <div class="card-header"><span class="card-title">وقود vs سحب</span></div>
            <div class="card-body">
                <div style="display:flex;gap:12px;margin-bottom:16px;">
                    <div class="dash-mini-stat" style="background:#e8f5e9;flex:1;">
                        <div style="font-size:11px;color:var(--text-muted);">⛽ وقود</div>
                        <div style="font-size:22px;font-weight:800;color:#2e7d32;">{{ $fuelStats['orders'] }}</div>
                        <div style="font-size:11px;color:#2e7d32;">{{ number_format($fuelStats['revenue'], 2) }} د.ل</div>
                    </div>
                    <div class="dash-mini-stat" style="background:#fff3e0;flex:1;">
                        <div style="font-size:11px;color:var(--text-muted);">🚛 سحب</div>
                        <div style="font-size:22px;font-weight:800;color:#e65100;">{{ $towingStats['orders'] }}</div>
                        <div style="font-size:11px;color:#e65100;">{{ number_format($towingStats['revenue'], 2) }} د.ل</div>
                    </div>
                </div>
                <div style="display:flex;justify-content:center;">
                    <div style="width:180px;height:180px;">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completion Rate --}}
        <div class="card">
            <div class="card-header"><span class="card-title">معدلات الأداء</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:12px;color:var(--text-muted);">معدل الإكمال</span>
                        <span style="font-size:14px;font-weight:700;color:#2e7d32;">{{ $completionRate }}%</span>
                    </div>
                    <div class="dash-progress-bar">
                        <div class="dash-progress-fill" style="width:{{ $completionRate }}%;background:#2e7d32;"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:12px;color:var(--text-muted);">معدل الإلغاء</span>
                        <span style="font-size:14px;font-weight:700;color:#c62828;">{{ $cancellationRate }}%</span>
                    </div>
                    <div class="dash-progress-bar">
                        <div class="dash-progress-fill" style="width:{{ $cancellationRate }}%;background:#c62828;"></div>
                    </div>
                </div>
                <div style="display:flex;gap:12px;margin-top:8px;">
                    <div class="dash-mini-stat" style="background:#e8f5e9;">
                        <div style="font-size:20px;font-weight:800;color:#2e7d32;">{{ $stats['total_completed'] }}</div>
                        <div style="font-size:11px;color:#2e7d32;">مكتمل</div>
                    </div>
                    <div class="dash-mini-stat" style="background:#fce4ec;">
                        <div style="font-size:20px;font-weight:800;color:#c62828;">{{ $stats['total_cancelled'] }}</div>
                        <div style="font-size:11px;color:#c62828;">ملغي</div>
                    </div>
                    <div class="dash-mini-stat" style="background:#fff3e0;">
                        <div style="font-size:20px;font-weight:800;color:#e65100;">{{ $byStatus->get('pending', 0) }}</div>
                        <div style="font-size:11px;color:#e65100;">بانتظار</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Service Fees + Pending Providers --}}
        <div class="card">
            <div class="card-header"><span class="card-title">رسوم الخدمة والتوثيق</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">
                <div class="dash-mini-stat" style="background:#f3e5f5;text-align:center;">
                    <div style="font-size:11px;color:var(--text-muted);">إجمالي رسوم الخدمة</div>
                    <div style="font-size:24px;font-weight:800;color:#6a1b9a;">{{ number_format($stats['service_fees_total'], 2) }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">د.ل</div>
                </div>
                <div style="display:flex;gap:12px;">
                    <div class="dash-mini-stat" style="background:#e8f5e9;flex:1;text-align:center;">
                        <div style="font-size:11px;color:var(--text-muted);">مزودون معتمدون</div>
                        <div style="font-size:22px;font-weight:800;color:#2e7d32;">{{ $stats['total_providers'] }}</div>
                    </div>
                    <div class="dash-mini-stat" style="background:#fff3e0;flex:1;text-align:center;">
                        <div style="font-size:11px;color:var(--text-muted);">بانتظار التوثيق</div>
                        <div style="font-size:22px;font-weight:800;color:#e65100;">{{ $stats['pending_providers'] }}</div>
                    </div>
                </div>
                <a href="{{ route('admin.providers.index') }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                    إدارة مزودي الخدمة ←
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 5 — Charts (Revenue + Orders per Day)
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-row" style="grid-template-columns:1fr 1fr;">
        <div class="card">
            <div class="card-header"><span class="card-title">الإيرادات اليومية — آخر 14 يوم</span></div>
            <div class="card-body" style="padding:16px 20px;">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title">الطلبات اليومية — آخر 14 يوم</span></div>
            <div class="card-body" style="padding:16px 20px;">
                <canvas id="ordersChart" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 6 — Status Distribution + Peak Hours
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-row" style="grid-template-columns:1fr 1fr;">
        <div class="card">
            <div class="card-header"><span class="card-title">توزيع حالات الطلبات</span></div>
            <div class="card-body" style="padding:16px 20px;">
                <canvas id="statusChart" height="130"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title">ساعات الذروة — آخر 30 يوم</span></div>
            <div class="card-body" style="padding:16px 20px;">
                <canvas id="peakChart" height="130"></canvas>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 7 — Recent Orders + Top Providers
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-row" id="tables-section" style="grid-template-columns:1fr 1fr;">
        {{-- Recent Orders --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">آخر الطلبات</span>
                <div style="display:flex;align-items:center;gap:10px;">
                    <span id="refresh-indicator" style="font-size:11px;color:var(--text-faint);"></span>
                    <a href="{{ route('admin.income.index') }}" class="btn btn-ghost btn-sm">عرض الكل ←</a>
                </div>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>السائق</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>المبلغ</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRequests as $req)
                        @php
                            $statusMap = [
                                'pending'     => ['label' => 'قيد الانتظار', 'bg' => '#fff3cd', 'fg' => '#856404'],
                                'accepted'    => ['label' => 'مقبول',         'bg' => '#cce5ff', 'fg' => '#004085'],
                                'en_route'    => ['label' => 'في الطريق',    'bg' => '#d4edda', 'fg' => '#155724'],
                                'arrived'     => ['label' => 'وصل',           'bg' => '#d1ecf1', 'fg' => '#0c5460'],
                                'in_progress' => ['label' => 'جاري التنفيذ', 'bg' => '#e2d5f1', 'fg' => '#4a148c'],
                                'completed'   => ['label' => 'مكتمل',        'bg' => '#e8f5e9', 'fg' => '#2e7d32'],
                                'cancelled'   => ['label' => 'ملغي',         'bg' => '#fce4ec', 'fg' => '#c62828'],
                            ];
                            $s = $statusMap[$req->status] ?? ['label' => $req->status, 'bg' => '#eee', 'fg' => '#333'];
                        @endphp
                        <tr>
                            <td class="td-muted td-mono">#{{ $req->id }}</td>
                            <td style="font-size:13px;">{{ $req->driver->name ?? '—' }}</td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $req->service_type === 'fuel_delivery' ? '⛽' : '🚛' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }};">{{ $s['label'] }}</span>
                            </td>
                            <td class="td-mono" style="font-weight:600;">
                                {{ $req->total ? number_format($req->total, 2) : '—' }}
                            </td>
                            <td class="td-muted" style="font-size:11px;">
                                {{ $req->created_at->locale('ar')->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6">
                            <div class="empty-state" style="padding:30px;">
                                <h3>لا توجد طلبات بعد</h3>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Providers --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">أفضل مزودي الخدمة</span>
                <a href="{{ route('admin.providers.index', ['status' => 'approved']) }}" class="btn btn-ghost btn-sm">عرض الكل ←</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>المكتملة</th>
                            <th>الإيرادات</th>
                            <th>التقييم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProviders as $i => $provider)
                        <tr>
                            <td class="td-muted">{{ $i + 1 }}</td>
                            <td>
                                <a href="{{ route('admin.providers.show', $provider) }}" style="font-weight:600;color:var(--primary);">
                                    {{ $provider->user->name ?? '—' }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $provider->service_type === 'fuel_delivery' ? '⛽' : '🚛' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background:#e8f5e9;color:#2e7d32;font-weight:700;">
                                    {{ $provider->completed_orders }}
                                </span>
                            </td>
                            <td class="td-mono" style="font-weight:600;color:#2e7d32;">
                                {{ number_format($provider->total_revenue ?? 0, 2) }}
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
                        @empty
                        <tr><td colspan="6">
                            <div class="empty-state" style="padding:30px;">
                                <h3>لا يوجد مزودو خدمة أكملوا طلبات بعد</h3>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ── Dashboard Row ─────────── */
    .dash-row {
        display: grid;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* ── Welcome Banner ────────── */
    .dash-welcome {
        background: linear-gradient(135deg, var(--primary) 0%, #006B52 100%);
        border-radius: var(--radius);
        padding: 24px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-direction: row-reverse;
        margin-bottom: 20px;
        box-shadow: 0 4px 16px rgba(0,75,59,0.2);
    }
    .dash-welcome-text h2 {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .dash-welcome-text p {
        color: rgba(255,255,255,0.7);
        font-size: 13px;
    }
    .dash-live-stat {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.15);
        padding: 8px 16px;
        border-radius: 99px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
    }
    .dash-live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4ade80;
        animation: pulse 2s infinite;
    }

    /* ── Metric Cards ──────────── */
    .dash-metric-card {
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        overflow: hidden;
    }
    .dash-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    .dash-metric-card::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
    }
    .dash-metric-green { background: var(--surface); border: 1px solid #c8e6c9; }
    .dash-metric-green::after { background: #2e7d32; }
    .dash-metric-blue { background: var(--surface); border: 1px solid #bbdefb; }
    .dash-metric-blue::after { background: #1565c0; }
    .dash-metric-purple { background: var(--surface); border: 1px solid #d1c4e9; }
    .dash-metric-purple::after { background: #4a148c; }
    .dash-metric-gold { background: var(--surface); border: 1px solid #ffe0b2; }
    .dash-metric-gold::after { background: #e65100; }

    .dash-metric-top {
        display: flex;
        align-items: center;
        flex-direction: row-reverse;
        gap: 8px;
        margin-bottom: 12px;
    }
    .dash-metric-icon { font-size: 18px; }
    .dash-metric-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .dash-metric-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
    }
    .dash-metric-unit {
        font-size: 11px;
        color: var(--text-faint);
        margin-top: 4px;
    }

    /* ── Mini Stat Block ─────── */
    .dash-mini-stat {
        padding: 12px;
        border-radius: var(--radius-sm);
        text-align: right;
    }

    /* ── Progress Bars ─────────── */
    .dash-progress-bar {
        height: 8px;
        background: #f0f0f0;
        border-radius: 99px;
        overflow: hidden;
    }
    .dash-progress-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 0.6s ease;
    }

    /* ── Responsive ────────────── */
    @media (max-width: 1200px) {
        .dash-row { grid-template-columns: 1fr 1fr !important; }
    }
    @media (max-width: 768px) {
        .dash-row { grid-template-columns: 1fr !important; }
        .dash-welcome { flex-direction: column; gap: 12px; text-align: center; }
        .dash-welcome-text h2, .dash-welcome-text p { text-align: center; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Cairo', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6c757d';

    const brandGreen = '#004B3B';
    const brandGold  = '#D4A843';

    // ── Doughnut — Fuel vs Towing ─────────────────────────
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: ['⛽ وقود', '🚛 سحب'],
            datasets: [{
                data: [{{ $fuelStats['orders'] }}, {{ $towingStats['orders'] }}],
                backgroundColor: [brandGreen, brandGold],
                borderWidth: 0,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true,
                    labels: { padding: 12, usePointStyle: true, pointStyleWidth: 10, font: { size: 11, weight: '600' } }
                }
            }
        }
    });

    // ── Bar — Revenue per Day ────────────────────────────
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartDays->pluck('date')->toArray()) !!},
            datasets: [{
                label: 'الإيرادات (د.ل)',
                data: {!! json_encode($chartDays->pluck('revenue')->toArray()) !!},
                backgroundColor: 'rgba(0,75,59,0.75)',
                borderRadius: 4,
                borderSkipped: false,
                barPercentage: 0.7,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 7, maxRotation: 0, font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: '#f5f5f5' } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });

    // ── Line — Orders per Day ────────────────────────────
    new Chart(document.getElementById('ordersChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartDays->pluck('date')->toArray()) !!},
            datasets: [{
                label: 'الطلبات',
                data: {!! json_encode($chartDays->pluck('orders')->toArray()) !!},
                borderColor: brandGreen,
                backgroundColor: 'rgba(0,75,59,0.08)',
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointRadius: 3,
                pointBackgroundColor: brandGreen,
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 7, maxRotation: 0, font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: '#f5f5f5' }, ticks: { stepSize: 1 } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });

    // ── Horizontal Bar — Status Distribution ────────────
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
                backgroundColor: [brandGreen, '#c62828', '#e65100', '#1565c0', '#0c5460', '#4a148c', '#6a1b9a'],
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 16,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f5f5f5' }, ticks: { stepSize: 1 } },
                y: { grid: { display: false } }
            }
        }
    });

    // ── Bar — Peak Hours ─────────────────────────────────
    (function() {
        const hours = Array.from({length: 24}, (_, i) => i);
        const data = hours.map(h => {!! json_encode($peakHours) !!}[h] || 0);
        const maxVal = Math.max(...data, 1);
        const colors = data.map(v => {
            const ratio = v / maxVal;
            return ratio > 0.7 ? '#c62828' : ratio > 0.4 ? '#e65100' : brandGreen;
        });

        new Chart(document.getElementById('peakChart'), {
            type: 'bar',
            data: {
                labels: hours.map(h => h + ':00'),
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderRadius: 3,
                    borderSkipped: false,
                    barPercentage: 0.85,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 12, maxRotation: 0, font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: '#f5f5f5' }, ticks: { stepSize: 1 } }
                }
            }
        });
    })();

    // ── Auto-refresh stats & tables every 10 seconds (skip charts) ──
    setInterval(() => {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Refresh specific sections without destroying charts
                ['dash-welcome', 'stats-section', 'breakdown-section', 'tables-section'].forEach(id => {
                    const freshEl = doc.getElementById(id);
                    const currentEl = document.getElementById(id);
                    if (freshEl && currentEl) currentEl.innerHTML = freshEl.innerHTML;
                });

                const ind = document.getElementById('refresh-indicator');
                if (ind) ind.textContent = 'تحديث ' + new Date().toLocaleTimeString('ar');
            })
            .catch(() => {});
    }, 10000);
</script>
@endpush
