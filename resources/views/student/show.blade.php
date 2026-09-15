<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject->name ?? 'تفاصيل المادة' }} — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
  body { font-family: 'Tajawal', sans-serif; background: #f8fafc; color: #1e293b; margin: 0; }
  
  /* القائمة المنسدلة للمواد */
  .sub-menu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    padding-right: 15px;
    background: rgba(0, 0, 0, 0.02);
  }
  .sub-menu.open {
    max-height: 300px;
    transition: max-height 0.3s ease-in;
  }
  .sub-menu a {
    font-size: 0.88rem !important;
    padding: 8px 12px !important;
    opacity: 0.85;
  }
  .sub-menu a:hover { opacity: 1; }

  /* الشريط العلوي */
  .top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 32px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }
  .back-btn {
    text-decoration: none;
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s;
  }
  .back-btn:hover { color: #0f172a; }

  /* بانر تفاصيل المادة */
  .subject-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    padding: 32px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.15);
  }
  .subject-hero h1 {
    font-size: 1.8rem;
    font-weight: 900;
    margin: 0 0 10px 0;
  }
  .subject-hero p {
    color: #94a3b8;
    font-size: 0.95rem;
    margin: 0;
    line-height: 1.6;
    max-width: 800px;
  }

  /* تنسيق الأقسام الداخلية (محاضرات، اختبارات، إعلانات) */
  .section-container {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
  }
  .section-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
  }

  /* القوائم والعناصر الداخلية */
  .item-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .list-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 16px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: border-color 0.2s;
  }
  .list-card:hover {
    border-color: #cbd5e1;
  }
  .list-info h4 {
    margin: 0 0 4px 0;
    font-size: 1rem;
    color: #0f172a;
    font-weight: 700;
  }
  .list-info p {
    margin: 0;
    font-size: 0.85rem;
    color: #64748b;
  }
  .action-link {
    background: #0f172a;
    color: #fff;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: background 0.2s;
  }
  .action-link:hover {
    background: #1e293b;
  }

  .empty-msg {
    color: #64748b;
    font-size: 0.9rem;
    text-align: center;
    padding: 20px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px dashed #cbd5e1;
  }
</style>
@stack('styles')
</head>
<body data-home="{{ url('/') }}" data-login="{{ route('login') }}">

<script>
  window.Manara = window.Manara || {
    currentUser: {
      id: {{ Auth::id() ?? 'null' }},
      name: "{{ Auth::user()->name ?? '' }}",
      email: "{{ Auth::user()->email ?? '' }}"
    }
  };
</script>

<div class="mobile-topbar">
  <button id="menuToggle" class="btn-ghost" style="color:#fff; font-size:1.3rem; background:none; border:none; cursor:pointer;">☰</button>
  <strong id="mobileTitle">منارة</strong>
  <span></span>
</div>
<div class="sidebar-scrim" id="sidebarScrim"></div>

<div class="app-shell">
  <!-- القائمة الجانبية -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <h2>منارة</h2>
      <span>لوحة الطالب</span>
    </div>
    
    <nav class="sidebar-nav">
      <a href="{{ route('student.dashboard') }}">🏠 الرئيسية</a>
      
      <a href="#" id="toggleSubjectsMenu" onclick="toggleSubMenu(event)" style="display: flex; justify-content: space-between; align-items: center;" class="active">
        <span>📖 المواد المسجلة</span>
        <span id="menuArrow" style="font-size: 0.8rem; transition: transform 0.3s; transform: rotate(180deg);">▼</span>
      </a>
      
      <div class="sub-menu open" id="subjectsSubMenu">
        @forelse($enrolledSubjects ?? [] as $s)
          <a href="{{ route('student.subject.show', $s->id) }}" style="display: block; text-decoration: none; color: inherit;" class="{{ $s->id == $subject->id ? 'active' : '' }}">
            ▪ {{ $s->name }}
          </a>
        @empty
          <span style="display: block; padding: 8px 12px; font-size: 0.85rem; color: #888;">لا توجد مواد</span>
        @endforelse
      </div>

      <a href="{{ route('student.lessons') }}">🎥 المحاضرات</a>
      <a href="{{ route('student.assignments') }}">📝 الاختبارات</a>
      <a href="{{ route('student.announcements') }}">📢 الإعلانات</a>
      
      @if (Route::has('student.profile.edit'))
        <a href="{{ route('student.profile.edit') }}">⚙️ تعديل بياناتي الشخصية</a>
      @endif
    </nav>

    <div style="padding: 20px; border-top: 1px solid rgba(0,0,0,0.05); margin-top: auto;">
      <p style="margin-bottom: 10px; font-weight: bold; font-size: 0.9rem;">{{ Auth::user()->name ?? '' }}</p>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #dc3545; border: none;">تسجيل الخروج</button>
      </form>
    </div>
  </aside>

  <!-- محتوى الصفحة الرئيسي -->
  <main class="main" style="display: flex; flex-direction: column; width: 100%;">
    <!-- شريط علوي مع زر العودة للرئيسية -->
    <div class="top-header">
      <a href="{{ route('student.dashboard') }}" class="back-btn">
        ← العودة إلى لوحة التحكم
      </a>
      <span style="font-size: 0.85rem; color: #64748b;">مرحباً، {{ Auth::user()->name }}</span>
    </div>

    <!-- محتوى صفحة المادة -->
    <div style="padding: 32px; flex: 1;">
      
      <!-- هيرو أو ترويسة المادة -->
      <div class="subject-hero">
        <h1>{{ $subject->name }}</h1>
        <p>{{ $subject->description ?? 'لا يوجد وصف تفصيلي متاح لهذه المادة حالياً.' }}</p>
      </div>

      <!-- 1. قسم المحاضرات -->
      <div class="section-container">
        <div class="section-title">🎥 المحاضرات والدروس</div>
        <div class="item-list">
          @forelse($subject->lessons ?? [] as $lesson)
            <div class="list-card">
              <div class="list-info">
                <h4>{{ $lesson->title }}</h4>
                <p>{{ Str::limit($lesson->description ?? 'محاضرة تعليمية للمادة', 60) }}</p>
              </div>
              <!-- يمكنك توجيه الرابط لصفحة عرض الدرس أو الفيديو الخاص به -->
              <a href="#" class="action-link">مشاهدة الدرس</a>
            </div>
          @empty
            <div class="empty-msg">لا توجد محاضرات مضافة لهذه المادة حتى الآن.</div>
          @endforelse
        </div>
      </div>

      <!-- 2. قسم الاختبارات والواجبات -->
      <div class="section-container">
        <div class="section-title">📝 الاختبارات والمهام</div>
        <div class="item-list">
          @forelse($subject->assignments ?? [] as $assignment)
            <div class="list-card">
              <div class="list-info">
                <h4>{{ $assignment->title }}</h4>
                <p>تاريخ التسليم: {{ $assignment->due_date ?? 'غير محدد' }}</p>
              </div>
              <a href="#" class="action-link" style="background: #2563eb;">بدء الاختبار</a>
            </div>
          @empty
            <div class="empty-msg">لا توجد اختبارات أو واجبات مطلوبة حالياً.</div>
          @endforelse
        </div>
      </div>

      <!-- 3. قسم الإعلانات الخاصة بالمادة -->
      <div class="section-container">
        <div class="section-title">📢 إعلانات المادة</div>
        <div class="item-list">
          @forelse($subject->announcements ?? [] as $announcement)
            <div class="list-card" style="background: #fffbeb; border-color: #fef3c7;">
              <div class="list-info">
                <h4 style="color: #92400e;">{{ $announcement->title ?? 'إعلان هام' }}</h4>
                <p style="color: #b45309;">{{ $announcement->body ?? $announcement->message }}</p>
              </div>
            </div>
          @empty
            <div class="empty-msg">لا توجد إعلانات جديدة تخص هذه المادة.</div>
          @endforelse
        </div>
      </div>

    </div>
  </main>
</div>

<script>
  function toggleSubMenu(event) {
    event.preventDefault();
    let subMenu = document.getElementById('subjectsSubMenu');
    let arrow = document.getElementById('menuArrow');
    
    subMenu.classList.toggle('open');
    if (subMenu.classList.contains('open')) {
      arrow.style.transform = 'rotate(180deg)';
    } else {
      arrow.style.transform = 'rotate(0deg)';
    }
  }
</script>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
<script src="{{ asset('js/dashboard-student.js') }}"></script>
@stack('scripts')
</body>
</html>