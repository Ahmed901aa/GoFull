<header class="navbar">
    <div class="navbar-title">
        <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="20" height="20" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:8px;">
            @switch(request()->route()->getName())
                @case('admin.dashboard')
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                    @break
                @case('admin.monitor.index')
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                    @break
                @default
                    <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
            @endswitch
        </svg>
        @yield('title', 'لوحة التحكم')
    </div>

    <div class="navbar-actions">
        <div style="display:flex;align-items:center;gap:8px;background:var(--primary-faint);padding:6px 14px;border-radius:99px;font-size:12px;color:var(--primary);font-weight:600;">
            <span class="live-dot"></span>
            مباشر
        </div>
        <div style="font-size:12px;color:var(--text-faint);padding:0 8px;font-family:'Cairo',sans-serif;display:flex;align-items:center;gap:6px;">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" width="14" height="14" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            {{ now()->locale('ar')->isoFormat('D MMMM YYYY') }}
        </div>
    </div>
</header>
