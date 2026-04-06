@extends('admin.layouts.app')

@section('title', 'توثيق مزودي الخدمة')
@section('page-title', 'توثيق مزودي الخدمة')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">مزودو الخدمة</h2>
        <p class="page-text">مراجعة وتوثيق حسابات مزودي الخدمة.</p>

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
                   class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-ghost' }}">
                    {{ $tab['icon'] }} {{ $tab['label'] }} ({{ $counts[$key] ?? 0 }})
                </a>
            @endforeach
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>نوع الخدمة</th>
                        <th>المركبة</th>
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
                    @endphp
                    <tr>
                        <td class="td-muted td-mono">#{{ $provider->id }}</td>
                        <td>{{ $provider->user->name ?? '—' }}</td>
                        <td class="td-muted" dir="ltr">{{ $provider->user->phone ?? '—' }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $provider->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                            </span>
                        </td>
                        <td class="td-muted">{{ $provider->vehicle_make }} {{ $provider->vehicle_model }} — {{ $provider->vehicle_plate }}</td>
                        <td>{{ $provider->total_ratings > 0 ? number_format($provider->average_rating, 1) . ' ⭐' : '—' }}</td>
                        <td><span class="badge" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }};">{{ $s['label'] }}</span></td>
                        <td style="display:flex;gap:6px;flex-wrap:wrap;">
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
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon">🚗</div>
                            <h3>لا يوجد مزودو خدمة في هذه الحالة</h3>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $providers->links() }}
    </section>
@endsection
