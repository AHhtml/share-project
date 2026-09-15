<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة الطالب — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@450;500;700;900&display=swap" rel="stylesheet">
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

  /* الشريط العلوي والإشعارات */
  .top-header {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 16px 32px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }
  .topbar-notifications {
    position: relative;
    display: inline-block;
  }
  .notification-btn {
    background: #f1f5f9;
    border: none;
    padding: 8px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.1rem;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    transition: background 0.2s;
  }
  .notification-btn:hover { background: #e2e8f0; }
  .notification-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    background: #ef4444;
    color: #fff;
    font-size: 0.65rem;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: bold;
  }
  .notifications-dropdown {
    position: absolute;
    left: 0;
    top: 50px;
    width: 320px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
    display: none;
    z-index: 1000;
    border: 1px solid #e2e8f0;
    text-align: right;
    overflow: hidden;
  }
  .notifications-dropdown.show { display: block; }
  .notifications-header {
    padding: 14px 18px;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 700;
    font-size: 0.9rem;
    color: #0f172a;
    background: #f8fafc;
  }
  .notifications-body {
    max-height: 280px;
    overflow-y: auto;
  }
  .notification-item {
    padding: 12px 18px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
    color: #334155;
    display: block;
    text-decoration: none;
    transition: background 0.2s;
  }
  .notification-item:hover { background: #f8fafc; }

  /* تصميم لوحة الترحيب الحديثة */
  .welcome-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    padding: 32px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .welcome-banner h1 {
    font-size: 1.8rem;
    font-weight: 900;
    margin: 0 0 8px 0;
    color: #ffffff;
  }
  .welcome-banner p {
    color: #94a3b8;
    font-size: 0.95rem;
    margin: 0;
  }

  /* شبكة كروت المواد */
  .subjects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
  }
  .subject-card {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -2px rgba(0,0,0,0.02);
    border: 1px solid #e2e8f0;
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.3s ease;
  }
  .subject-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px -3px rgba(0,0,0,0.07);
    border-color: #cbd5e1;
  }
  .subject-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
  }
  .subject-desc {
    font-size: 0.88rem;
    color: #64748b;
    margin-bottom: 20px;
    line-height: 1.6;
  }
  
  /* عدادات المادة */
  .subject-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    background: #f8fafc;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
    border: 1px solid #f1f5f9;
  }
  .stat-item span {
    display: block;
    color: #64748b;
    font-size: 0.75rem;
    margin-bottom: 2px;
  }
  .stat-item strong {
    color: #0f172a;
    font-size: 1.05rem;
    font-weight: 700;
  }

  /* زر دخول المادة */
  .btn-enter {
    display: block;
    text-align: center;
    background: #0f172a;
    color: #fff;
    padding: 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: background 0.2s;
  }
  .btn-enter:hover {
    background: #1e293b;
  }

  .empty-state {
    background: #ffffff;
    padding: 48px;
    border-radius: 14px;
    grid-column: 1 / -1;
    text-align: center;
    color: #64748b;
    border: 2px dashed #cbd5e1;
    font-size: 0.95rem;
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
      <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">🏠 الرئيسية</a>
      
      <a href="#" id="toggleSubjectsMenu" onclick="toggleSubMenu(event)" style="display: flex; justify-content: space-between; align-items: center;">
        <span>📖 المواد المسجلة</span>
        <span id="menuArrow" style="font-size: 0.8rem; transition: transform 0.3s;">▼</span>
      </a>
      
      <div class="sub-menu {{ request()->routeIs('student.subject.*') ? 'open' : '' }}" id="subjectsSubMenu">
        @forelse($enrolledSubjects ?? [] as $subject)
          <a href="{{ route('student.subject.show', $subject->id) }}" style="display: block; text-decoration: none; color: inherit;" class="{{ request()->is('student/subjects/' . $subject->id) ? 'active' : '' }}">
            ▪ {{ $subject->name }}
          </a>
        @empty
          <span style="display: block; padding: 8px 12px; font-size: 0.85rem; color: #888;">لا توجد مواد</span>
        @endforelse
      </div>

      <a href="{{ route('student.lessons') }}" class="{{ request()->routeIs('student.lessons') ? 'active' : '' }}">🎥 المحاضرات</a>
      <a href="{{ route('student.assignments') }}" class="{{ request()->routeIs('student.assignments') ? 'active' : '' }}">📝 الاختبارات</a>
      <a href="{{ route('student.announcements') }}" class="{{ request()->routeIs('student.announcements') ? 'active' : '' }}">📢 الإعلانات</a>
      
      @if (Route::has('student.profile.edit'))
        <a href="{{ route('student.profile.edit') }}" class="{{ request()->routeIs('student.profile.*') ? 'active' : '' }}">⚙️ تعديل بياناتي الشخصية</a>
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
    

    <!-- المحتوى الداخلي -->
    <div style="padding: 32px; flex: 1;">
      @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 0.9rem; border: 1px solid #bbf7d0;">
          {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 0.9rem; border: 1px solid #fecaca;">
          {{ session('error') }}
        </div>
      @endif

      <!-- بانر الترحيب الاحترافي -->
      <div class="welcome-banner">
        <div>
          <h1>مرحباً بك، {{ Auth::user()->name }} 👋</h1>
          <p>إليك ملخص لموادك الدراسية والمستجدات الخاصة بك لهذا الفصل.</p>
        </div>
        <div class="topbar-notifications">
        <button class="notification-btn" onclick="toggleNotificationsDropdown(event)" title="الإشعارات">
          🔔
          @if(isset($notifications) && $notifications->count() > 0)
            <span class="notification-badge">{{ $notifications->count() }}</span>
          @endif
        </button>
        <div class="notifications-dropdown" id="notificationsDropdown">
          <div class="notifications-header">التنبيهات والإشعارات</div>
          <div class="notifications-body">
            @forelse($notifications ?? [] as $notification)
              <a href="#" class="notification-item">
                <strong style="display: block; margin-bottom: 2px; color: #0f172a;">{{ $notification->title ?? 'تنبيه جديد' }}</strong>
                <p style="margin: 0; color: #64748b; font-size: 0.8rem;">{{ Str::limit($notification->body ?? $notification->message, 50) }}</p>
              </a>
            @empty
              <div style="padding: 20px; text-align: center; color: #64748b; font-size: 0.85rem;">لا توجد إشعارات جديدة</div>
            @endforelse
          </div>
        </div>
      </div>
      </div>

      <!-- قسم المواد المسجلة -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0;">المواد المسجلة</h2>
      </div>

      <div class="subjects-grid">
          @forelse($enrolledSubjects ?? [] as $subject)
              <div class="subject-card">
                  <div>
                      <h3 class="subject-title">{{ $subject->name }}</h3>
                      <p class="subject-desc">
                          {{ Str::limit($subject->description ?? 'لا يوجد وصف متاح لهذه المادة حالياً.', 80) }}
                      </p>
                  </div>

                  <div>
                      <!-- إحصائيات المادة (محاضرات، اختبارات، إعلانات) -->
                      <div class="subject-stats">
                          <div class="stat-item">
                              <span>محاضرات</span>
                              <strong>{{ optional($subject->lessons)->count() ?? 0 }}</strong>
                          </div>
                          <div class="stat-item" style="border-right: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0;">
                              <span>اختبارات</span>
                              <strong>{{ optional($subject->assignments)->count() ?? 0 }}</strong>
                          </div>
                          <div class="stat-item">
                              <span>إعلانات</span>
                              <strong>{{ optional($subject->announcements)->count() ?? 0 }}</strong>
                          </div>
                      </div>

                      <a href="{{ route('student.subject.show', $subject->id) }}" class="btn-enter">
                          دخول المادة
                      </a>
                  </div>
              </div>
          @empty
              <div class="empty-state">
                  أنت لست مسجلاً في أي مادة دراسية حتى الآن.
              </div>
          @endforelse
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

  function toggleNotificationsDropdown(event) {
    event.stopPropagation();
    let dropdown = document.getElementById('notificationsDropdown');
    dropdown.classList.toggle('show');
  }

  window.addEventListener('click', function() {
    let dropdown = document.getElementById('notificationsDropdown');
    if (dropdown && dropdown.classList.contains('show')) {
      dropdown.classList.remove('show');
    }
  });
</script>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
<script src="{{ asset('js/dashboard-student.js') }}"></script>
@stack('scripts')
</body>
</html>