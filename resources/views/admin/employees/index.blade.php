@extends('admin.layouts.app')

@section('title', 'الموظفون')
@section('page-title', 'الموظفون')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h2 style="font-size:18px;font-weight:700;color:var(--text);margin:0;">دليل الموظفين</h2>
            <p style="font-size:13px;color:var(--text-muted);margin:4px 0 0;">إدارة حسابات الموظفين وصلاحياتهم.</p>
        </div>
        <a class="btn btn-primary" href="{{ url('/admin/employees/create') }}">+ إضافة موظف</a>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>رقم الجوال</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td class="td-muted td-mono">#{{ $emp->id }}</td>
                        <td style="font-weight:600;">{{ $emp->name }}</td>
                        <td class="td-muted">{{ $emp->phone }}</td>
                        <td>
                            <span class="badge" style="background:{{ $emp->status === 'active' ? '#e8f5e9' : '#fce4ec' }};color:{{ $emp->status === 'active' ? '#2e7d32' : '#c62828' }};">
                                {{ $emp->status === 'active' ? 'نشط' : 'معطّل' }}
                            </span>
                        </td>
                        <td class="td-muted" style="font-size:12px;">{{ $emp->created_at->locale('ar')->diffForHumans() }}</td>
                        <td>
                            <form method="POST" action="{{ url('/admin/employees/' . $emp->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <div class="empty-state-icon">👥</div>
                            <h3>لا يوجد موظفون بعد</h3>
                            <p>أضف أول موظف من الزر أعلاه.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $employees->links() }}
@endsection
