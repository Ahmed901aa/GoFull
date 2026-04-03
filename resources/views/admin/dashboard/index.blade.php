@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')
@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">👥</div>
        <div>
            <div class="stat-value">{{ $stats['total_drivers'] }}</div>
            <div class="stat-label">إجمالي السائقين</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon primary">🚗</div>
        <div>
            <div class="stat-value">{{ $stats['total_providers'] }}</div>
            <div class="stat-label">مزودو الخدمة النشطون</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon primary">⏳</div>
        <div>
            <div class="stat-value">{{ $stats['pending_providers'] }}</div>
            <div class="stat-label">بانتظار التوثيق</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon primary">🔴</div>
        <div>
            <div class="stat-value">{{ $stats['active_requests'] }}</div>
            <div class="stat-label">الطلبات النشطة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon primary">✅</div>
        <div>
            <div class="stat-value">{{ $stats['completed_today'] }}</div>
            <div class="stat-label">مكتملة اليوم</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon primary">📋</div>
        <div>
            <div class="stat-value">{{ $stats['total_requests'] }}</div>
            <div class="stat-label">إجمالي الطلبات</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">آخر الطلبات</span>
        <a href="{{ route('admin.monitor.index') }}" class="btn btn-ghost btn-sm">عرض الكل ←</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>السائق</th>
                    <th>مزود الخدمة</th>
                    <th>النوع</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentRequests as $req)
                @php
                    $statusMap = [
                        'pending'     => 'قيد الانتظار',
                        'accepted'    => 'مقبول',
                        'en_route'    => 'في الطريق',
                        'arrived'     => 'وصل',
                        'in_progress' => 'جاري التنفيذ',
                        'completed'   => 'مكتمل',
                        'cancelled'   => 'ملغي',
                    ];
                @endphp
                <tr>
                    <td class="td-muted td-mono">#{{ $req->id }}</td>
                    <td>{{ $req->driver->name ?? '—' }}</td>
                    <td class="td-muted">{{ $req->provider->user->name ?? 'غير معيّن' }}</td>
                    <td>
                        <span class="badge badge-primary">
                            {{ $req->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-primary">
                            {{ $statusMap[$req->status] ?? $req->status }}
                        </span>
                    </td>
                    <td class="td-muted" style="font-size:12px">
                        {{ $req->created_at->locale('ar')->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <h3>لا توجد طلبات بعد</h3>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
