@extends('admin.layouts.app')

@section('title', 'التحليلات')
@section('page-title', 'التحليلات والتقارير')

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     ROW 1 — Key Metrics (5 cards)
     ═══════════════════════════════════════════════════════════ --}}
<div class="a-row" style="grid-template-columns:repeat(5,1fr);">
    <div class="a-metric-card">
        <div class="a-metric-header">
            <span class="a-metric-icon" style="background:#ede7f6;color:#4a148c;">📋</span>
            <span class="a-metric-label">إجمالي الطلبات</span>
        </div>
        <div class="a-metric-value">{{ number_format($totalRequests) }}</div>
        <div class="a-metric-sub">
            <span style="color:#2e7d32;">✅ {{ $completedCount }} مكتمل</span>
            <span style="color:#c62828;">❌ {{ $cancelledCount }} ملغي</span>
        </div>
    </div>

    <div class="a-metric-card">
        <div class="a-metric-header">
            <span class="a-metric-icon" style="background:#e8f5e9;color:#2e7d32;">💰</span>
            <span class="a-metric-label">إجمالي الإيرادات</span>
        </div>
        <div class="a-metric-value">{{ number_format($revenue['total'], 2) }}</div>
        <div class="a-metric-sub"><span>د.ل</span></div>
    </div>

    <div class="a-metric-card">
        <div class="a-metric-header">
            <span class="a-metric-icon" style="background:#e3f2fd;color:#1565c0;">📅</span>
            <span class="a-metric-label">إيرادات اليوم</span>
        </div>
        <div class="a-metric-value">{{ number_format($revenue['today'], 2) }}</div>
        <div class="a-metric-sub">
            @php $diff = $revenue['today'] - $revenue['yesterday']; @endphp
            @if($diff >= 0)
                <span style="color:#2e7d32;">▲ +{{ number_format($diff, 2) }} عن أمس</span>
            @else
                <span style="color:#c62828;">▼ {{ number_format($diff, 2) }} عن أمس</span>
            @endif
        </div>
    </div>

    <div class="a-metric-card">
        <div class="a-metric-header">
            <span class="a-metric-icon" style="background:#fff3e0;color:#e65100;">📊</span>
            <span class="a-metric-label">إيرادات الشهر</span>
        </div>
        <div class="a-metric-value">{{ number_format($revenue['this_month'], 2) }}</div>
        <div class="a-metric-sub"><span>د.ل</span></div>
    </div>

    <div class="a-metric-card">
        <div class="a-metric-header">
            <span class="a-metric-icon" style="background:#fce4ec;color:#c62828;">🧾</span>
            <span class="a-metric-label">متوسط قيمة الطلب</span>
        </div>
        <div class="a-metric-value">{{ number_format($avgOrderValue, 2) }}</div>
        <div class="a-metric-sub"><span>د.ل لكل طلب</span></div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ROW 2 — Rates + Revenue Split + Fees
     ═══════════════════════════════════════════════════════════ --}}
<div class="a-row" style="grid-template-columns:1fr 1fr 1fr;">
    {{-- Completion & Cancellation Rates --}}
    <div class="card">
        <div class="card-header"><span class="card-title">معدلات الأداء</span></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:12px;color:var(--text-muted);">معدل الإكمال</span>
                    <span style="font-size:14px;font-weight:700;color:#2e7d32;">{{ $completionRate }}%</span>
                </div>
                <div style="height:8px;background:#f0f0f0;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $completionRate }}%;background:#2e7d32;border-radius:99px;transition:width 0.5s;"></div>
                </div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:12px;color:var(--text-muted);">معدل الإلغاء</span>
                    <span style="font-size:14px;font-weight:700;color:#c62828;">{{ $cancellationRate }}%</span>
                </div>
                <div style="height:8px;background:#f0f0f0;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $cancellationRate }}%;background:#c62828;border-radius:99px;transition:width 0.5s;"></div>
                </div>
            </div>
            <div style="display:flex;gap:12px;margin-top:8px;">
                <div class="a-mini-stat" style="background:#e8f5e9;">
                    <div style="font-size:20px;font-weight:800;color:#2e7d32;">{{ $completedCount }}</div>
                    <div style="font-size:11px;color:#2e7d32;">مكتمل</div>
                </div>
                <div class="a-mini-stat" style="background:#fce4ec;">
                    <div style="font-size:20px;font-weight:800;color:#c62828;">{{ $cancelledCount }}</div>
                    <div style="font-size:11px;color:#c62828;">ملغي</div>
                </div>
                <div class="a-mini-stat" style="background:#fff3e0;">
                    <div style="font-size:20px;font-weight:800;color:#e65100;">{{ $byStatus->get('pending', 0) }}</div>
                    <div style="font-size:11px;color:#e65100;">بانتظار</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue by Service Type --}}
    <div class="card">
        <div class="card-header"><span class="card-title">الإيرادات حسب الخدمة</span></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;gap:12px;">
                <div class="a-mini-stat" style="background:#e8f5e9;flex:1;">
                    <div style="font-size:11px;color:var(--text-muted);">⛽ وقود</div>
                    <div style="font-size:18px;font-weight:800;color:#2e7d32;">{{ number_format($revenue['fuel_revenue'], 2) }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $byType->get('fuel_delivery', 0) }} طلب</div>
                </div>
                <div class="a-mini-stat" style="background:#fff3e0;flex:1;">
                    <div style="font-size:11px;color:var(--text-muted);">🚛 سحب</div>
                    <div style="font-size:18px;font-weight:800;color:#e65100;">{{ number_format($revenue['tow_revenue'], 2) }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $byType->get('towing', 0) }} طلب</div>
                </div>
            </div>
            <div style="display:flex;justify-content:center;padding:8px 0;">
                <div style="width:220px;height:220px;">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Service Fees & Users --}}
    <div class="card">
        <div class="card-header"><span class="card-title">رسوم الخدمة والمستخدمون</span></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">
            <div class="a-mini-stat" style="background:#f3e5f5;text-align:center;">
                <div style="font-size:11px;color:var(--text-muted);">إجمالي رسوم الخدمة المحصّلة</div>
                <div style="font-size:24px;font-weight:800;color:#6a1b9a;">{{ number_format($revenue['service_fees'], 2) }} <span style="font-size:13px;font-weight:400;">د.ل</span></div>
                <div style="font-size:11px;color:var(--text-muted);">الرسم الحالي: {{ number_format($currentServiceFee, 2) }} د.ل / طلب</div>
            </div>
            <div style="display:flex;gap:12px;">
                <div class="a-mini-stat" style="background:#e3f2fd;flex:1;text-align:center;">
                    <div style="font-size:11px;color:var(--text-muted);">👤 عملاء</div>
                    <div style="font-size:22px;font-weight:800;color:#1565c0;">{{ $usersByRole->get('driver', 0) }}</div>
                </div>
                <div class="a-mini-stat" style="background:#e8f5e9;flex:1;text-align:center;">
                    <div style="font-size:11px;color:var(--text-muted);">🚗 مزودو خدمة</div>
                    <div style="font-size:22px;font-weight:800;color:#2e7d32;">{{ $usersByRole->get('provider', 0) }}</div>
                </div>
                <div class="a-mini-stat" style="background:#ede7f6;flex:1;text-align:center;">
                    <div style="font-size:11px;color:var(--text-muted);">🛡️ إدارة</div>
                    <div style="font-size:22px;font-weight:800;color:#4a148c;">{{ $usersByRole->get('admin', 0) + $usersByRole->get('employee', 0) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ROW 3 — Daily Trends (orders + revenue, 2 datasets)
     ═══════════════════════════════════════════════════════════ --}}
@if($perDay->isNotEmpty())
<div class="a-row" style="grid-template-columns:1fr 1fr;">
    <div class="card">
        <div class="card-header"><span class="card-title">الطلبات اليومية — آخر 30 يوم</span></div>
        <div class="card-body" style="padding:20px;">
            <canvas id="dailyChart" height="110"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">الإيرادات اليومية — آخر 30 يوم</span></div>
        <div class="card-body" style="padding:20px;">
            <canvas id="revenueChart" height="110"></canvas>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     ROW 4 — Status Bar Chart + Peak Hours
     ═══════════════════════════════════════════════════════════ --}}
<div class="a-row" style="grid-template-columns:1fr 1fr;">
    <div class="card">
        <div class="card-header"><span class="card-title">توزيع حالات الطلبات</span></div>
        <div class="card-body" style="padding:20px;">
            <canvas id="statusChart" height="140"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><span class="card-title">ساعات الذروة — آخر 30 يوم</span></div>
        <div class="card-body" style="padding:20px;">
            <canvas id="peakChart" height="140"></canvas>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ROW 5 — Top Providers
     ═══════════════════════════════════════════════════════════ --}}
@if($topProviders->isNotEmpty())
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <span class="card-title">أفضل 10 مزودي خدمة</span>
        <a href="{{ route('admin.providers.index', ['status' => 'approved']) }}" class="btn btn-ghost btn-sm">عرض الكل ←</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>نوع الخدمة</th>
                    <th>الطلبات المكتملة</th>
                    <th>الإيرادات (د.ل)</th>
                    <th>التقييم</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProviders as $i => $provider)
                <tr>
                    <td class="td-muted">{{ $i + 1 }}</td>
                    <td>
                        <a href="{{ route('admin.providers.show', $provider) }}" style="font-weight:600;color:var(--primary);">
                            {{ $provider->user->name ?? '—' }}
                        </a>
                    </td>
                    <td>
                        <span class="badge badge-primary">
                            {{ $provider->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background:#e8f5e9;color:#2e7d32;font-weight:700;">{{ $provider->completed_orders }}</span>
                    </td>
                    <td class="td-mono" style="font-weight:700;color:#2e7d32;">{{ number_format($provider->total_revenue, 2) }}</td>
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
    .a-row {
        display: grid;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* ── Metric Card ─────────── */
    .a-metric-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        transition: box-shadow var(--transition);
    }
    .a-metric-card:hover { box-shadow: var(--shadow); }
    .a-metric-header {
        display: flex;
        align-items: center;
        flex-direction: row-reverse;
        gap: 8px;
        margin-bottom: 12px;
    }
    .a-metric-icon {
        width: 34px; height: 34px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
    }
    .a-metric-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .a-metric-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
        margin-bottom: 6px;
    }
    .a-metric-sub {
        display: flex;
        gap: 12px;
        font-size: 11px;
        color: var(--text-muted);
    }

    /* ── Mini Stat Block ─────── */
    .a-mini-stat {
        padding: 12px;
        border-radius: var(--radius-sm);
        text-align: right;
    }

    @media (max-width: 1100px) {
        .a-row { grid-template-columns: 1fr 1fr !important; }
    }
    @media (max-width: 768px) {
        .a-row { grid-template-columns: 1fr !important; }
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

    // ── Doughnut — Service Types ───────────────────────────
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: ['⛽ توصيل وقود', '🚛 خدمة سحب'],
            datasets: [{
                data: [{{ $byType->get('fuel_delivery', 0) }}, {{ $byType->get('towing', 0) }}],
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
                    labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10, font: { size: 12, weight: '600' } }
                }
            }
        }
    });

    // ── Horizontal Bar — Status ────────────────────────────
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
                barThickness: 18,
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

    // ── Line — Daily Orders (completed vs cancelled) ──────
    @if($perDay->isNotEmpty())
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($perDay->pluck('date')->toArray()) !!},
            datasets: [
                {
                    label: 'إجمالي',
                    data: {!! json_encode($perDay->pluck('total')->toArray()) !!},
                    borderColor: brandGreen,
                    backgroundColor: 'rgba(0,75,59,0.06)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                },
                {
                    label: 'مكتمل',
                    data: {!! json_encode($perDay->pluck('completed')->toArray()) !!},
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46,125,50,0.06)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    borderDash: [5, 3],
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', rtl: true, labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 8, maxRotation: 0, font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: '#f5f5f5' }, ticks: { stepSize: 1 } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
    @endif

    // ── Bar — Revenue per Day ────────────────────────────
    @if($revenuePerDay->isNotEmpty())
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($revenuePerDay->pluck('date')->toArray()) !!},
            datasets: [{
                label: 'الإيرادات (د.ل)',
                data: {!! json_encode($revenuePerDay->pluck('revenue')->toArray()) !!},
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
                x: { grid: { display: false }, ticks: { maxTicksLimit: 8, maxRotation: 0, font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: '#f5f5f5' } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
    @endif

    // ── Bar — Peak Hours ─────────────────────────────────
    (function() {
        const hours = Array.from({length: 24}, (_, i) => i);
        const data = hours.map(h => {{ Js::from($peakHours) }}[h] || 0);
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
</script>
@endpush
