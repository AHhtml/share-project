<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إنشاء حساب — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body style="min-height: 100vh; display: flex; flex-direction: column; overflow-y: auto;">

<header class="topbar">
  <div class="container topbar-inner">
    <a href="{{ url('/') }}" class="brand"><span class="beacon-dot"></span> منارة</a>
    <button class="theme-toggle" id="themeToggle" aria-label="تبديل الوضع الليلي"><span class="knob">🌙</span></button>
  </div>
</header>

<main class="login-wrap" style="padding: 40px 15px; margin: auto 0; display: flex; justify-content: center; align-items: flex-start; min-height: calc(100vh - 80px);">
  <div class="login-card card" style="width:min(620px, 100%); opacity: 1; visibility: visible; transform: none; margin: 0 auto;">
    <h1>إنشاء حساب جديد</h1>
    <p class="login-sub">اختر نوع الحساب المناسب لك، وأكمل بياناتك لإنشائه.</p>

    <!-- بطاقات اختيار الدور -->
    <div class="role-grid" id="roleGrid">
      <button class="role-pick active" type="button" data-role="student">
        <span class="glyph">🎓</span>
        <strong>طالب</strong>
      </button>
      <button class="role-pick" type="button" data-role="teacher">
        <span class="glyph">📚</span>
        <strong>معلم</strong>
      </button>
      <button class="role-pick" type="button" data-role="management">
        <span class="glyph">🧭</span>
        <strong>الإدارة</strong>
      </button>
      <button class="role-pick" type="button" data-role="admin">
        <span class="glyph">🗝️</span>
        <strong>مدير المركز</strong>
      </button>
    </div>

    @if ($errors->any())
      <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 14px;">
        <ul style="margin:0; padding-right:20px; text-align:right;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('register') }}" method="POST" id="registerForm" class="login-form">
      @csrf

      <input type="hidden" name="role" id="selectedRole" value="{{ old('role', 'student') }}">

      <div class="field">
        <label for="fullName">الاسم الكامل</label>
        <input type="text" name="name" id="fullName" value="{{ old('name') }}" placeholder="مثال: محمد أحمد" required>
      </div>

      <div class="field">
        <label for="regEmail">البريد الإلكتروني</label>
        <input type="email" name="email" id="regEmail" value="{{ old('email') }}" placeholder="name@example.com" required>
      </div>

      <!-- حقل اختيار المادة للمعلم (مادة واحدة) -->
      <div class="field" id="teacherSubjectField" style="display: none;">
        <label for="regSubject">المادة الدراسية (خاص بالمعلمين)</label>
        <select name="subject_id" id="regSubject" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color, #ccc); background-color: var(--bg-card, #fff); color: var(--text-color, #333);">
          <option value="">اختر المادة الدراسية...</option>
          @foreach(\App\Models\Subject::all() as $subject)
            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
              {{ $subject->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- حقل اختيار المواد للطالب (أكثر من مادة) -->
      <div class="field" id="studentSubjectsField" style="display: block;">
        <label>المواد الدراسية المراد التسجيل بها (خاص بالطلاب)</label>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-top: 8px; padding: 12px; border: 1px solid var(--border-color, #ccc); border-radius: 6px; background-color: var(--bg-card, #fff);">
          @foreach(\App\Models\Subject::all() as $subject)
            <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer;">
              <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" {{ is_array(old('subject_ids')) && in_array($subject->id, old('subject_ids')) ? 'checked' : '' }}>
              {{ $subject->name }}
            </label>
          @endforeach
        </div>
      </div>

      <div class="field">
        <label for="regPhone">رقم الهاتف (اختياري)</label>
        <input type="tel" name="phone" id="regPhone" value="{{ old('phone') }}" placeholder="05xxxxxxxx">
      </div>

      <div class="field">
        <label for="regPassword">كلمة المرور</label>
        <input type="password" name="password" id="regPassword" placeholder="••••••••" minlength="6" required>
      </div>

      <div class="field">
        <label for="regConfirm">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" id="regConfirm" placeholder="••••••••" minlength="6" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">إنشاء الحساب</button>

      <p class="hint" style="margin-top: 15px; text-align: center;">
        لديك حساب مسبقاً؟ 
        <a href="{{ route('login') }}" style="color:var(--beacon-dim); font-weight:700;">سجّل الدخول</a>
      </p>
    </form>
  </div>
</main>

<script src="{{ asset('js/theme.js') }}"></script>
<script>
  if (typeof ManaraTheme !== 'undefined') {
    ManaraTheme.init(document.getElementById("themeToggle"));
  }

  const roleInput = document.getElementById("selectedRole");
  const teacherSubjectField = document.getElementById("teacherSubjectField");
  const studentSubjectsField = document.getElementById("studentSubjectsField");

  function toggleFieldsByRole(role) {
    if (role === "teacher") {
      teacherSubjectField.style.display = "block";
      studentSubjectsField.style.display = "none";
    } else if (role === "student") {
      teacherSubjectField.style.display = "none";
      studentSubjectsField.style.display = "block";
    } else {
      teacherSubjectField.style.display = "none";
      studentSubjectsField.style.display = "none";
    }
  }

  // التبديل عند الضغط على أزرار الأدوار
  document.querySelectorAll(".role-pick").forEach((btn) => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".role-pick").forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      
      const chosenRole = btn.dataset.role;
      roleInput.value = chosenRole;
      toggleFieldsByRole(chosenRole);
    });
  });

  // فحص الحالة المبدئية عند التحميل
  document.addEventListener("DOMContentLoaded", () => {
    const initialRole = roleInput.value;
    const activeBtn = document.querySelector(`.role-pick[data-role="${initialRole}"]`);
    if (activeBtn) {
      document.querySelectorAll(".role-pick").forEach((b) => b.classList.remove("active"));
      activeBtn.classList.add("active");
    }
    toggleFieldsByRole(initialRole);
  });
</script>
</body>
</html>