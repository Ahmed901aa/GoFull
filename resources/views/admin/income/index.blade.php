@extends('admin.layouts.app')
@section('title', 'دخل السائقين والطلبات')
@section('content')

{{-- ════════════════════════════════════════════════════════
     Section 1 — Overview Stats
     ════════════════════════════════════════════════════════ --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">💰</div>
        <div>
            <div class="stat-value">{{ number_format($totalIncome, 2) }}</div>
            <div class="stat-label">إجمالي الدخل (د.ل)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;">👥</div>
        <div>
            <div class="stat-value">{{ $driversWithCompletedOrders }}</div>
            <div class="stat-label">سائقون أكملوا طلبات</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">📊</div>
        <div>
            <div class="stat-value">{{ $drivers->total() }}</div>
            <div class="stat-label">إجمالي السائقين النشطين</div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     Section 2 — Orders Breakdown (Fuel vs Towing)
     ════════════════════════════════════════════════════════ --}}
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);margin-top:20px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">⛽</div>
        <div>
            <div class="stat-value">{{ $fuelOrders }}</div>
            <div class="stat-label">طلبات وقود مكتملة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff3e0;color:#e65100;">🚛</div>
        <div>
            <div class="stat-value">{{ $towingOrders }}</div>
            <div class="stat-label">طلبات سحب مكتملة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">💵</div>
        <div>
            <div class="stat-value">{{ number_format($fuelIncome, 2) }}</div>
            <div class="stat-label">دخل الوقود (د.ل)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff3e0;color:#e65100;">💵</div>
        <div>
            <div class="stat-value">{{ number_format($towingIncome, 2) }}</div>
            <div class="stat-label">دخل السحب (د.ل)</div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     Section 3 — Payment Stats
     ════════════════════════════════════════════════════════ --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);margin-top:20px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3e5f5;color:#6a1b9a;">🧾</div>
        <div>
            <div class="stat-value">{{ $totalPaidOrders }}</div>
            <div class="stat-label">عدد الطلبات المدفوعة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">✅</div>
        <div>
            <div class="stat-value">{{ number_format($totalPaidAmount, 2) }}</div>
            <div class="stat-label">إجمالي المبالغ المدفوعة (د.ل)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fce4ec;color:#c62828;">💳</div>
        <div>
            <div class="stat-value">{{ number_format($cashPaid, 2) }}</div>
            <div class="stat-label">المدفوع نقداً (د.ل)</div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     Section 4 — Top Requesters
     ════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-top:20px;margin-bottom:20px;">
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
     Section 5 — Driver Income Table (existing + search)
     ════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">دخل السائقين</span>
    </div>
    <div style="padding:16px;">
        <form method="GET" action="{{ route('admin.income.index') }}" style="display:flex;gap:10px;">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="بحث بالاسم أو رقم الهاتف..."
                   style="flex:1;padding:8px 14px;border:1px solid var(--border);border-radius:8px;font-family:inherit;font-size:14px;">
            <button type="submit" class="btn btn-primary btn-sm">بحث</button>
            @if(request('search'))
                <a href="{{ route('admin.income.index') }}" class="btn btn-ghost btn-sm">مسح</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
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
    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: 1fr !important; }
    }
</style>
@endpush
