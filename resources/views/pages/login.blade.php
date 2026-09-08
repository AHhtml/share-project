<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<header class="topbar">
  <div class="container topbar-inner">
    <a href="{{ url('/') }}" class="brand"><span class="beacon-dot"></span> منارة</a>
    <button class="theme-toggle" id="themeToggle" aria-label="تبديل الوضع الليلي"><span class="knob">🌙</span></button>
  </div>
</header>

<main class="login-wrap">
  <div class="login-card card" style="display: block; opacity: 1; visibility: visible;">
    <h1>مرحباً بعودتك</h1>
    <p class="login-sub">أدخل بريدك الإلكتروني وكلمة المرور للدخول إلى حسابك الخاص.</p>

    <!-- عرض أخطاء تسجيل الدخول -->
    @if ($errors->any())
      <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 14px;">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="login-form" style="border-top:none; padding-top:0;">
      @csrf

      <div class="field">
        <label for="email">البريد الإلكتروني</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="name@manara.edu" required>
      </div>

      <div class="field">
        <label for="password">كلمة المرور</label>
        <input type="password" name="password" id="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">تسجيل الدخول</button>
      
      <p class="hint" style="margin-top: 15px; text-align: center;">
        ليس لديك حساب؟ 
        <a href="{{ route('register') }}" style="color:var(--beacon-dim); font-weight:700; text-decoration: underline;">أنشئ حساباً جديداً</a>
      </p>
    </form>
  </div>
</main>

<script src="{{ asset('js/theme.js') }}"></script>
<script>
  if (typeof ManaraTheme !== 'undefined') {
    ManaraTheme.init(document.getElementById("themeToggle"));
  }
</script>
</body>
</html>