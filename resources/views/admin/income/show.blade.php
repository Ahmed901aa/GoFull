@extends('admin.layouts.app')
@section('title', 'دخل السائق — ' . $driver->name)
@section('content')

{{-- Driver Info --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">{{ $driver->name }}</span>
        <a href="{{ route('admin.income.index') }}" class="btn btn-ghost btn-sm">→ رجوع</a>
    </div>
    <div style="padding:20px;display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:20px;">
        <div>
            <div class="stat-label" style="margin-bottom:4px;">الهاتف</div>
            <div style="font-weight:600;font-size:15px;" dir="ltr">{{ $driver->phone }}</div>
        </div>
        <div>
            <div class="stat-label" style="margin-bottom:4px;">نوع المركبة</div>
            <div style="font-weight:600;font-size:15px;">
                {{ $driver->vehicle?->vehicle_type ?? 'لم يتم التسجيل' }}
            </div>
        </div>
        <div>
            <div class="stat-label" style="margin-bottom:4px;">رقم اللوحة</div>
            <div style="font-weight:600;font-size:15px;" dir="ltr">
                {{ $driver->vehicle?->license_plate ?? '—' }}
            </div>
        </div>
        <div>
            <div class="stat-label" style="margin-bottom:4px;">الحالة</div>
            <div>
                @if($driver->status === 'active')
                    <span class="badge" style="background:#e8f5e9;color:#2e7d32;">نشط</span>
                @else
                    <span class="badge" style="background:#fce4ec;color:#c62828;">معلّق</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Income Stats --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">💰</div>
        <div>
            <div class="stat-value">{{ number_format($totalIncome, 2) }}</div>
            <div class="stat-label">إجمالي الدخل (د.ل)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff3e0;color:#e65100;">📅</div>
        <div>
            <div class="stat-value">{{ number_format($todayIncome, 2) }}</div>
            <div class="stat-label">دخل اليوم (د.ل)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">📋</div>
        <div>
            <div class="stat-value">{{ $orders->total() }}</div>
            <div class="stat-label">إجمالي الطلبات المكتملة</div>
        </div>
    </div>
</div>

{{-- Orders Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">الطلبات المكتملة</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>النوع</th>
                    <th>مزود الخدمة</th>
                    <th>المبلغ الفرعي</th>
                    <th>رسوم الخدمة</th>
                    <th>الإجمالي (د.ل)</th>
                    <th>طريقة الدفع</th>
                    <th>تاريخ الإكمال</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="td-muted td-mono">#{{ $order->id }}</td>
                    <td>
                        <span class="badge badge-primary">
                            {{ $order->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                        </span>
                    </td>
                    <td>{{ $order->provider?->user?->name ?? '—' }}</td>
                    <td class="td-mono">{{ number_format($order->subtotal, 2) }}</td>
                    <td class="td-mono">{{ number_format($order->service_fee, 2) }}</td>
                    <td style="font-weight:700;color:#2e7d32;">{{ number_format($order->total, 2) }}</td>
                    <td>
                        <span class="badge" style="background:#f3e5f5;color:#6a1b9a;">
                            {{ $order->payment_method === 'cash' ? 'نقدي' : ($order->payment_method ?? '—') }}
                        </span>
                    </td>
                    <td class="td-muted" style="font-size:12px;">
                        {{ $order->completed_at?->format('Y-m-d H:i') ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state" style="padding:30px;">
                        <h3>لا توجد طلبات مكتملة</h3>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $orders->links() }}
</div>

@endsection

@push('styles')
<style>
    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: 1fr !important; }
    }
</style>
@endpush
