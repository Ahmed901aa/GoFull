<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول — لوحة تحكم GoFull</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #004B3B;
            --primary-hover: #006B54;
            --primary-light: #e8f5f2;
            --accent: #4ADE80;
        }

        body {
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            display: flex;
            direction: rtl;
            background: #0a0a0a;
            overflow: hidden;
        }

        /* ── Left Panel (Branding) ── */
        .brand-panel {
            flex: 1;
            background: linear-gradient(160deg, #001a14 0%, #003329 30%, #004B3B 60%, #006B54 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        /* Animated floating orbs */
        .brand-panel .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s ease-in-out infinite;
        }
        .brand-panel .orb-1 {
            width: 500px; height: 500px;
            background: #4ADE80;
            top: -150px; left: -100px;
            animation-delay: 0s;
        }
        .brand-panel .orb-2 {
            width: 400px; height: 400px;
            background: #06b6d4;
            bottom: -100px; right: -80px;
            animation-delay: -7s;
        }
        .brand-panel .orb-3 {
            width: 300px; height: 300px;
            background: #4ADE80;
            top: 50%; left: 60%;
            animation-delay: -14s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -40px) scale(1.05); }
            66% { transform: translate(-20px, 30px) scale(0.95); }
        }

        /* Grid pattern overlay */
        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 1;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
        }

        .brand-logo-wrap {
            width: 100px;
            height: 100px;
            border-radius: 28px;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.3);
            animation: logoFloat 6s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .brand-logo {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            object-fit: contain;
        }

        .brand-title {
            font-size: 38px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 10px;
        }

        .brand-title span {
            background: linear-gradient(135deg, #4ADE80, #06d6a0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            font-size: 15px;
            color: rgba(255,255,255,0.55);
            max-width: 340px;
            line-height: 1.8;
            margin: 0 auto;
        }

        .brand-divider {
            width: 48px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), transparent);
            border-radius: 3px;
            margin: 36px auto;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 320px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,255,255,0.75);
            font-size: 14px;
            padding: 14px 18px;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px;
            transition: all 0.3s;
        }

        .brand-feature:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.1);
            transform: translateX(-4px);
        }

        .brand-feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(74,222,128,0.15), rgba(74,222,128,0.05));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-feature-icon svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: var(--accent);
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Stats row */
        .brand-stats {
            display: flex;
            gap: 32px;
            margin-top: 48px;
            padding-top: 32px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .brand-stat {
            text-align: center;
        }

        .brand-stat-num {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .brand-stat-num span {
            color: var(--accent);
            font-size: 18px;
        }

        .brand-stat-label {
            font-size: 12px;
            color: rgba(255,255,255,0.45);
            margin-top: 4px;
        }

        /* ── Right Panel (Form) ── */
        .form-panel {
            width: 520px;
            min-width: 520px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            background: #fff;
            position: relative;
        }

        /* Subtle top accent bar */
        .form-panel::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
        }

        .form-container {
            width: 100%;
            max-width: 380px;
        }

        /* Welcome badge */
        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .welcome-badge svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: var(--primary);
            stroke-width: 2;
        }

        .form-header {
            margin-bottom: 36px;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
            text-align: right;
        }

        .form-header p {
            font-size: 15px;
            color: #6b7280;
            text-align: right;
            line-height: 1.6;
        }

        /* ── Alert ── */
        .alert {
            background: linear-gradient(135deg, #fef2f2, #fff5f5);
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }

        .alert svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: #dc2626;
            stroke-width: 2;
            flex-shrink: 0;
        }

        /* ── Form Fields ── */
        .field {
            margin-bottom: 22px;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
            text-align: right;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .field-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            fill: none;
            stroke: #9ca3af;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
            transition: stroke 0.3s;
        }

        .input-wrap input {
            width: 100%;
            padding: 14px 48px 14px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-family: 'Cairo', sans-serif;
            font-size: 15px;
            color: #111827;
            background: #f9fafb;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: right;
            direction: ltr;
        }

        .input-wrap input:hover {
            border-color: #d1d5db;
        }

        .input-wrap input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0,75,59,0.08);
        }

        .input-wrap:focus-within .field-icon {
            stroke: var(--primary);
        }

        .input-wrap input::placeholder {
            color: #9ca3af;
            text-align: right;
        }

        /* ── Password toggle ── */
        .toggle-password {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .toggle-password:hover {
            background: #f3f4f6;
        }

        .toggle-password svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: #9ca3af;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.2s;
        }

        .toggle-password:hover svg {
            stroke: var(--primary);
        }

        /* ── Submit Button ── */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary), #006B54);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 12px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.1));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-login:hover {
            box-shadow: 0 8px 24px rgba(0,75,59,0.35);
            transform: translateY(-2px);
        }

        .btn-login:hover::before {
            opacity: 1;
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(0,75,59,0.25);
        }

        .btn-login svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: #fff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform 0.3s;
        }

        .btn-login:hover svg {
            transform: translateX(-4px);
        }

        /* ── Divider ── */
        .form-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 28px 0;
            color: #d1d5db;
            font-size: 12px;
        }

        .form-divider::before,
        .form-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        /* ── Security note ── */
        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            color: #9ca3af;
        }

        .security-note svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: #9ca3af;
            stroke-width: 2;
        }

        /* ── Footer ── */
        .form-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #b0b0b0;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }

        /* ── Responsive ── */
        @media (max-width: 960px) {
            .brand-panel { display: none; }
            .form-panel {
                width: 100%;
                min-width: unset;
            }
            body { background: #fff; }
        }

        /* ── Page load animation ── */
        .form-container {
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-content {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- ── Branding Panel ── -->
<div class="brand-panel">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="brand-content">
        <div class="brand-logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="GoFull" class="brand-logo">
        </div>
        <div class="brand-title">Go<span>Full</span></div>
        <div class="brand-subtitle">منصة إدارة خدمات إمداد الوقود والساحبة<br>تحكم كامل من مكان واحد</div>

        <div class="brand-divider"></div>

        <div class="brand-features">
            <div class="brand-feature">
                <div class="brand-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </div>
                إدارة الطلبات ومتابعة الحالة لحظياً
            </div>
            <div class="brand-feature">
                <div class="brand-feature-icon">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                إدارة مزودي الخدمات والعملاء
            </div>
            <div class="brand-feature">
                <div class="brand-feature-icon">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                </div>
                تقارير وإحصائيات تفصيلية
            </div>
        </div>

        <div class="brand-stats">
            <div class="brand-stat">
                <div class="brand-stat-num">24<span>/7</span></div>
                <div class="brand-stat-label">خدمة متواصلة</div>
            </div>
            <div class="brand-stat">
                <div class="brand-stat-num">100<span>%</span></div>
                <div class="brand-stat-label">تغطية شاملة</div>
            </div>
            <div class="brand-stat">
                <div class="brand-stat-num">5<span>min</span></div>
                <div class="brand-stat-label">متوسط الاستجابة</div>
            </div>
        </div>
    </div>
</div>

<!-- ── Form Panel ── -->
<div class="form-panel">
    <div class="form-container">
        <div class="welcome-badge">
            <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            لوحة تحكم آمنة
        </div>

        <div class="form-header">
            <h2>مرحباً بعودتك</h2>
            <p>أدخل بياناتك للوصول إلى لوحة التحكم</p>
        </div>

        @if($errors->any())
        <div class="alert">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="field">
                <label>رقم الهاتف</label>
                <div class="input-wrap">
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           placeholder="09XXXXXXXX" required autofocus>
                    <svg class="field-icon" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                </div>
            </div>

            <div class="field">
                <label>كلمة المرور</label>
                <div class="input-wrap">
                    <input type="password" name="password" id="password"
                           placeholder="••••••••" required>
                    <svg class="field-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <svg id="eye-icon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">
                تسجيل الدخول
                <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            </button>
        </form>

        <div class="form-divider">GoFull</div>

        <div class="security-note">
            <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            اتصال مشفر وآمن
        </div>

        <div class="form-footer">
            GoFull &copy; {{ date('Y') }} — جميع الحقوق محفوظة
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
}
</script>

</body>
</html>
