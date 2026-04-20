<aside class="sidebar" id="sidebar">
    {{-- Logo --}}
    <div class="sidebar-logo">
        <img src="{{ asset('images/logo.png') }}" alt="GoFull" style="width:38px;height:38px;border-radius:10px;object-fit:contain;">
        <div class="sidebar-logo-text">Go<span>Full</span></div>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-group-label">الرئيسية</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            لوحة التحكم
        </a>

        <a href="{{ route('admin.monitor.index') }}"
           class="nav-item {{ request()->routeIs('admin.monitor*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                <path d="M12 6v6l4 2"/>
            </svg>
            المراقبة المباشرة
            @php
                $activeQuery = \App\Models\ServiceRequest::whereIn('status',['pending','accepted','en_route','arrived','in_progress']);
                if (!auth()->user()->isAdmin() && auth()->user()->employee_type) {
                    $mappedType = match(auth()->user()->employee_type) { 'fuel' => 'fuel_delivery', 'towing' => 'towing', default => null };
                    if ($mappedType) { $activeQuery->where('service_type', $mappedType); }
                }
                $active = $activeQuery->count();
            @endphp
            @if($active > 0)
                <span class="nav-badge">{{ $active }}</span>
            @endif
        </a>

        <div class="nav-group-label">المستخدمون</div>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            العملاء
        </a>

        <a href="{{ route('admin.income.index') }}"
           class="nav-item {{ request()->routeIs('admin.income*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
            دخل السائقين
        </a>

        <a href="{{ route('admin.providers.index') }}"
           class="nav-item {{ request()->routeIs('admin.providers*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            مزودو الخدمة
            @php
                $pendingQuery = \App\Models\ProviderProfile::where('verification_status','pending');
                if (!auth()->user()->isAdmin() && auth()->user()->employee_type) {
                    $mappedSvc = match(auth()->user()->employee_type) { 'fuel' => 'fuel_delivery', 'towing' => 'towing', default => null };
                    if ($mappedSvc) { $pendingQuery->where('service_type', $mappedSvc); }
                }
                $pending = $pendingQuery->count();
            @endphp
            @if($pending > 0)
                <span class="nav-badge">{{ $pending }}</span>
            @endif
        </a>

        @if(auth()->user()->role === 'admin')
        <div class="nav-group-label">الإدارة</div>

        <a href="{{ route('admin.analytics.index') }}"
           class="nav-item {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            التحليلات
        </a>

        <a href="{{ route('admin.fuel_prices.index') }}"
           class="nav-item {{ request()->routeIs('admin.fuel_prices*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            أسعار الوقود
        </a>

        <a href="{{ route('admin.settings.index') }}"
           class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>
            </svg>
            الإعدادات
        </a>

        <a href="{{ route('admin.employees.index') }}"
           class="nav-item {{ request()->routeIs('admin.employees*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            الموظفون
        </a>
        @endif

    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm" title="تسجيل الخروج" style="color:rgba(255,255,255,0.5);">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">
                    @if(auth()->user()->role === 'admin')
                        مدير النظام
                    @elseif(auth()->user()->employee_type === 'fuel')
                        موظف وقود
                    @elseif(auth()->user()->employee_type === 'towing')
                        موظف سحب
                    @else
                        موظف
                    @endif
                </div>
            </div>
            <div class="sidebar-avatar">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>
        </div>
    </div>
</aside>
