<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول — لوحة تحكم GoFull</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-wrap { width:100%; max-width:400px; padding:24px; }
        .login-logo { text-align:right; margin-bottom:32px; }
        .login-logo-icon { width:56px;height:56px;background:var(--primary);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 12px; }
        .login-logo-text { font-size:22px;font-weight:700;letter-spacing:-0.3px; }
        .login-logo-text span { color:var(--primary); }
        .login-sub { font-size:13px;color:var(--text-muted);margin-top:4px; }
        .login-card { background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-logo">
        <img src="{{ asset('images/logo.png') }}" alt="GoFull" style="width:64px;height:64px;border-radius:12px;object-fit:contain;display:block;margin:0 auto 12px;">
        <div class="login-logo-text">Go<span>Full</span> لوحة التحكم</div>
        <div class="login-sub">تسجيل الدخول إلى لوحة التحكم</div>
    </div>

    <div class="login-card">
        @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="form-control" placeholder="09XXXXXXXX" required autofocus>
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password"
                       class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:flex-end;padding:10px">
                تسجيل الدخول
            </button>
        </form>
    </div>
</div>
</body>
</html>