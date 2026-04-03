<header class="navbar">
    <div class="navbar-title">@yield('title', 'لوحة التحكم')</div>

    <div class="navbar-actions">
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted)">
            <span style="width:7px;height:7px;border-radius:99px;background:var(--primary);display:inline-block;animation:pulse 2s infinite"></span>
            مباشر
        </div>
        <div style="font-size:12px;color:var(--text-faint);padding:0 8px;font-family:'Cairo',sans-serif">
            {{ now()->locale('ar')->isoFormat('D MMMM YYYY') }}
        </div>
    </div>
</header>

<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
</style>
