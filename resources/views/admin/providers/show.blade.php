@extends('admin.layouts.app')

@section('title', ($provider->user->name ?? 'مزود الخدمة') . ' — تفاصيل')

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

{{-- ══════════════════════════════════════════════════════════
     HEADER — Profile Card
     ══════════════════════════════════════════════════════════ --}}
<div class="prov-header">
    <div class="prov-avatar">
        {{ mb_substr($provider->user->name ?? '?', 0, 1) }}
    </div>
    <div class="prov-header-info">
        <h2>{{ $provider->user->name ?? '—' }}</h2>
        <div class="prov-header-meta">
            <span class="badge" style="background:{{ $vs['bg'] }};color:{{ $vs['fg'] }};">{{ $vs['label'] }}</span>
            <span class="badge badge-primary">
                {{ $provider->service_type === 'fuel_delivery' ? '⛽ توصيل وقود' : '🚛 خدمة سحب' }}
            </span>
            @if($provider->is_available)
                <span class="badge" style="background:#d4edda;color:#155724;">متاح</span>
            @else
                <span class="badge" style="background:#f5f5f5;color:#999;">غير متاح</span>
            @endif
        </div>
        <div class="prov-header-sub">
            <span dir="ltr">{{ $provider->user->phone ?? '—' }}</span>
            <span>•</span>
            <span>تسجيل: {{ $provider->created_at->format('Y-m-d') }}</span>
            @if($provider->verified_at)
                <span>•</span>
                <span>توثيق: {{ $provider->verified_at->format('Y-m-d') }}</span>
            @endif
        </div>
    </div>
    <div class="prov-header-actions">
        @if($provider->verification_status === 'pending' || $provider->verification_status === 'appointment_set')
            <form method="POST" action="{{ route('admin.providers.approve', $provider) }}" onsubmit="return confirm('هل تريد اعتماد هذا المزود؟')">
                @csrf @method('PATCH')
                <button class="btn btn-primary btn-sm">اعتماد</button>
            </form>
            <form method="POST" action="{{ route('admin.providers.reject', $provider) }}" onsubmit="return confirm('هل تريد رفض هذا المزود؟')">
                @csrf @method('PATCH')
                <input type="hidden" name="rejection_reason" value="لم يستوفِ الشروط المطلوبة">
                <button class="btn btn-danger btn-sm">رفض</button>
            </form>
        @endif
        <a href="{{ route('admin.providers.index') }}" class="btn btn-secondary btn-sm">← رجوع</a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 1 — Revenue Cards
     ══════════════════════════════════════════════════════════ --}}
<div class="prov-row" style="grid-template-columns:repeat(4,1fr);">
    <div class="prov-metric prov-metric-green">
        <div class="prov-metric-icon">💰</div>
        <div class="prov-metric-value">{{ number_format($totalRevenue, 2) }}</div>
        <div class="prov-metric-label">إجمالي الإيرادات (د.ل)</div>
    </div>
    <div class="prov-metric prov-metric-blue">
        <div class="prov-metric-icon">📅</div>
        <div class="prov-metric-value">{{ number_format($todayRevenue, 2) }}</div>
        <div class="prov-metric-label">إيرادات اليوم (د.ل)</div>
    </div>
    <div class="prov-metric prov-metric-purple">
        <div class="prov-metric-icon">📊</div>
        <div class="prov-metric-value">{{ number_format($weekRevenue, 2) }}</div>
        <div class="prov-metric-label">إيرادات الأسبوع (د.ل)</div>
    </div>
    <div class="prov-metric prov-metric-gold">
        <div class="prov-metric-icon">🏦</div>
        <div class="prov-metric-value">{{ number_format($monthRevenue, 2) }}</div>
        <div class="prov-metric-label">إيرادات الشهر (د.ل)</div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 2 — Order Stats
     ══════════════════════════════════════════════════════════ --}}
<div class="prov-row" style="grid-template-columns:repeat(6,1fr);">
    <div class="stat-card">
        <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">📋</div>
        <div>
            <p class="stat-value">{{ $provider->total_orders }}</p>
            <p class="stat-label">إجمالي الطلبات</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">✅</div>
        <div>
            <p class="stat-value">{{ $provider->completed_orders }}</p>
            <p class="stat-label">مكتملة</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fce4ec;color:#c62828;">❌</div>
        <div>
            <p class="stat-value">{{ $provider->cancelled_orders }}</p>
            <p class="stat-label">ملغاة</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff3e0;color:#e65100;">🔴</div>
        <div>
            <p class="stat-value">{{ $provider->active_orders }}</p>
            <p class="stat-label">نشطة الآن</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;">📊</div>
        <div>
            <p class="stat-value">{{ $completionRate }}%</p>
            <p class="stat-label">نسبة الإنجاز</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3e5f5;color:#6a1b9a;">🧾</div>
        <div>
            <p class="stat-value">{{ number_format($totalServiceFees, 2) }}</p>
            <p class="stat-label">رسوم الخدمة (د.ل)</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 3 — Personal Info + Vehicle + Rating
     ══════════════════════════════════════════════════════════ --}}
<div class="prov-row" style="grid-template-columns:1fr 1fr 1fr;">
    {{-- Personal Info --}}
    <div class="card">
        <div class="card-header"><span class="card-title">المعلومات الشخصية</span></div>
        <div class="card-body" style="padding:0;">
            <table class="prov-info-table">
                <tr>
                    <td class="prov-info-key">المعرّف</td>
                    <td>#{{ $provider->id }}</td>
                </tr>
                <tr>
                    <td class="prov-info-key">الاسم</td>
                    <td style="font-weight:600;">{{ $provider->user->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="prov-info-key">رقم الجوال</td>
                    <td dir="ltr">{{ $provider->user->phone ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="prov-info-key">نوع الخدمة</td>
                    <td>{{ $provider->service_type === 'fuel_delivery' ? '⛽ توصيل وقود' : '🚛 خدمة سحب' }}</td>
                </tr>
                <tr>
                    <td class="prov-info-key">حالة الحساب</td>
                    <td>
                        <span class="badge" style="background:{{ $vs['bg'] }};color:{{ $vs['fg'] }};">{{ $vs['label'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="prov-info-key">متاح للعمل</td>
                    <td>
                        @if($provider->is_available)
                            <span style="color:#2e7d32;font-weight:600;">نعم</span>
                        @else
                            <span style="color:#999;">لا</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="prov-info-key">تاريخ التسجيل</td>
                    <td>{{ $provider->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @if($provider->verified_at)
                <tr>
                    <td class="prov-info-key">تاريخ التوثيق</td>
                    <td>{{ $provider->verified_at->format('Y-m-d H:i') }}
                        @if($provider->verifiedBy) — {{ $provider->verifiedBy->name }} @endif
                    </td>
                </tr>
                @endif
                @if($provider->rejection_reason)
                <tr>
                    <td class="prov-info-key">سبب الرفض</td>
                    <td style="color:#c62828;">{{ $provider->rejection_reason }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Vehicle Info --}}
    <div class="card">
        <div class="card-header"><span class="card-title">معلومات المركبة</span></div>
        <div class="card-body" style="padding:0;">
            <table class="prov-info-table">
                <tr>
                    <td class="prov-info-key">الشركة المصنعة</td>
                    <td style="font-weight:600;">{{ $provider->vehicle_make }}</td>
                </tr>
                <tr>
                    <td class="prov-info-key">الموديل</td>
                    <td>{{ $provider->vehicle_model }}</td>
                </tr>
                <tr>
                    <td class="prov-info-key">سنة الصنع</td>
                    <td>{{ $provider->vehicle_year ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="prov-info-key">لوحة المركبة</td>
                    <td style="font-weight:700;letter-spacing:1px;" dir="ltr">{{ $provider->vehicle_plate }}</td>
                </tr>
                <tr>
                    <td class="prov-info-key">اللون</td>
                    <td>{{ $provider->vehicle_color ?? '—' }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Rating Summary --}}
    <div class="card">
        <div class="card-header"><span class="card-title">التقييم</span></div>
        <div class="card-body">
            <div style="text-align:center;margin-bottom:16px;">
                <div style="font-size:42px;font-weight:800;color:var(--text);line-height:1;">
                    {{ ($provider->real_total_ratings ?? 0) > 0 ? number_format($provider->real_avg_rating, 1) : '—' }}
                </div>
                <div style="font-size:18px;color:#e65100;margin:4px 0;">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($provider->real_avg_rating ?? 0))
                            ★
                        @else
                            ☆
                        @endif
                    @endfor
                </div>
                <div style="font-size:12px;color:var(--text-muted);">{{ $provider->real_total_ratings ?? 0 }} تقييم</div>
            </div>

            {{-- Star Distribution --}}
            @for($star = 5; $star >= 1; $star--)
            @php $count = $ratingDistribution[$star] ?? 0; $pct = ($provider->real_total_ratings ?? 0) > 0 ? round(($count / $provider->real_total_ratings) * 100) : 0; @endphp
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <span style="font-size:12px;width:18px;text-align:center;color:var(--text-muted);">{{ $star }}</span>
                <span style="font-size:12px;color:#e65100;">★</span>
                <div style="flex:1;height:6px;background:#f0f0f0;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:#e65100;border-radius:99px;"></div>
                </div>
                <span style="font-size:11px;color:var(--text-muted);width:30px;">{{ $count }}</span>
            </div>
            @endfor
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 4 — Recent Ratings + Documents
     ══════════════════════════════════════════════════════════ --}}
<div class="prov-row" style="grid-template-columns:1fr 1fr;">
    {{-- Recent Ratings --}}
    <div class="card">
        <div class="card-header"><span class="card-title">آخر التقييمات</span></div>
        @if($recentRatings->isNotEmpty())
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
            @foreach($recentRatings as $r)
            <div style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span style="font-weight:600;font-size:13px;">{{ $r->driver_name }}</span>
                    <span style="font-size:12px;color:#e65100;">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= $r->rating ? '★' : '☆' }}
                        @endfor
                    </span>
                </div>
                @if($r->comment)
                    <p style="font-size:12px;color:var(--text-muted);margin:0;">{{ $r->comment }}</p>
                @endif
                <div style="font-size:11px;color:var(--text-faint);margin-top:4px;">
                    {{ \Carbon\Carbon::parse($r->created_at)->locale('ar')->diffForHumans() }}
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="card-body">
            <div class="empty-state" style="padding:20px;"><h3>لا توجد تقييمات بعد</h3></div>
        </div>
        @endif
    </div>

    {{-- Documents --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">الوثائق المرفقة</span>
            @if($provider->documents->isNotEmpty())
                <span class="badge badge-primary">{{ $provider->documents->count() }}</span>
            @endif
        </div>
        <div class="card-body">
            @if($provider->documents->isNotEmpty())
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;">
                @foreach($provider->documents as $doc)
                <div style="border:1px solid var(--border);border-radius:var(--radius);padding:14px;text-align:center;">
                    <div style="font-size:28px;margin-bottom:8px;">📄</div>
                    <p style="font-size:12px;font-weight:600;margin-bottom:8px;">{{ $doc->document_type }}</p>
                    @if($doc->file_path)
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;">عرض الملف</a>
                    @else
                        <span style="font-size:12px;color:var(--text-muted);">لا يوجد ملف</span>
                    @endif
                </div>
                @endforeach
            </div>
            @else
                <div class="empty-state" style="padding:20px;"><h3>لا توجد وثائق مرفقة</h3></div>
            @endif
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 5 — All Orders (paginated)
     ══════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">جميع الطلبات</span>
        <span style="font-size:12px;color:var(--text-faint);">{{ $recentOrders->total() }} طلب</span>
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
                    <th>رسوم الخدمة</th>
                    <th>الدفع</th>
                    <th>التقييم</th>
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
                @forelse($recentOrders as $order)
                @php $os = $statusMap[$order->status] ?? ['label' => $order->status, 'bg' => '#eee', 'fg' => '#333']; @endphp
                <tr>
                    <td class="td-muted td-mono">#{{ $order->id }}</td>
                    <td style="font-weight:600;">{{ $order->driver->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-primary">
                            {{ $order->service_type === 'fuel_delivery' ? '⛽' : '🚛' }}
                        </span>
                    </td>
                    <td><span class="badge" style="background:{{ $os['bg'] }};color:{{ $os['fg'] }};">{{ $os['label'] }}</span></td>
                    <td style="font-weight:600;">{{ $order->total ? number_format($order->total, 2) . ' د.ل' : '—' }}</td>
                    <td class="td-muted">{{ $order->service_fee ? number_format($order->service_fee, 2) : '—' }}</td>
                    <td>
                        @if($order->payment_status === 'paid')
                            <span class="badge" style="background:#d4edda;color:#155724;">مدفوع</span>
                        @else
                            <span class="badge" style="background:#fff3cd;color:#856404;">معلّق</span>
                        @endif
                    </td>
                    <td>
                        @if($order->rating)
                            <span style="color:#e65100;font-weight:600;">{{ $order->rating->rating }}★</span>
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                    <td class="td-muted" style="font-size:12px;">{{ $order->created_at->format('m/d H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state" style="padding:30px;">
                        <h3>لا توجد طلبات بعد</h3>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $recentOrders->links() }}
</div>

@endsection

@push('styles')
<style>
    .prov-row {
        display: grid;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* ── Header ──────────────── */
    .prov-header {
        background: linear-gradient(135deg, var(--primary) 0%, #006B52 100%);
        border-radius: var(--radius);
        padding: 24px 28px;
        display: flex;
        align-items: center;
        flex-direction: row-reverse;
        gap: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 16px rgba(0,75,59,0.2);
    }
    .prov-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
    }
    .prov-header-info { flex: 1; }
    .prov-header-info h2 {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .prov-header-meta {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }
    .prov-header-sub {
        display: flex;
        gap: 8px;
        color: rgba(255,255,255,0.6);
        font-size: 12px;
        flex-wrap: wrap;
    }
    .prov-header-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    /* ── Metric Cards ────────── */
    .prov-metric {
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        background: var(--surface);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
    }
    .prov-metric:hover { transform: translateY(-2px); }
    .prov-metric::after {
        content: '';
        position: absolute;
        left: 0; bottom: 0;
        width: 100%; height: 3px;
    }
    .prov-metric-green { border: 1px solid #c8e6c9; }
    .prov-metric-green::after { background: #2e7d32; }
    .prov-metric-blue { border: 1px solid #bbdefb; }
    .prov-metric-blue::after { background: #1565c0; }
    .prov-metric-purple { border: 1px solid #d1c4e9; }
    .prov-metric-purple::after { background: #4a148c; }
    .prov-metric-gold { border: 1px solid #ffe0b2; }
    .prov-metric-gold::after { background: #e65100; }

    .prov-metric-icon { font-size: 20px; margin-bottom: 8px; }
    .prov-metric-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
        margin-bottom: 4px;
    }
    .prov-metric-label {
        font-size: 11px;
        color: var(--text-muted);
    }

    /* ── Info Table ───────────── */
    .prov-info-table { width: 100%; border-collapse: collapse; }
    .prov-info-table tr { border-bottom: 1px solid var(--border); }
    .prov-info-table tr:last-child { border-bottom: none; }
    .prov-info-table td { padding: 10px 20px; font-size: 13px; }
    .prov-info-key {
        font-weight: 700;
        color: var(--text-muted);
        font-size: 12px;
        width: 130px;
    }

    /* ── Responsive ───────────── */
    @media (max-width: 1100px) {
        .prov-row { grid-template-columns: 1fr 1fr !important; }
    }
    @media (max-width: 768px) {
        .prov-row { grid-template-columns: 1fr !important; }
        .prov-header {
            flex-direction: column;
            text-align: center;
        }
        .prov-header-meta { justify-content: center; }
        .prov-header-sub { justify-content: center; }
        .prov-header-actions { justify-content: center; }
    }
</style>
@endpush
