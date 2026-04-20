@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')
@section('content')

<div id="dashboard-content">

    {{-- ══════════════════════════════════════════════════════════
         ROW 1 — Welcome Banner
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-welcome animate-in" id="dash-welcome">
        <div class="dash-welcome-bg"></div>
        <div class="dash-welcome-content">
            <div class="dash-welcome-text">
                <div class="dash-welcome-greeting">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="22" height="22" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/>
                    </svg>
                    مرحباً، {{ auth()->user()->name }}
                </div>
                <p>{{ now()->locale('ar')->translatedFormat('l، d F Y') }}</p>
            </div>
            <div class="dash-welcome-badges">
                <div class="dash-live-stat">
                    <div class="dash-live-dot"></div>
                    <span>{{ $stats['active_requests'] }} طلب نشط</span>
                </div>
                <div class="dash-live-stat dash-live-stat-alt">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ $stats['completed_today'] }} مكتمل اليوم</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 2 — Revenue Cards
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-row" id="stats-section" style="grid-template-columns:repeat(4,1fr);">
        <div class="dash-metric-card dash-metric-green animate-in">
            <div class="dash-metric-top">
                <div class="dash-metric-icon-wrap" style="background:#dcfce7;">
                    <svg fill="none" stroke="#166534" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                </div>
                <span class="dash-metric-label">إيرادات اليوم</span>
            </div>
            <div class="dash-metric-value">{{ number_format($stats['revenue_today'], 2) }}</div>
            <div class="dash-metric-unit">د.ل</div>
        </div>
        <div class="dash-metric-card dash-metric-blue animate-in">
            <div class="dash-metric-top">
                <div class="dash-metric-icon-wrap" style="background:#dbeafe;">
                    <svg fill="none" stroke="#1e40af" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <span class="dash-metric-label">إيرادات الأسبوع</span>
            </div>
            <div class="dash-metric-value">{{ number_format($stats['revenue_week'], 2) }}</div>
            <div class="dash-metric-unit">د.ل</div>
        </div>
        <div class="dash-metric-card dash-metric-purple animate-in">
            <div class="dash-metric-top">
                <div class="dash-metric-icon-wrap" style="background:#ede9fe;">
                    <svg fill="none" stroke="#6d28d9" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                </div>
                <span class="dash-metric-label">إيرادات الشهر</span>
            </div>
            <div class="dash-metric-value">{{ number_format($stats['revenue_month'], 2) }}</div>
            <div class="dash-metric-unit">د.ل</div>
        </div>
        <div class="dash-metric-card dash-metric-gold animate-in">
            <div class="dash-metric-top">
                <div class="dash-metric-icon-wrap" style="background:#fef3c7;">
                    <svg fill="none" stroke="#92400e" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                    </svg>
                </div>
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
        <div class="stat-card animate-in">
            <div class="stat-icon" style="background:#fef2f2;">
                <svg fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value" id="active-count">{{ $stats['active_requests'] }}</div>
                <div class="stat-label">طلبات نشطة</div>
            </div>
        </div>
        <div class="stat-card animate-in">
            <div class="stat-icon" style="background:#f0fdf4;">
                <svg fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['completed_today'] }}</div>
                <div class="stat-label">مكتملة اليوم</div>
            </div>
        </div>
        <div class="stat-card animate-in">
            <div class="stat-icon" style="background:#ede9fe;">
                <svg fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_completed'] }}</div>
                <div class="stat-label">إجمالي المكتملة</div>
            </div>
        </div>
        <div class="stat-card animate-in">
            <div class="stat-icon" style="background:#dbeafe;">
                <svg fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_drivers'] }}</div>
                <div class="stat-label">العملاء</div>
            </div>
        </div>
        <div class="stat-card animate-in">
            <div class="stat-icon" style="background:#fef3c7;">
                <svg fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5a2 2 0 01-2 2h-1"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
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
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-title">
                    <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="16" height="16" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.21 15.89A10 10 0 118 2.83"/><path d="M22 12A10 10 0 0012 2v10z"/>
                    </svg>
                    وقود vs ساحبة
                </span>
            </div>
            <div class="card-body">
                <div style="display:flex;gap:12px;margin-bottom:16px;">
                    <div class="dash-mini-stat" style="background:#f0fdf4;flex:1;border-radius:var(--radius-sm);">
                        <div style="font-size:11px;color:var(--text-muted);display:flex;align-items:center;gap:4px;">
                            <svg fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24" width="12" height="12"><circle cx="12" cy="12" r="10"/></svg>
                            وقود
                        </div>
                        <div style="font-size:22px;font-weight:800;color:#166534;">{{ $fuelStats['orders'] }}</div>
                        <div style="font-size:11px;color:#16a34a;">{{ number_format($fuelStats['revenue'], 2) }} د.ل</div>
                    </div>
                    <div class="dash-mini-stat" style="background:#fff7ed;flex:1;border-radius:var(--radius-sm);">
                        <div style="font-size:11px;color:var(--text-muted);display:flex;align-items:center;gap:4px;">
                            <svg fill="none" stroke="#ea580c" stroke-width="2" viewBox="0 0 24 24" width="12" height="12"><circle cx="12" cy="12" r="10"/></svg>
                            ساحبة
                        </div>
                        <div style="font-size:22px;font-weight:800;color:#9a3412;">{{ $towingStats['orders'] }}</div>
                        <div style="font-size:11px;color:#ea580c;">{{ number_format($towingStats['revenue'], 2) }} د.ل</div>
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
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-title">
                    <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="16" height="16" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                    </svg>
                    معدلات الأداء
                </span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:18px;">
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                        <span style="font-size:12px;color:var(--text-muted);font-weight:500;">معدل الإكمال</span>
                        <span style="font-size:15px;font-weight:700;color:#16a34a;">{{ $completionRate }}%</span>
                    </div>
                    <div class="dash-progress-bar">
                        <div class="dash-progress-fill" style="width:{{ $completionRate }}%;background:linear-gradient(90deg,#16a34a,#4ade80);"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                        <span style="font-size:12px;color:var(--text-muted);font-weight:500;">معدل الإلغاء</span>
                        <span style="font-size:15px;font-weight:700;color:#dc2626;">{{ $cancellationRate }}%</span>
                    </div>
                    <div class="dash-progress-bar">
                        <div class="dash-progress-fill" style="width:{{ $cancellationRate }}%;background:linear-gradient(90deg,#dc2626,#f87171);"></div>
                    </div>
                </div>
                <div style="display:flex;gap:12px;margin-top:4px;">
                    <div class="dash-mini-stat" style="background:#f0fdf4;flex:1;border-radius:var(--radius-sm);text-align:center;">
                        <div style="font-size:22px;font-weight:800;color:#166534;">{{ $stats['total_completed'] }}</div>
                        <div style="font-size:11px;color:#16a34a;font-weight:600;">مكتمل</div>
                    </div>
                    <div class="dash-mini-stat" style="background:#fef2f2;flex:1;border-radius:var(--radius-sm);text-align:center;">
                        <div style="font-size:22px;font-weight:800;color:#991b1b;">{{ $stats['total_cancelled'] }}</div>
                        <div style="font-size:11px;color:#dc2626;font-weight:600;">ملغي</div>
                    </div>
                    <div class="dash-mini-stat" style="background:#fff7ed;flex:1;border-radius:var(--radius-sm);text-align:center;">
                        <div style="font-size:22px;font-weight:800;color:#9a3412;">{{ $byStatus->get('pending', 0) }}</div>
                        <div style="font-size:11px;color:#ea580c;font-weight:600;">بانتظار</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Service Fees --}}
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-title">
                    <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="16" height="16" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    رسوم الخدمة والتوثيق
                </span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">
                <div class="dash-mini-stat" style="background:linear-gradient(135deg,#ede9fe,#f5f3ff);text-align:center;border-radius:var(--radius-sm);padding:16px;">
                    <div style="font-size:11px;color:var(--text-muted);font-weight:500;">إجمالي رسوم الخدمة</div>
                    <div style="font-size:28px;font-weight:800;color:#6d28d9;margin:4px 0;">{{ number_format($stats['service_fees_total'], 2) }}</div>
                    <div style="font-size:12px;color:#7c3aed;font-weight:600;">د.ل</div>
                </div>
                <div style="display:flex;gap:12px;">
                    <div class="dash-mini-stat" style="background:#f0fdf4;flex:1;text-align:center;border-radius:var(--radius-sm);">
                        <div style="font-size:11px;color:var(--text-muted);">معتمدون</div>
                        <div style="font-size:22px;font-weight:800;color:#166534;">{{ $stats['total_providers'] }}</div>
                    </div>
                    <div class="dash-mini-stat" style="background:#fff7ed;flex:1;text-align:center;border-radius:var(--radius-sm);">
                        <div style="font-size:11px;color:var(--text-muted);">بانتظار التوثيق</div>
                        <div style="font-size:22px;font-weight:800;color:#9a3412;">{{ $stats['pending_providers'] }}</div>
                    </div>
                </div>
                <a href="{{ route('admin.providers.index') }}" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    إدارة مزودي الخدمة
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 5 — Charts
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-row" style="grid-template-columns:1fr 1fr;">
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-title">
                    <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="16" height="16" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                    الإيرادات اليومية — آخر 14 يوم
                </span>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-title">
                    <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="16" height="16" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    الطلبات اليومية — آخر 14 يوم
                </span>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                <canvas id="ordersChart" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 6 — Status + Peak Hours
         ══════════════════════════════════════════════════════════ --}}
    <div class="dash-row" style="grid-template-columns:1fr 1fr;">
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-title">
                    <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="16" height="16" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.21 15.89A10 10 0 118 2.83"/><path d="M22 12A10 10 0 0012 2v10z"/>
                    </svg>
                    توزيع حالات الطلبات
                </span>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                <canvas id="statusChart" height="130"></canvas>
            </div>
        </div>
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-title">
                    <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="16" height="16" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                    </svg>
                    ساعات الذروة — آخر 30 يوم
                </span>
            </div>
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
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-title">
                    <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="16" height="16" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                    </svg>
                    آخر الطلبات
                </span>
                <div style="display:flex;align-items:center;gap:10px;">
                    <span id="refresh-indicator" style="font-size:11px;color:var(--text-faint);"></span>
                    <a href="{{ route('admin.income.index') }}" class="btn btn-ghost btn-sm">
                        عرض الكل
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="12" height="12"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                </div>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>العميل</th>
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
                                'pending'     => ['label' => 'قيد الانتظار', 'bg' => '#fef3c7', 'fg' => '#92400e'],
                                'accepted'    => ['label' => 'مقبول',         'bg' => '#dbeafe', 'fg' => '#1e40af'],
                                'en_route'    => ['label' => 'في الطريق',    'bg' => '#dcfce7', 'fg' => '#166534'],
                                'arrived'     => ['label' => 'وصل',           'bg' => '#e0f2fe', 'fg' => '#075985'],
                                'in_progress' => ['label' => 'جاري التنفيذ', 'bg' => '#ede9fe', 'fg' => '#5b21b6'],
                                'completed'   => ['label' => 'مكتمل',        'bg' => '#f0fdf4', 'fg' => '#166534'],
                                'cancelled'   => ['label' => 'ملغي',         'bg' => '#fef2f2', 'fg' => '#991b1b'],
                            ];
                            $s = $statusMap[$req->status] ?? ['label' => $req->status, 'bg' => '#f3f4f6', 'fg' => '#374151'];
                        @endphp
                        <tr>
                            <td class="td-muted td-mono">#{{ $req->id }}</td>
                            <td style="font-size:13px;font-weight:500;">{{ $req->driver->name ?? '—' }}</td>
                            <td>
                                <span class="badge" style="background:{{ $req->service_type === 'fuel_delivery' ? '#f0fdf4' : '#fff7ed' }};color:{{ $req->service_type === 'fuel_delivery' ? '#166534' : '#9a3412' }};">
                                    {{ $req->service_type === 'fuel_delivery' ? 'وقود' : 'ساحبة' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }};">{{ $s['label'] }}</span>
                            </td>
                            <td class="td-mono" style="font-weight:700;">
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
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-title">
                    <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="16" height="16" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    أفضل مزودي الخدمة
                </span>
                <a href="{{ route('admin.providers.index', ['status' => 'approved']) }}" class="btn btn-ghost btn-sm">
                    عرض الكل
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="12" height="12"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
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
                                <span class="badge" style="background:{{ $provider->service_type === 'fuel_delivery' ? '#f0fdf4' : '#fff7ed' }};color:{{ $provider->service_type === 'fuel_delivery' ? '#166534' : '#9a3412' }};">
                                    {{ $provider->service_type === 'fuel_delivery' ? 'وقود' : 'ساحبة' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-green" style="font-weight:700;">
                                    {{ $provider->completed_orders }}
                                </span>
                            </td>
                            <td class="td-mono" style="font-weight:700;color:#166534;">
                                {{ number_format($provider->total_revenue ?? 0, 2) }}
                            </td>
                            <td>
                                @if(($provider->real_total_ratings ?? 0) > 0)
                                    <span style="font-weight:700;color:#d97706;">{{ number_format($provider->real_avg_rating, 1) }}</span>
                                    <span style="font-size:11px;color:var(--text-muted);">({{ $provider->real_total_ratings }})</span>
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
        border-radius: var(--radius-lg);
        padding: 28px 32px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #001a14 0%, var(--primary) 50%, #006B54 100%);
    }
    .dash-welcome-bg {
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 50%, rgba(74,222,128,0.08) 0%, transparent 50%),
            radial-gradient(circle at 80% 50%, rgba(6,214,160,0.06) 0%, transparent 50%);
    }
    .dash-welcome-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-direction: row-reverse;
    }
    .dash-welcome-greeting {
        color: #fff;
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-direction: row-reverse;
    }
    .dash-welcome-greeting svg { color: var(--accent); }
    .dash-welcome-text p {
        color: rgba(255,255,255,0.55);
        font-size: 13px;
    }
    .dash-welcome-badges {
        display: flex;
        gap: 10px;
    }
    .dash-live-stat {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.08);
        padding: 8px 18px;
        border-radius: 99px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
    }
    .dash-live-stat-alt {
        background: rgba(74,222,128,0.12);
        border-color: rgba(74,222,128,0.15);
        color: var(--accent);
    }
    .dash-live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--accent);
        animation: pulse 2s infinite;
        box-shadow: 0 0 8px rgba(74,222,128,0.5);
    }

    /* ── Metric Cards ──────────── */
    .dash-metric-card {
        border-radius: var(--radius);
        padding: 22px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 1px solid var(--border);
        background: var(--surface);
    }
    .dash-metric-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    .dash-metric-card::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        transition: height 0.3s;
    }
    .dash-metric-card:hover::after {
        height: 4px;
    }
    .dash-metric-green::after { background: linear-gradient(90deg, #16a34a, #4ade80); }
    .dash-metric-blue::after { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .dash-metric-purple::after { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
    .dash-metric-gold::after { background: linear-gradient(90deg, #d97706, #fbbf24); }

    .dash-metric-top {
        display: flex;
        align-items: center;
        flex-direction: row-reverse;
        gap: 10px;
        margin-bottom: 14px;
    }
    .dash-metric-icon-wrap {
        width: 42px;
        height: 42px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s;
    }
    .dash-metric-card:hover .dash-metric-icon-wrap {
        transform: scale(1.1) rotate(-5deg);
    }
    .dash-metric-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .dash-metric-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
    }
    .dash-metric-unit {
        font-size: 12px;
        color: var(--text-faint);
        margin-top: 4px;
        font-weight: 500;
    }

    /* ── Mini Stat Block ─────── */
    .dash-mini-stat {
        padding: 12px;
        border-radius: var(--radius-sm);
        text-align: right;
        transition: transform 0.2s;
    }
    .dash-mini-stat:hover {
        transform: scale(1.02);
    }

    /* ── Progress Bars ─────────── */
    .dash-progress-bar {
        height: 10px;
        background: #f3f4f6;
        border-radius: 99px;
        overflow: hidden;
    }
    .dash-progress-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .dash-progress-fill::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3));
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* ── Responsive ────────────── */
    @media (max-width: 1200px) {
        .dash-row { grid-template-columns: 1fr 1fr !important; }
    }
    @media (max-width: 768px) {
        .dash-row { grid-template-columns: 1fr !important; }
        .dash-welcome-content { flex-direction: column; gap: 16px; text-align: center; }
        .dash-welcome-greeting { justify-content: center; }
        .dash-welcome-text p { text-align: center; }
        .dash-welcome-badges { flex-wrap: wrap; justify-content: center; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Cairo', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7280';

    const brandGreen = '#004B3B';
    const brandGold  = '#D4A843';

    // ── Doughnut — Fuel vs Towing ─────────────────────────
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: ['وقود', 'ساحبة'],
            datasets: [{
                data: [{{ $fuelStats['orders'] }}, {{ $towingStats['orders'] }}],
                backgroundColor: ['#16a34a', '#ea580c'],
                borderWidth: 0,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true,
                    labels: { padding: 14, usePointStyle: true, pointStyleWidth: 10, font: { size: 12, weight: '600' } }
                }
            },
            animation: { animateRotate: true, duration: 1200 }
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
                backgroundColor: 'rgba(0,75,59,0.8)',
                hoverBackgroundColor: 'rgba(0,75,59,1)',
                borderRadius: 6,
                borderSkipped: false,
                barPercentage: 0.65,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#111827', titleFont: { weight: '700' }, padding: 12, cornerRadius: 8 } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 7, maxRotation: 0, font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false } }
            },
            animation: { duration: 1000, easing: 'easeOutQuart' }
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
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                    g.addColorStop(0, 'rgba(0,75,59,0.15)');
                    g.addColorStop(1, 'rgba(0,75,59,0)');
                    return g;
                },
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: brandGreen,
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: brandGreen,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#111827', padding: 12, cornerRadius: 8 } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 7, maxRotation: 0, font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false }, ticks: { stepSize: 1 } }
            },
            animation: { duration: 1200, easing: 'easeOutQuart' }
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
                backgroundColor: ['#16a34a', '#dc2626', '#d97706', '#2563eb', '#0891b2', '#7c3aed', '#c026d3'],
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 18,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#111827', padding: 12, cornerRadius: 8 } },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false }, ticks: { stepSize: 1 } },
                y: { grid: { display: false } }
            },
            animation: { duration: 1000, easing: 'easeOutQuart' }
        }
    });

    // ── Bar — Peak Hours ─────────────────────────────────
    (function() {
        const hours = Array.from({length: 24}, (_, i) => i);
        const peakMap = {!! json_encode((object) $peakHours) !!};
        const data = hours.map(h => peakMap[h] || 0);
        const maxVal = Math.max(...data, 1);
        const colors = data.map(v => {
            const ratio = v / maxVal;
            return ratio > 0.7 ? '#dc2626' : ratio > 0.4 ? '#d97706' : '#16a34a';
        });

        new Chart(document.getElementById('peakChart'), {
            type: 'bar',
            data: {
                labels: hours.map(h => h + ':00'),
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderRadius: 4,
                    borderSkipped: false,
                    barPercentage: 0.85,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#111827', padding: 12, cornerRadius: 8 } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 12, maxRotation: 0, font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false }, ticks: { stepSize: 1 } }
                },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });
    })();

    // ── Auto-refresh ──
    setInterval(() => {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
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
