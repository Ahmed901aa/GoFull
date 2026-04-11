@extends('admin.layouts.app')

@section('title', 'أسعار الوقود')
@section('page-title', 'أسعار الوقود')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="card-title">إدارة أسعار الوقود والضريبة</span>
        </div>
        <div class="card-body" style="padding:20px;">
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
                تحديث أسعار اللتر والضريبة لأنواع الوقود المختلفة. الأسعار بالدينار الليبي.
            </p>

            @foreach($prices as $price)
            <form method="POST" action="{{ route('admin.fuel_prices.update', $price) }}" style="margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--border);">
                @csrf
                @method('PATCH')

                {{-- Row 1: Fuel type + Price + Status + Save --}}
                <div style="display:grid;grid-template-columns:1fr 180px 120px 120px;gap:16px;align-items:end;">
                    <div>
                        <label class="form-label">نوع الوقود</label>
                        <div style="padding:9px 12px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-weight:600;">
                            @if($price->fuel_type === 'petrol') ⛽ @else 🛢️ @endif
                            {{ $price->name_ar }}
                            <span class="td-muted" style="font-size:11px;font-weight:400;">({{ $price->fuel_type }})</span>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="price-{{ $price->id }}">السعر لكل لتر (د.ل)</label>
                        <input id="price-{{ $price->id }}"
                               type="number"
                               name="price_per_liter"
                               step="0.01"
                               min="0"
                               value="{{ $price->price_per_liter }}"
                               class="form-control"
                               required>
                    </div>
                    <div>
                        <label class="form-label">الحالة</label>
                        <label style="display:flex;align-items:center;gap:8px;padding:9px 12px;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" {{ $price->is_active ? 'checked' : '' }}>
                            <span>مفعّل</span>
                        </label>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" style="width:100%;">حفظ</button>
                    </div>
                </div>

                {{-- Row 2: Tax type + Tax value --}}
                <div style="display:grid;grid-template-columns:200px 200px 1fr;gap:16px;align-items:end;margin-top:12px;">
                    <div>
                        <label class="form-label" for="tax-type-{{ $price->id }}">نوع الضريبة</label>
                        <select id="tax-type-{{ $price->id }}"
                                name="tax_type"
                                class="form-control"
                                style="padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:inherit;background:var(--surface);width:100%;">
                            <option value="percentage" {{ ($price->tax_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>
                                نسبة مئوية (%)
                            </option>
                            <option value="fixed" {{ ($price->tax_type ?? 'percentage') === 'fixed' ? 'selected' : '' }}>
                                مبلغ ثابت (د.ل)
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="tax-value-{{ $price->id }}">قيمة الضريبة</label>
                        <input id="tax-value-{{ $price->id }}"
                               type="number"
                               name="tax_value"
                               step="0.01"
                               min="0"
                               value="{{ $price->tax_value ?? '0.00' }}"
                               class="form-control"
                               required>
                    </div>
                    <div style="padding:9px 0;font-size:13px;color:var(--text-muted);">
                        @if((float)($price->tax_value ?? 0) > 0)
                            الضريبة: <strong style="color:var(--text);">{{ $price->tax_label }}</strong>
                            &nbsp;|&nbsp;
                            السعر شامل الضريبة: <strong style="color:#2e7d32;">{{ number_format($price->price_with_tax, 2) }} د.ل</strong>
                        @else
                            بدون ضريبة حالياً
                        @endif
                    </div>
                </div>
            </form>
            @endforeach
        </div>
    </div>

    {{-- Preview Cards --}}
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <span class="card-title">معاينة الأسعار</span>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                @foreach($prices->where('is_active', true) as $price)
                <article class="stat-card">
                    <div class="stat-icon" style="background:{{ $price->fuel_type === 'petrol' ? '#e8f5e9' : '#fff3e0' }};color:{{ $price->fuel_type === 'petrol' ? '#2e7d32' : '#e65100' }};">
                        @if($price->fuel_type === 'petrol') ⛽ @else 🛢️ @endif
                    </div>
                    <div>
                        <p class="stat-value">{{ number_format($price->price_with_tax, 2) }}</p>
                        <p class="stat-label">{{ $price->name_ar }} (د.ل/لتر شامل الضريبة)</p>
                        @if((float)$price->tax_value > 0)
                            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">
                                السعر الأساسي: {{ number_format($price->price_per_liter, 2) }} د.ل
                                &nbsp;|&nbsp;
                                الضريبة: {{ $price->tax_label }}
                            </p>
                        @endif
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @media (max-width: 900px) {
        .card-body [style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
