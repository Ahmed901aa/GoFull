<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') — GoFull</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        body { font-family: 'Cairo', sans-serif; }

        /* RTL fixes */
        .sidebar { left: auto; right: 0; border-right: none; border-left: 1px solid var(--border); }
        .main-content { margin-left: 0; margin-right: var(--sidebar-width); }
        .nav-badge { margin-left: 0; margin-right: auto; }
        thead th { text-align: right; }
        .page-header { flex-direction: row-reverse; }
        .sidebar-user { flex-direction: row-reverse; }
        .card-header { flex-direction: row-reverse; }
        .alert { flex-direction: row-reverse; }
        .btn { flex-direction: row-reverse; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); right: 0; left: auto; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-layout">
    @include('admin.partials.sidebar')
    <div class="main-content">
        @include('admin.partials.navbar')
        <div class="page-content">
            @include('admin.partials.alerts')
            @yield('content')
        </div>
    </div>
</div>
@stack('scripts')
<script>
    document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('open');
    });
</script>
</body>
</html>
