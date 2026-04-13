@extends('admin.layouts.app')

@section('title', 'المراقبة المباشرة')

@section('content')
<div id="monitor-content">

    {{-- ── Live Banner ─────────────────────────────────────── --}}
    <div class="mon-banner">
        <div class="mon-banner-text">
            <h2>المراقبة المباشرة</h2>
            <p>مراقبة جميع الطلبات الجارية حاليًا في النظام</p>
        </div>
        <div class="mon-live-badge">
            <span class="mon-live-dot"></span>
            <span id="monitor-indicator">مباشر</span>
        </div>
    </div>

    {{-- ── Status Summary Cards ────────────────────────────── --}}
    <div class="mon-row" style="grid-template-columns:repeat(5,1fr);">
        <div class="mon-stat-card mon-stat-total">
            <div class="mon-stat-value">{{ $totalActive }}</div>
            <div class="mon-stat-label">إجمالي النشطة</div>
        </div>
        <div class="mon-stat-card" style="border-bottom:3px solid #856404;">
            <div class="mon-stat-value">{{ $statusCounts->get('pending', 0) }}</div>
            <div class="mon-stat-label">قيد الانتظار</div>
        </div>
        <div class="mon-stat-card" style="border-bottom:3px solid #155724;">
            <div class="mon-stat-value">{{ $statusCounts->get('en_route', 0) + $statusCounts->get('accepted', 0) }}</div>
            <div class="mon-stat-label">مقبول / في الطريق</div>
        </div>
        <div class="mon-stat-card" style="border-bottom:3px solid #0c5460;">
            <div class="mon-stat-value">{{ $statusCounts->get('arrived', 0) }}</div>
            <div class="mon-stat-label">وصل</div>
        </div>
        <div class="mon-stat-card" style="border-bottom:3px solid #4a148c;">
            <div class="mon-stat-value">{{ $statusCounts->get('in_progress', 0) }}</div>
            <div class="mon-stat-label">جاري التنفيذ</div>
        </div>
    </div>

    {{-- ── Type Breakdown ──────────────────────────────────── --}}
    <div class="mon-row" style="grid-template-columns:1fr 1fr;margin-bottom:20px;">
        <div class="mon-type-card" style="border-right:4px solid #2e7d32;">
            <span style="font-size:18px;">⛽</span>
            <span style="font-weight:700;color:#2e7d32;font-size:20px;">{{ $fuelActive }}</span>
            <span style="font-size:13px;color:var(--text-muted);">طلب وقود نشط</span>
        </div>
        <div class="mon-type-card" style="border-right:4px solid #e65100;">
            <span style="font-size:18px;">🚛</span>
            <span style="font-weight:700;color:#e65100;font-size:20px;">{{ $towingActive }}</span>
            <span style="font-size:13px;color:var(--text-muted);">طلب سحب نشط</span>
        </div>
    </div>

    {{-- ── Active Requests Table ───────────────────────────── --}}
    @php
        $statusMap = [
            'pending'     => ['label' => 'قيد الانتظار', 'bg' => '#fff3cd', 'fg' => '#856404'],
            'accepted'    => ['label' => 'مقبول',         'bg' => '#cce5ff', 'fg' => '#004085'],
            'en_route'    => ['label' => 'في الطريق',    'bg' => '#d4edda', 'fg' => '#155724'],
            'arrived'     => ['label' => 'وصل',           'bg' => '#d1ecf1', 'fg' => '#0c5460'],
            'in_progress' => ['label' => 'جاري التنفيذ', 'bg' => '#e2d5f1', 'fg' => '#4a148c'],
        ];
    @endphp

    <div class="card">
        <div class="card-header">
            <span class="card-title">الطلبات النشطة</span>
            <span style="font-size:12px;color:var(--text-faint);">{{ $requests->total() }} طلب</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>السائق (العميل)</th>
                        <th>مزود الخدمة</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th>الموقع</th>
                        <th>المبلغ</th>
                        <th>التاريخ</th>
                        <th>تفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    @php $s = $statusMap[$req->status] ?? ['label' => $req->status, 'bg' => '#eee', 'fg' => '#333']; @endphp
                    <tr>
                        <td class="td-muted td-mono">#{{ $req->id }}</td>
                        <td style="font-weight:600;">{{ $req->driver->name ?? '—' }}</td>
                        <td class="td-muted">{{ $req->provider->user->name ?? 'غير معيّن' }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $req->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                            </span>
                        </td>
                        <td><span class="badge" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }};">{{ $s['label'] }}</span></td>
                        <td class="td-muted" style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $req->driver_address }}">
                            {{ $req->driver_address ?? '—' }}
                        </td>
                        <td class="td-mono" style="font-weight:600;">
                            {{ $req->total ? number_format($req->total, 2) : '—' }}
                        </td>
                        <td class="td-muted" style="font-size:12px;">
                            {{ $req->created_at->locale('ar')->diffForHumans() }}
                        </td>
                        <td>
                            <a href="{{ route('admin.monitor.show', $req) }}" class="btn btn-ghost btn-sm">عرض ←</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9">
                        <div class="empty-state" style="padding:40px;">
                            <div class="empty-state-icon">✅</div>
                            <h3>لا توجد طلبات نشطة حاليًا</h3>
                            <p>سيتم تحديث هذه الصفحة تلقائيًا عند وصول طلبات جديدة</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $requests->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .mon-row {
        display: grid;
        gap: 16px;
        margin-bottom: 16px;
    }

    /* ── Live Banner ──────────── */
    .mon-banner {
        background: linear-gradient(135deg, var(--primary) 0%, #006B52 100%);
        border-radius: var(--radius);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-direction: row-reverse;
        margin-bottom: 20px;
        box-shadow: 0 4px 16px rgba(0,75,59,0.2);
    }
    .mon-banner-text h2 {
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .mon-banner-text p {
        color: rgba(255,255,255,0.7);
        font-size: 13px;
    }
    .mon-live-badge {
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
    .mon-live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4ade80;
        animation: pulse 2s infinite;
    }

    /* ── Status Stat Cards ────── */
    .mon-stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s;
    }
    .mon-stat-card:hover { transform: translateY(-2px); }
    .mon-stat-total {
        background: var(--primary);
        border-color: var(--primary);
    }
    .mon-stat-total .mon-stat-value { color: #fff; }
    .mon-stat-total .mon-stat-label { color: rgba(255,255,255,0.7); }
    .mon-stat-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
        margin-bottom: 4px;
    }
    .mon-stat-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* ── Type Cards ───────────── */
    .mon-type-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-direction: row-reverse;
        box-shadow: var(--shadow-sm);
    }

    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

    @media (max-width: 900px) {
        .mon-row { grid-template-columns: repeat(2, 1fr) !important; }
    }
    @media (max-width: 600px) {
        .mon-row { grid-template-columns: 1fr !important; }
        .mon-banner { flex-direction: column; gap: 12px; text-align: center; }
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
                const fresh = doc.getElementById('monitor-content');
                if (fresh) {
                    document.getElementById('monitor-content').innerHTML = fresh.innerHTML;
                }
            })
            .catch(() => {});
    }, 5000);
</script>
@endpush
