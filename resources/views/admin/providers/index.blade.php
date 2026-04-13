@extends('admin.layouts.app')

@section('title', 'توثيق مزودي الخدمة')
@section('page-title', 'توثيق مزودي الخدمة')

@section('content')
    {{-- Status Tabs --}}
    <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
        @php
            $tabs = [
                'pending'         => ['label' => 'بانتظار التوثيق', 'icon' => '⏳'],
                'appointment_set' => ['label' => 'موعد محدد',       'icon' => '📅'],
                'approved'        => ['label' => 'معتمد',           'icon' => '✅'],
                'rejected'        => ['label' => 'مرفوض',           'icon' => '❌'],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            <a href="{{ route('admin.providers.index', ['status' => $key]) }}"
               class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-secondary' }}">
                {{ $tab['icon'] }} {{ $tab['label'] }} ({{ $counts[$key] ?? 0 }})
            </a>
        @endforeach
    </div>

    {{-- Search + Service Type Filter --}}
    @php $serviceType = request('service_type'); @endphp
    <div style="display:flex;gap:8px;margin-bottom:20px;align-items:center;flex-wrap:wrap;">
        <form method="GET" action="{{ route('admin.providers.index') }}" style="display:flex;gap:8px;flex:1;min-width:250px;">
            <input type="hidden" name="status" value="{{ $status }}">
            @if($serviceType)<input type="hidden" name="service_type" value="{{ $serviceType }}">@endif
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="بحث بالاسم أو رقم الهاتف..."
                   class="form-control"
                   style="flex:1;padding:6px 12px;font-size:13px;">
            <button type="submit" class="btn btn-primary btn-sm">بحث</button>
            @if(request('search'))
                <a href="{{ route('admin.providers.index', ['status' => $status, 'service_type' => $serviceType]) }}" class="btn btn-ghost btn-sm">مسح</a>
            @endif
        </form>
        <div style="display:flex;gap:6px;">
            <a href="{{ route('admin.providers.index', ['status' => $status, 'search' => request('search')]) }}"
               class="btn btn-sm {{ !$serviceType ? 'btn-primary' : 'btn-secondary' }}">
                📋 الكل
            </a>
            <a href="{{ route('admin.providers.index', ['status' => $status, 'service_type' => 'fuel_delivery', 'search' => request('search')]) }}"
               class="btn btn-sm {{ $serviceType === 'fuel_delivery' ? 'btn-primary' : 'btn-secondary' }}">
                ⛽ وقود
            </a>
            <a href="{{ route('admin.providers.index', ['status' => $status, 'service_type' => 'towing', 'search' => request('search')]) }}"
               class="btn btn-sm {{ $serviceType === 'towing' ? 'btn-primary' : 'btn-secondary' }}">
                🚛 سحب
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>نوع الخدمة</th>
                        <th>المركبة</th>
                        <th>الطلبات المكتملة</th>
                        <th>التقييم</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($providers as $provider)
                    @php
                        $statusLabels = [
                            'pending'         => ['label' => 'بانتظار التوثيق', 'bg' => '#fff3cd', 'fg' => '#856404'],
                            'appointment_set' => ['label' => 'موعد محدد',       'bg' => '#cce5ff', 'fg' => '#004085'],
                            'approved'        => ['label' => 'معتمد',           'bg' => '#d4edda', 'fg' => '#155724'],
                            'rejected'        => ['label' => 'مرفوض',           'bg' => '#f8d7da', 'fg' => '#721c24'],
                        ];
                        $s = $statusLabels[$provider->verification_status] ?? ['label' => $provider->verification_status, 'bg' => '#eee', 'fg' => '#333'];
                        $completionRate = $provider->total_orders > 0
                            ? round(($provider->completed_orders / $provider->total_orders) * 100)
                            : 0;
                    @endphp
                    <tr>
                        <td class="td-muted td-mono">#{{ $provider->id }}</td>
                        <td>
                            <a href="{{ route('admin.providers.show', $provider) }}" style="font-weight:600;color:var(--primary);">
                                {{ $provider->user->name ?? '—' }}
                            </a>
                        </td>
                        <td class="td-muted" dir="ltr">{{ $provider->user->phone ?? '—' }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $provider->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                            </span>
                        </td>
                        <td class="td-muted" style="font-size:12px;">{{ $provider->vehicle_make }} {{ $provider->vehicle_model }} — {{ $provider->vehicle_plate }}</td>
                        <td>
                            <span class="badge" style="background:#e8f5e9;color:#2e7d32;font-weight:700;">
                                {{ $provider->completed_orders }}
                            </span>
                            @if($provider->total_orders > 0)
                                <span style="font-size:11px;color:var(--text-muted);margin-right:4px;">
                                    ({{ $completionRate }}%)
                                </span>
                            @endif
                        </td>
                        <td>
                            @if(($provider->real_total_ratings ?? 0) > 0)
                                <span style="font-weight:700;color:#e65100;">{{ number_format($provider->real_avg_rating, 1) }}</span>
                                <span style="font-size:11px;color:var(--text-muted);">⭐ ({{ $provider->real_total_ratings }})</span>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>
                        <td><span class="badge" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }};">{{ $s['label'] }}</span></td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                <a href="{{ route('admin.providers.show', $provider) }}" class="btn btn-ghost btn-sm">عرض</a>
                                @if($provider->verification_status === 'pending' || $provider->verification_status === 'appointment_set')
                                    <form method="POST" action="{{ route('admin.providers.approve', $provider) }}" onsubmit="return confirm('هل تريد اعتماد هذا المزود؟')">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm" style="background:#d4edda;color:#155724;">اعتماد</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.providers.reject', $provider) }}" onsubmit="return confirm('هل تريد رفض هذا المزود؟')">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="rejection_reason" value="لم يستوفِ الشروط المطلوبة">
                                        <button class="btn btn-sm" style="background:#f8d7da;color:#721c24;">رفض</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9">
                        <div class="empty-state">
                            <div class="empty-state-icon">🚗</div>
                            <h3>لا يوجد مزودو خدمة في هذه الحالة</h3>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $providers->links() }}
@endsection
