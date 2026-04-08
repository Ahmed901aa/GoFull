@extends('admin.layouts.app')

@section('title', 'إدارة المستخدمين')
@section('page-title', 'إدارة المستخدمين')

@section('content')
    {{-- Search & Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو رقم الهاتف..." class="form-control" style="flex:1;min-width:200px;">
        <select name="role" class="form-control" style="width:140px;">
            <option value="">كل الأدوار</option>
            <option value="driver" {{ request('role') === 'driver' ? 'selected' : '' }}>عميل</option>
            <option value="provider" {{ request('role') === 'provider' ? 'selected' : '' }}>مزود خدمة</option>
        </select>
        <select name="status" class="form-control" style="width:140px;">
            <option value="">كل الحالات</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>معلّق</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">بحث</button>
    </form>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>رقم الهاتف</th>
                        <th>الدور</th>
                        <th>الطلبات المكتملة</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="td-muted td-mono">#{{ $user->id }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}" style="font-weight:600;color:var(--primary);">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td class="td-muted" dir="ltr">{{ $user->phone }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $user->role === 'driver' ? '👤 عميل' : '🚗 مزود خدمة' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background:#e8f5e9;color:#2e7d32;font-weight:700;">
                                {{ $user->completed_orders_count ?? 0 }}
                            </span>
                            <span style="font-size:11px;color:var(--text-muted);">/ {{ $user->total_orders_count ?? 0 }}</span>
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge" style="background:#d4edda;color:#155724;">نشط</span>
                            @else
                                <span class="badge" style="background:#f8d7da;color:#721c24;">معلّق</span>
                            @endif
                        </td>
                        <td class="td-muted" style="font-size:12px;">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost btn-sm">عرض</a>
                                @if($user->status === 'active')
                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" onsubmit="return confirm('هل تريد تعليق هذا المستخدم؟')">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm" style="background:#f8d7da;color:#721c24;">تعليق</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.activate', $user) }}" onsubmit="return confirm('هل تريد تفعيل هذا المستخدم؟')">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm" style="background:#d4edda;color:#155724;">تفعيل</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon">👥</div>
                            <h3>لا يوجد مستخدمون</h3>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $users->links() }}
@endsection
