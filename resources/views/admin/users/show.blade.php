@extends('admin.layouts.app')

@section('title', 'تفاصيل المستخدم')
@section('page-title', 'تفاصيل المستخدم')

@section('content')
    @php
        $isProvider = $user->role === 'provider';
        $completedOrders = $user->serviceRequests->where('status', 'completed')->count();
        $cancelledOrders = $user->serviceRequests->where('status', 'cancelled')->count();
        $totalOrders = $user->serviceRequests->count();
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;
    @endphp

    {{-- Stats --}}
    <div class="stats-grid">
        <article class="stat-card">
            <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">✅</div>
            <div>
                <p class="stat-value">{{ $completedOrders }}</p>
                <p class="stat-label">طلبات مكتملة</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon" style="background:#fce4ec;color:#c62828;">❌</div>
            <div>
                <p class="stat-value">{{ $cancelledOrders }}</p>
                <p class="stat-label">طلبات ملغاة</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">📋</div>
            <div>
                <p class="stat-value">{{ $totalOrders }}</p>
                <p class="stat-label">إجمالي الطلبات</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;">📊</div>
            <div>
                <p class="stat-value">{{ $completionRate }}%</p>
                <p class="stat-label">نسبة الإنجاز</p>
            </div>
        </article>
    </div>

    {{-- User Info --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">معلومات المستخدم</span>
            @if($user->status === 'active')
                <span class="badge" style="background:#d4edda;color:#155724;">نشط</span>
            @else
                <span class="badge" style="background:#f8d7da;color:#721c24;">معلّق</span>
            @endif
        </div>
        <div class="card-body" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <tbody>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;width:160px;">المعرّف</td>
                        <td style="padding:12px 20px;font-size:13.5px;">#{{ $user->id }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">الاسم</td>
                        <td style="padding:12px 20px;font-size:13.5px;font-weight:600;">{{ $user->name }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">رقم الجوال</td>
                        <td style="padding:12px 20px;font-size:13.5px;" dir="ltr">{{ $user->phone }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">الدور</td>
                        <td style="padding:12px 20px;">
                            <span class="badge badge-primary">
                                {{ $user->role === 'driver' ? '👤 عميل (سائق)' : '🚗 مزود خدمة' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">تاريخ التسجيل</td>
                        <td style="padding:12px 20px;font-size:13px;color:var(--text-muted);">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Provider Profile --}}
    @if($isProvider && $user->providerProfile)
    @php $pp = $user->providerProfile; @endphp
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <span class="card-title">ملف مزود الخدمة</span>
            <a href="{{ route('admin.providers.show', $pp) }}" class="btn btn-ghost btn-sm">عرض الملف الكامل ←</a>
        </div>
        <div class="card-body" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <tbody>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;width:160px;">نوع الخدمة</td>
                        <td style="padding:12px 20px;">
                            <span class="badge badge-primary">
                                {{ $pp->service_type === 'fuel_delivery' ? '⛽ توصيل وقود' : '🚛 خدمة سحب' }}
                            </span>
                        </td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">المركبة</td>
                        <td style="padding:12px 20px;font-size:13.5px;">{{ $pp->vehicle_make }} {{ $pp->vehicle_model }} — {{ $pp->vehicle_plate }}</td>
                    </tr>
                    @php
                        $ppRating = \Illuminate\Support\Facades\DB::table('ratings')
                            ->join('service_requests', 'service_requests.id', '=', 'ratings.request_id')
                            ->where('service_requests.provider_id', $pp->id)
                            ->selectRaw('ROUND(AVG(ratings.rating), 1) as avg_r, COUNT(*) as total_r')
                            ->first();
                    @endphp
                    <tr>
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">التقييم</td>
                        <td style="padding:12px 20px;">
                            @if(($ppRating->total_r ?? 0) > 0)
                                <span style="font-weight:700;color:#e65100;">{{ number_format($ppRating->avg_r, 1) }}</span>
                                <span style="font-size:12px;color:var(--text-muted);">⭐ ({{ $ppRating->total_r }} تقييم)</span>
                            @else
                                <span class="td-muted">لا يوجد تقييمات بعد</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Recent Orders --}}
    @if($user->serviceRequests->isNotEmpty())
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <span class="card-title">آخر الطلبات</span>
            <span class="badge badge-primary">{{ $totalOrders }}</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th>المبلغ</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
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
                    @endphp
                    @foreach($user->serviceRequests as $order)
                    @php $os = $statusMap[$order->status] ?? ['label' => $order->status, 'bg' => '#eee', 'fg' => '#333']; @endphp
                    <tr>
                        <td class="td-muted td-mono">#{{ $order->id }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $order->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                            </span>
                        </td>
                        <td><span class="badge" style="background:{{ $os['bg'] }};color:{{ $os['fg'] }};">{{ $os['label'] }}</span></td>
                        <td style="font-weight:600;">{{ $order->total ? number_format($order->total, 2) . ' د.ل' : '—' }}</td>
                        <td class="td-muted" style="font-size:12px;">{{ $order->created_at->locale('ar')->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div style="display:flex;gap:10px;margin-top:20px;">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">← رجوع للقائمة</a>
        @if($user->status === 'active')
            <form method="POST" action="{{ route('admin.users.suspend', $user) }}" onsubmit="return confirm('هل تريد تعليق هذا المستخدم؟')">
                @csrf @method('PATCH')
                <button class="btn btn-danger">تعليق الحساب</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.users.activate', $user) }}" onsubmit="return confirm('هل تريد تفعيل هذا المستخدم؟')">
                @csrf @method('PATCH')
                <button class="btn btn-primary">تفعيل الحساب</button>
            </form>
        @endif
    </div>
@endsection
