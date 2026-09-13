<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تعديل بيانات الطالب — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
  .collapsible-menu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
    opacity: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .collapsible-menu.open {
    max-height: 300px;
    opacity: 1;
  }
</style>
</head>
<body data-home="{{ url('/') }}" data-login="{{ route('login') }}">

<div class="mobile-topbar">
  <button id="menuToggle" class="btn-ghost" style="color:#fff; font-size:1.3rem; background:none; border:none; cursor:pointer;">☰</button>
  <strong id="mobileTitle">منارة</strong>
  <span></span>
</div>
<div class="sidebar-scrim" id="sidebarScrim"></div>

<div class="app-shell">
  <!-- القائمة الجانبية للإدارة -->
  <aside class="sidebar" id="sidebar">
    <div style="padding: 20px;">
      <p style="margin-bottom: 5px; font-weight: bold;">{{ Auth::user()->name }}</p>
      <p style="font-size: 0.85rem; color: #d97706; margin-bottom: 15px;">الإدارة</p>
      
      <nav class="sidebar-nav" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px;">
        <a href="{{ route('management.students.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">← عودة لقائمة الطلاب</a>
      </nav>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #dc3545; border: none;">تسجيل الخروج</button>
      </form>
    </div>
  </aside>

  <main class="main">
    <section data-section="edit-student">
      <div class="main-head">
        <div>
          <h1>تعديل بيانات الطالب: {{ $student->name }}</h1>
          <p>قم بتعديل معلومات الطالب الأساسية أو تغيير المساق الدراسي المرتبط به.</p>
        </div>
        <a href="{{ route('management.students.index') }}" class="btn btn-ghost" style="border: 1px solid #cbd5e1; text-decoration: none;">← عودة</a>
      </div>

      @if($errors->any())
        <div style="background: #f8d7da; color: #842029; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      <div class="card section-card" style="max-width: 600px; padding: 30px;">
        <form action="{{ route('management.students.update', $student->id) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="field" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">اسم الطالب الكامل</label>
            <input type="text" name="name" value="{{ old('name', $student->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem;">
          </div>

          <div class="field" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email', $student->email) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem;">
          </div>

          <div class="field" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">كلمة المرور الجديدة (اختياري)</label>
            <input type="password" name="password" placeholder="اتركها فارغة إذا لم تقم برغبة تغييرها" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem;">
          </div>

          <!-- حقل اختيار وتعديل المساق الدراسي -->
          <div class="field" style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">المساق الدراسي</label>
            <select name="subject_id" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; background: #fff;">
              <option value="">-- بدون مساق --</option>
              @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ optional($student->subjects->first())->id == $subject->id ? 'selected' : '' }}>
                  {{ $subject->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('management.students.index') }}" class="btn btn-ghost" style="border: 1px solid #cbd5e1; text-decoration: none; padding: 10px 20px; display: inline-flex; align-items: center;">إلغاء</a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 25px;">حفظ كافة التعديلات</button>
          </div>
        </form>
      </div>
    </section>
  </main>
</div>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
</body>
</html>