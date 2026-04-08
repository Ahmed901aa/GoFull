@extends('admin.layouts.app')

@section('title', 'تفاصيل مزود الخدمة')
@section('page-title', 'تفاصيل مزود الخدمة')

@section('content')
    @php
        $completionRate = $provider->total_orders > 0
            ? round(($provider->completed_orders / $provider->total_orders) * 100)
            : 0;
        $statusLabels = [
            'pending'         => ['label' => 'بانتظار التوثيق', 'bg' => '#fff3cd', 'fg' => '#856404'],
            'appointment_set' => ['label' => 'موعد محدد',       'bg' => '#cce5ff', 'fg' => '#004085'],
            'approved'        => ['label' => 'معتمد',           'bg' => '#d4edda', 'fg' => '#155724'],
            'rejected'        => ['label' => 'مرفوض',           'bg' => '#f8d7da', 'fg' => '#721c24'],
        ];
        $vs = $statusLabels[$provider->verification_status] ?? ['label' => $provider->verification_status, 'bg' => '#eee', 'fg' => '#333'];
    @endphp

    {{-- Order Stats --}}
    <div class="stats-grid">
        <article class="stat-card">
            <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">✅</div>
            <div>
                <p class="stat-value">{{ $provider->completed_orders }}</p>
                <p class="stat-label">طلبات مكتملة</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon" style="background:#fce4ec;color:#c62828;">❌</div>
            <div>
                <p class="stat-value">{{ $provider->cancelled_orders }}</p>
                <p class="stat-label">طلبات ملغاة</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;">📊</div>
            <div>
                <p class="stat-value">{{ $completionRate }}%</p>
                <p class="stat-label">نسبة الإنجاز</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon" style="background:#fff3e0;color:#e65100;">⭐</div>
            <div>
                <p class="stat-value">{{ ($provider->real_total_ratings ?? 0) > 0 ? number_format($provider->real_avg_rating, 1) : '—' }}</p>
                <p class="stat-label">التقييم ({{ $provider->real_total_ratings ?? 0 }} تقييم)</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">📋</div>
            <div>
                <p class="stat-value">{{ $provider->total_orders }}</p>
                <p class="stat-label">إجمالي الطلبات</p>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">💰</div>
            <div>
                <p class="stat-value">{{ number_format($totalRevenue, 2) }}</p>
                <p class="stat-label">إجمالي الإيرادات (د.ل)</p>
            </div>
        </article>
    </div>

    {{-- Provider Info --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">معلومات المزود</span>
            <span class="badge" style="background:{{ $vs['bg'] }};color:{{ $vs['fg'] }};">{{ $vs['label'] }}</span>
        </div>
        <div class="card-body" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <tbody>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;width:160px;">المعرّف</td>
                        <td style="padding:12px 20px;font-size:13.5px;">#{{ $provider->id }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">الاسم</td>
                        <td style="padding:12px 20px;font-size:13.5px;font-weight:600;">{{ $provider->user->name ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">رقم الجوال</td>
                        <td style="padding:12px 20px;font-size:13.5px;" dir="ltr">{{ $provider->user->phone ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">نوع الخدمة</td>
                        <td style="padding:12px 20px;">
                            <span class="badge badge-primary">
                                {{ $provider->service_type === 'fuel_delivery' ? '⛽ توصيل وقود' : '🚛 خدمة سحب' }}
                            </span>
                        </td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">المركبة</td>
                        <td style="padding:12px 20px;font-size:13.5px;">
                            {{ $provider->vehicle_make }} {{ $provider->vehicle_model }}
                            @if($provider->vehicle_year) ({{ $provider->vehicle_year }}) @endif
                            @if($provider->vehicle_color) — {{ $provider->vehicle_color }} @endif
                        </td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">لوحة المركبة</td>
                        <td style="padding:12px 20px;font-size:13.5px;font-weight:600;" dir="ltr">{{ $provider->vehicle_plate }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">متاح للعمل</td>
                        <td style="padding:12px 20px;">
                            @if($provider->is_available)
                                <span class="badge" style="background:#d4edda;color:#155724;">متاح</span>
                            @else
                                <span class="badge" style="background:#f5f5f5;color:#999;">غير متاح</span>
                            @endif
                        </td>
                    </tr>
                    @if($provider->verified_at)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">تاريخ التوثيق</td>
                        <td style="padding:12px 20px;font-size:13px;color:var(--text-muted);">
                            {{ $provider->verified_at->format('Y-m-d H:i') }}
                            @if($provider->verifiedBy)
                                — بواسطة {{ $provider->verifiedBy->name }}
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($provider->rejection_reason)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">سبب الرفض</td>
                        <td style="padding:12px 20px;font-size:13.5px;color:#c62828;">{{ $provider->rejection_reason }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding:12px 20px;font-weight:700;color:var(--text-muted);font-size:13px;">تاريخ التسجيل</td>
                        <td style="padding:12px 20px;font-size:13px;color:var(--text-muted);">{{ $provider->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Documents --}}
    @if($provider->documents->isNotEmpty())
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <span class="card-title">الوثائق المرفقة</span>
            <span class="badge badge-primary">{{ $provider->documents->count() }}</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
                @foreach($provider->documents as $doc)
                <div style="border:1px solid var(--border);border-radius:var(--radius);padding:12px;text-align:center;">
                    <p style="font-size:12px;font-weight:600;margin-bottom:8px;">{{ $doc->document_type }}</p>
                    @if($doc->file_path)
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-ghost btn-sm">عرض الملف</a>
                    @else
                        <span style="font-size:12px;color:var(--text-muted);">لا يوجد ملف</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Recent Orders --}}
    @if($recentOrders->isNotEmpty())
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <span class="card-title">آخر الطلبات</span>
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
                    @foreach($recentOrders as $order)
                    @php $os = $statusMap[$order->status] ?? ['label' => $order->status, 'bg' => '#eee', 'fg' => '#333']; @endphp
                    <tr>
                        <td class="td-muted td-mono">#{{ $order->id }}</td>
                        <td>{{ $order->driver->name ?? '—' }}</td>
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
        <a href="{{ route('admin.providers.index') }}" class="btn btn-secondary">← رجوع للقائمة</a>
        @if($provider->verification_status === 'pending' || $provider->verification_status === 'appointment_set')
            <form method="POST" action="{{ route('admin.providers.approve', $provider) }}" onsubmit="return confirm('هل تريد اعتماد هذا المزود؟')">
                @csrf @method('PATCH')
                <button class="btn btn-primary">اعتماد المزود</button>
            </form>
            <form method="POST" action="{{ route('admin.providers.reject', $provider) }}" onsubmit="return confirm('هل تريد رفض هذا المزود؟')">
                @csrf @method('PATCH')
                <input type="hidden" name="rejection_reason" value="لم يستوفِ الشروط المطلوبة">
                <button class="btn btn-danger">رفض</button>
            </form>
        @endif
    </div>
@endsection
