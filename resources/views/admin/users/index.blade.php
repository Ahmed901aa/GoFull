@extends('admin.layouts.app')

@section('title', 'إدارة المستخدمين')
@section('page-title', 'إدارة المستخدمين')

@section('content')
    <section class="page-card">
        <h2 class="page-heading">قائمة المستخدمين</h2>
        <p class="page-text">إدارة جميع السائقين ومزودي الخدمة المسجلين.</p>

        {{-- Search & Filters --}}
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو رقم الهاتف..." class="form-input" style="flex:1;min-width:200px;">
            <select name="role" class="form-input" style="width:140px;">
                <option value="">كل الأدوار</option>
                <option value="driver" {{ request('role') === 'driver' ? 'selected' : '' }}>سائق</option>
                <option value="provider" {{ request('role') === 'provider' ? 'selected' : '' }}>مزود خدمة</option>
            </select>
            <select name="status" class="form-input" style="width:140px;">
                <option value="">كل الحالات</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>معلّق</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">بحث</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>رقم الهاتف</th>
                        <th>الدور</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="td-muted td-mono">#{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td class="td-muted" dir="ltr">{{ $user->phone }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $user->role === 'driver' ? 'سائق' : 'مزود خدمة' }}
                            </span>
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge" style="background:#d4edda;color:#155724;">نشط</span>
                            @else
                                <span class="badge" style="background:#f8d7da;color:#721c24;">معلّق</span>
                            @endif
                        </td>
                        <td class="td-muted" style="font-size:12px;">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td style="display:flex;gap:6px;">
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
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('هل تريد حذف هذا المستخدم نهائيًا؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:#f5c6cb;color:#721c24;">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-icon">👥</div>
                            <h3>لا يوجد مستخدمون</h3>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </section>
@endsection
