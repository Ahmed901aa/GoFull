@extends('admin.layouts.app')
@section('title', 'دخل السائقين والطلبات')
@section('content')

{{-- ════════════════════════════════════════════════════════
     Section 1 — Revenue Overview (gradient cards)
     ════════════════════════════════════════════════════════ --}}
<div class="inc-row" style="grid-template-columns:repeat(4,1fr);">
    <div class="inc-metric-card inc-green">
        <div class="inc-metric-icon">💰</div>
        <div class="inc-metric-value">{{ number_format($totalIncome, 2) }}</div>
        <div class="inc-metric-label">إجمالي الدخل (د.ل)</div>
    </div>
    <div class="inc-metric-card inc-blue">
        <div class="inc-metric-icon">👥</div>
        <div class="inc-metric-value">{{ $driversWithCompletedOrders }}</div>
        <div class="inc-metric-label">سائقون أكملوا طلبات</div>
    </div>
    <div class="inc-metric-card inc-purple">
        <div class="inc-metric-icon">🧾</div>
        <div class="inc-metric-value">{{ $totalPaidOrders }}</div>
        <div class="inc-metric-label">طلبات مدفوعة</div>
    </div>
    <div class="inc-metric-card inc-orange">
        <div class="inc-metric-icon">💳</div>
        <div class="inc-metric-value">{{ number_format($cashPaid, 2) }}</div>
        <div class="inc-metric-label">المدفوع نقداً (د.ل)</div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     Section 2 — Fuel vs Towing Cards + Payment Stats
     ════════════════════════════════════════════════════════ --}}
<div class="inc-row" style="grid-template-columns:1fr 1fr 1fr;">
    {{-- Fuel --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">⛽ وقود</span>
        </div>
        <div class="card-body" style="display:flex;gap:16px;">
            <div class="inc-mini" style="background:#e8f5e9;flex:1;text-align:center;">
                <div style="font-size:11px;color:var(--text-muted);">طلبات مكتملة</div>
                <div style="font-size:24px;font-weight:800;color:#2e7d32;">{{ $fuelOrders }}</div>
            </div>
            <div class="inc-mini" style="background:#e8f5e9;flex:1;text-align:center;">
                <div style="font-size:11px;color:var(--text-muted);">الدخل</div>
                <div style="font-size:20px;font-weight:800;color:#2e7d32;">{{ number_format($fuelIncome, 2) }}</div>
                <div style="font-size:10px;color:var(--text-faint);">د.ل</div>
            </div>
        </div>
    </div>

    {{-- Towing --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🚛 سحب</span>
        </div>
        <div class="card-body" style="display:flex;gap:16px;">
            <div class="inc-mini" style="background:#fff3e0;flex:1;text-align:center;">
                <div style="font-size:11px;color:var(--text-muted);">طلبات مكتملة</div>
                <div style="font-size:24px;font-weight:800;color:#e65100;">{{ $towingOrders }}</div>
            </div>
            <div class="inc-mini" style="background:#fff3e0;flex:1;text-align:center;">
                <div style="font-size:11px;color:var(--text-muted);">الدخل</div>
                <div style="font-size:20px;font-weight:800;color:#e65100;">{{ number_format($towingIncome, 2) }}</div>
                <div style="font-size:10px;color:var(--text-faint);">د.ل</div>
            </div>
        </div>
    </div>

    {{-- Payment Summary --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">ملخص الدفع</span>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:var(--surface-2);border-radius:var(--radius-sm);">
                <span style="font-size:12px;color:var(--text-muted);">إجمالي المدفوع</span>
                <span style="font-size:16px;font-weight:700;color:#2e7d32;">{{ number_format($totalPaidAmount, 2) }} د.ل</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:var(--surface-2);border-radius:var(--radius-sm);">
                <span style="font-size:12px;color:var(--text-muted);">طلبات مدفوعة</span>
                <span style="font-size:16px;font-weight:700;color:#4a148c;">{{ $totalPaidOrders }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:var(--surface-2);border-radius:var(--radius-sm);">
                <span style="font-size:12px;color:var(--text-muted);">نقداً</span>
                <span style="font-size:16px;font-weight:700;color:#c62828;">{{ number_format($cashPaid, 2) }} د.ل</span>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     Section 3 — Top Requesters
     ════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">أكثر السائقين طلباً (أعلى 10)</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>السائق</th>
                    <th>الهاتف</th>
                    <th>إجمالي الطلبات</th>
                    <th>طلبات وقود</th>
                    <th>طلبات سحب</th>
                    <th>مكتملة</th>
                    <th>إجمالي المدفوع (د.ل)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topRequesters as $i => $requester)
                <tr>
                    <td class="td-muted td-mono">{{ $i + 1 }}</td>
                    <td style="font-weight:600;">{{ $requester->name }}</td>
                    <td class="td-muted" dir="ltr">{{ $requester->phone }}</td>
                    <td>
                        <span class="badge" style="background:#ede7f6;color:#4a148c;font-weight:700;">
                            {{ $requester->total_requests }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background:#e8f5e9;color:#2e7d32;">
                            ⛽ {{ $requester->fuel_requests }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background:#fff3e0;color:#e65100;">
                            🚛 {{ $requester->towing_requests }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background:#e3f2fd;color:#1565c0;font-weight:700;">
                            {{ $requester->completed_requests }}
                        </span>
                    </td>
                    <td style="font-weight:700;color:#2e7d32;">
                        {{ number_format($requester->total_spent, 2) }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state" style="padding:20px;">
                        <h3>لا توجد طلبات بعد</h3>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     Section 4 — Driver Income Table + Search
     ════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">دخل السائقين</span>
        <form method="GET" action="{{ route('admin.income.index') }}" style="display:flex;gap:8px;">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="بحث بالاسم أو رقم الهاتف..."
                   class="form-control"
                   style="width:260px;padding:6px 12px;font-size:13px;">
            <button type="submit" class="btn btn-primary btn-sm">بحث</button>
            @if(request('search'))
                <a href="{{ route('admin.income.index') }}" class="btn btn-ghost btn-sm">مسح</a>
            @endif
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>السائق</th>
                    <th>الهاتف</th>
                    <th>نوع المركبة</th>
                    <th>رقم اللوحة</th>
                    <th>الطلبات المكتملة</th>
                    <th>إجمالي الدخل (د.ل)</th>
                    <th>التفاصيل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $i => $driver)
                <tr>
                    <td class="td-muted td-mono">{{ $drivers->firstItem() + $i }}</td>
                    <td style="font-weight:600;">{{ $driver->name }}</td>
                    <td class="td-muted" dir="ltr">{{ $driver->phone }}</td>
                    <td>
                        @if($driver->vehicle)
                            <span class="badge" style="background:#e3f2fd;color:#1565c0;">{{ $driver->vehicle->vehicle_type }}</span>
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($driver->vehicle)
                            <span class="td-mono" style="font-weight:600;">{{ $driver->vehicle->license_plate }}</span>
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge" style="background:#e8f5e9;color:#2e7d32;font-weight:700;">
                            {{ $driver->completed_orders }}
                        </span>
                    </td>
                    <td style="font-weight:700;color:#2e7d32;">
                        {{ number_format($driver->total_income, 2) }}
                    </td>
                    <td>
                        <a href="{{ route('admin.income.show', $driver) }}" class="btn btn-ghost btn-sm">
                            عرض ←
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state" style="padding:30px;">
                        <h3>لا يوجد سائقون أكملوا طلبات بعد</h3>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $drivers->links() }}
</div>

@endsection

@push('styles')
<style>
    .inc-row {
        display: grid;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* ── Metric Cards ──────────── */
    .inc-metric-card {
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        overflow: hidden;
        background: var(--surface);
    }
    .inc-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    .inc-metric-card::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
    }
    .inc-green { border: 1px solid #c8e6c9; }
    .inc-green::after { background: #2e7d32; }
    .inc-blue { border: 1px solid #bbdefb; }
    .inc-blue::after { background: #1565c0; }
    .inc-purple { border: 1px solid #d1c4e9; }
    .inc-purple::after { background: #6a1b9a; }
    .inc-orange { border: 1px solid #ffe0b2; }
    .inc-orange::after { background: #e65100; }

    .inc-metric-icon { font-size: 20px; margin-bottom: 8px; }
    .inc-metric-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
        margin-bottom: 4px;
    }
    .inc-metric-label {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* ── Mini Stat Block ─────── */
    .inc-mini {
        padding: 14px;
        border-radius: var(--radius-sm);
    }

    @media (max-width: 1100px) {
        .inc-row { grid-template-columns: 1fr 1fr !important; }
    }
    @media (max-width: 768px) {
        .inc-row { grid-template-columns: 1fr !important; }
    }
</style>
@endpush
