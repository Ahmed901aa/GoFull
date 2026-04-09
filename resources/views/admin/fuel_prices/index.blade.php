@extends('admin.layouts.app')

@section('title', 'أسعار الوقود')
@section('page-title', 'أسعار الوقود')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="card-title">إدارة أسعار الوقود</span>
        </div>
        <div class="card-body" style="padding:20px;">
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
                تحديث أسعار اللتر لأنواع الوقود المختلفة. الأسعار بالدينار الليبي.
            </p>

            @foreach($prices as $price)
            <form method="POST" action="{{ route('admin.fuel_prices.update', $price) }}" style="margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border);">
                @csrf
                @method('PATCH')
                <div style="display:grid;grid-template-columns:1fr 200px 120px 140px;gap:16px;align-items:end;">
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
            </form>
            @endforeach
        </div>
    </div>

    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <span class="card-title">معاينة</span>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                @foreach($prices->where('is_active', true) as $price)
                <article class="stat-card">
                    <div class="stat-icon" style="background:{{ $price->fuel_type === 'petrol' ? '#e8f5e9' : '#fff3e0' }};color:{{ $price->fuel_type === 'petrol' ? '#2e7d32' : '#e65100' }};">
                        @if($price->fuel_type === 'petrol') ⛽ @else 🛢️ @endif
                    </div>
                    <div>
                        <p class="stat-value">{{ number_format($price->price_per_liter, 2) }}</p>
                        <p class="stat-label">{{ $price->name_ar }} (د.ل/لتر)</p>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </div>
@endsection
