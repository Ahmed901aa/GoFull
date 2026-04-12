@extends('admin.layouts.app')

@section('title', 'الإعدادات')
@section('page-title', 'الإعدادات')

@section('content')

    {{-- Service Fee Stats --}}
    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);margin-bottom:20px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;">💰</div>
            <div>
                <div class="stat-value">{{ number_format($totalServiceFees, 2) }}</div>
                <div class="stat-label">إجمالي رسوم الخدمة المحصّلة (د.ل)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3e0;color:#e65100;">📅</div>
            <div>
                <div class="stat-value">{{ number_format($todayServiceFees, 2) }}</div>
                <div class="stat-label">رسوم الخدمة اليوم (د.ل)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede7f6;color:#4a148c;">📋</div>
            <div>
                <div class="stat-value">{{ $completedOrders }}</div>
                <div class="stat-label">إجمالي الطلبات المكتملة</div>
            </div>
        </div>
    </div>

    {{-- Service Fee Control --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">رسوم الخدمة</span>
        </div>
        <div class="card-body" style="padding:20px;">
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
                رسوم الخدمة هي مبلغ ثابت يُضاف تلقائيًا إلى كل طلب عند إكماله (وقود أو سحب). يتم تحصيلها من السائق.
            </p>

            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PATCH')

                <div style="display:grid;grid-template-columns:300px 200px 1fr;gap:16px;align-items:end;">
                    <div>
                        <label class="form-label" for="service_fee">رسوم الخدمة لكل طلب (د.ل)</label>
                        <input id="service_fee"
                               type="number"
                               name="service_fee"
                               step="0.01"
                               min="0"
                               value="{{ $serviceFee }}"
                               class="form-control"
                               style="font-size:18px;font-weight:700;padding:12px 16px;"
                               required>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;">حفظ</button>
                    </div>
                    <div style="padding:12px 0;font-size:13px;color:var(--text-muted);">
                        القيمة الحالية: <strong style="color:#2e7d32;font-size:16px;">{{ number_format($serviceFee, 2) }} د.ل</strong> لكل طلب
                    </div>
                </div>

                @error('service_fee')
                    <p style="color:var(--danger);font-size:13px;margin-top:8px;">{{ $message }}</p>
                @enderror
            </form>
        </div>
    </div>

    {{-- Explanation --}}
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <span class="card-title">كيف تعمل رسوم الخدمة؟</span>
        </div>
        <div class="card-body" style="padding:20px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div style="padding:16px;background:var(--surface-2);border-radius:var(--radius-sm);border:1px solid var(--border);">
                    <div style="font-weight:700;margin-bottom:8px;font-size:15px;">⛽ طلب وقود</div>
                    <div style="font-size:13px;color:var(--text-muted);line-height:1.8;">
                        سعر اللتر × الكمية = المبلغ الفرعي<br>
                        المبلغ الفرعي + <strong style="color:#2e7d32;">رسوم الخدمة ({{ number_format($serviceFee, 2) }} د.ل)</strong> = الإجمالي
                    </div>
                </div>
                <div style="padding:16px;background:var(--surface-2);border-radius:var(--radius-sm);border:1px solid var(--border);">
                    <div style="font-weight:700;margin-bottom:8px;font-size:15px;">🚛 طلب سحب</div>
                    <div style="font-size:13px;color:var(--text-muted);line-height:1.8;">
                        سعر السحب الأساسي = المبلغ الفرعي<br>
                        المبلغ الفرعي + <strong style="color:#2e7d32;">رسوم الخدمة ({{ number_format($serviceFee, 2) }} د.ل)</strong> = الإجمالي
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: 1fr !important; }
        .card-body [style*="grid-template-columns"] { grid-template-columns: 1fr !important; }
    }
</style>
@endpush
