@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')
@section('content')

<div id="dashboard-content">
    {{-- Stats --}}
    <div class="stats-grid">
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
            <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">💰</div>
            <div>
                <div class="stat-value">{{ number_format($stats['revenue_today'], 2) }}</div>
                <div class="stat-label">إيرادات اليوم (د.ل)</div>
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
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3e0;color:#e65100;">⏳</div>
            <div>
                <div class="stat-value">{{ $stats['pending_providers'] }}</div>
                <div class="stat-label">بانتظار التوثيق</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">📊</div>
            <div>
                <div class="stat-value">{{ $stats['total_requests'] }}</div>
                <div class="stat-label">إجمالي الطلبات</div>
            </div>
        </div>
    </div>

    {{-- Two columns: Recent Orders + Top Providers --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
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
                            <td class="td-muted" style="font-size:11px;">
                                {{ $req->created_at->locale('ar')->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5">
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
                            <th>الطلبات المكتملة</th>
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
                                <span class="badge" style="background:#e8f5e9;color:#2e7d32;font-weight:700;">
                                    {{ $provider->completed_orders }}
                                </span>
                            </td>
                            <td>
                                @if(($provider->real_total_ratings ?? 0) > 0)
                                    <span style="font-weight:700;color:#e65100;">{{ number_format($provider->real_avg_rating, 1) }}</span> ⭐
                                @else
                                    <span class="td-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4">
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
    @media (max-width: 900px) {
        #dashboard-content > div:last-child { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script>
    setInterval(() => {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const fresh = doc.getElementById('dashboard-content');
                if (fresh) document.getElementById('dashboard-content').innerHTML = fresh.innerHTML;
                const ind = document.getElementById('refresh-indicator');
                if (ind) ind.textContent = 'تحديث ' + new Date().toLocaleTimeString('ar');
            })
            .catch(() => {});
    }, 5000);
</script>
@endpush
