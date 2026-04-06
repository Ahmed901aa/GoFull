@extends('admin.layouts.app')

@section('title', 'المراقبة المباشرة')
@section('page-title', 'المراقبة المباشرة')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">الطلبات النشطة</h2>
        <p class="page-text">مراقبة جميع الطلبات الجارية حاليًا في النظام.</p>

        @php
            $statusMap = [
                'pending'     => ['label' => 'قيد الانتظار', 'bg' => '#fff3cd', 'fg' => '#856404'],
                'accepted'    => ['label' => 'مقبول',         'bg' => '#cce5ff', 'fg' => '#004085'],
                'en_route'    => ['label' => 'في الطريق',    'bg' => '#d4edda', 'fg' => '#155724'],
                'arrived'     => ['label' => 'وصل',           'bg' => '#d1ecf1', 'fg' => '#0c5460'],
                'in_progress' => ['label' => 'جاري التنفيذ', 'bg' => '#e2d5f1', 'fg' => '#4a148c'],
            ];
        @endphp

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>السائق (العميل)</th>
                        <th>مزود الخدمة</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th>الموقع</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    @php $s = $statusMap[$req->status] ?? ['label' => $req->status, 'bg' => '#eee', 'fg' => '#333']; @endphp
                    <tr>
                        <td class="td-muted td-mono">#{{ $req->id }}</td>
                        <td>{{ $req->driver->name ?? '—' }}</td>
                        <td class="td-muted">{{ $req->provider->user->name ?? 'غير معيّن' }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $req->service_type === 'fuel_delivery' ? '⛽ وقود' : '🚛 سحب' }}
                            </span>
                        </td>
                        <td><span class="badge" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }};">{{ $s['label'] }}</span></td>
                        <td class="td-muted" style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $req->driver_address ?? '—' }}
                        </td>
                        <td class="td-muted" style="font-size:12px;">
                            {{ $req->created_at->locale('ar')->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-icon">✅</div>
                            <h3>لا توجد طلبات نشطة حاليًا</h3>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $requests->links() }}
    </section>
@endsection
