<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة المعلم — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<style>
    *, *:before, *:after {
        box-sizing: border-box;
    }
    body {
        font-family: 'Tajawal', sans-serif;
        background-color: #f8fafc;
        margin: 0;
        overflow-x: hidden;
    }

    /* تخطيط الصفحة العام */
    .app-shell {
        display: flex;
        min-height: 100vh;
        direction: rtl;
    }

    /* القائمة الجانبية (Sidebar) */
    .sidebar {
        width: 280px;
        background: #065f46;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: fixed;
        top: 0;
        right: 0;
        height: 100vh;
        z-index: 1050;
        box-shadow: -2px 0 10px rgba(0,0,0,0.05);
        transition: right 0.3s ease-in-out;
    }

    .sidebar-content {
        padding: 24px 20px;
        overflow-y: auto;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .sidebar-nav {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 20px;
        flex: 1;
    }

    .sidebar-nav .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 12px 16px;
        color: #e2e8f0;
        background: transparent;
        border: none;
        border-radius: 8px;
        font-family: 'Tajawal', sans-serif;
        font-size: 0.95rem;
        font-weight: 500;
        text-align: right;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .sidebar-nav .nav-item:hover,
    .sidebar-nav .nav-item.active {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    /* محتوى الصفحة الرئيسي */
    .main {
        flex: 1;
        margin-right: 280px; /* مساحة للقائمة الجانبية الثابتة */
        padding: 30px;
        overflow-y: auto;
        box-sizing: border-box;
    }

    /* رأس الصفحة العلوي (الهيدر) */
    .topbar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        padding: 24px 30px;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .teacher-welcome-box {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .teacher-avatar {
        width: 75px;
        height: 75px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #065f46;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        flex-shrink: 0;
    }

    .teacher-info h1 {
        margin: 0 0 5px 0;
        font-size: 1.5rem;
        color: #1e293b;
    }

    .teacher-info p {
        margin: 0;
        color: #64748b;
        font-size: 0.95rem;
    }

    /* نظام الإشعارات */
    .notification-container {
        position: relative;
    }

    .notification-btn {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 10px 16px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: 'Tajawal', sans-serif;
        font-size: 0.95rem;
        color: #1e293b;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: background 0.2s;
    }

    .notification-btn:hover {
        background: #f1f5f9;
    }

    .notification-badge {
        background-color: #ef4444;
        color: white;
        font-size: 0.75rem;
        padding: 2px 7px;
        border-radius: 50%;
        font-weight: bold;
    }

    .notification-dropdown {
        display: none;
        position: absolute;
        left: 0;
        top: 120%;
        width: 320px;
        background: #ffffff;
        color: #333;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        z-index: 1100;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .notification-dropdown.active {
        display: block;
    }

    .notification-header {
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 0.9rem;
        font-weight: bold;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
    }

    .notification-item {
        padding: 12px 16px;
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        text-align: right;
    }

    /* شبكة الإحصائيات */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    .stat-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        position: relative;
    }

    .stat-card .glyph {
        font-size: 1.8rem;
        margin-bottom: 12px;
    }

    .stat-card .num {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .stat-card .lbl {
        color: #64748b;
        font-size: 0.95rem;
    }

    /* تفاصيل المحاضرات والجدول */
    .section-card {
        margin-bottom: 25px;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .data-table th, .data-table td {
        padding: 12px 16px;
        text-align: right;
        border-bottom: 1px solid #e2e8f0;
    }

    .data-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
    }

    /* التجاوب مع الجوال والشاشات الصغيرة */
    .mobile-topbar {
        display: none;
    }

    .sidebar-scrim {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
    }

    @media (max-width: 992px) {
        .sidebar {
            right: -280px;
        }
        .sidebar.active {
            right: 0;
        }
        .main {
            margin-right: 0;
            padding: 20px;
        }
        .mobile-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            background: #065f46;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .sidebar-scrim.active {
            display: block;
        }
    }
</style>

</head>
<body data-home="{{ url('/') }}" data-login="{{ route('login') }}">

<div class="mobile-topbar">
  <button id="menuToggle" style="color:#fff; font-size:1.4rem; background:none; border:none; cursor:pointer;">☰</button>
  <strong>منارة — لوحة المعلم</strong>
  <span></span>
</div>
<div class="sidebar-scrim" id="sidebarScrim"></div>

<div class="app-shell">
  <!-- القائمة الجانبية -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-content">
      <div>
        <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">
          <p style="margin: 0 0 4px 0; font-weight: bold; font-size: 1.1rem;">{{ Auth::user()->name }}</p>
          <p style="font-size: 0.85rem; color: #fbbf24; margin: 0; font-weight: 600;">
            معلم 
            @if(isset($teacherSubjects) && $teacherSubjects->count() > 0)
              — {{ $teacherSubjects->pluck('name')->implode('، ') }}
            @endif
          </p>
        </div>
        
        <nav class="sidebar-nav">
          <button type="button" class="nav-item active" onclick="switchSection('overview')">📊 الرئيسية</button>
          <a href="{{ route('teacher.lessons.index') }}" class="nav-item">🎥 محاضراتي</a>
          <a href="{{ route('teacher.assignments.index') }}" class="nav-item">📝 اختباراتي</a>
          <a href="{{ route('teacher.students.index') }}" class="nav-item">🎓 طلابي</a>
          <a href="{{ route('teacher.announcements.index') }}" class="nav-item">📢 الإعلانات</a>
          <a href="{{ route('teacher.profile') }}" class="nav-item">⚙️ ملفي الشخصي</a>
        </nav>
      </div>

      <form action="{{ route('logout') }}" method="POST" style="margin-top: 20px;">
        @csrf
        <button type="submit" style="width: 100%; background-color: #dc3545; color: #fff; border: none; padding: 12px; border-radius: 8px; font-family: 'Tajawal', sans-serif; font-weight: bold; cursor: pointer;">تسجيل الخروج</button>
      </form>
    </div>
  </aside>

  <!-- المحتوى الرئيسي -->
  <main class="main">

    <!-- تنبيهات النجاح والأخطاء -->
    @if(session('success'))
      <div style="padding: 14px 18px; background-color: #d1fae5; color: #065f46; border-radius: 10px; margin-bottom: 20px; font-weight: 500; border: 1px solid #a7f3d0;">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div style="padding: 14px 18px; background-color: #fee2e2; color: #991b1b; border-radius: 10px; margin-bottom: 20px; font-weight: 500; border: 1px solid #fecaca;">
        {{ session('error') }}
      </div>
    @endif

    <!-- قسم الرئيسية -->
    <section data-section="overview">
      <div class="topbar-header">
        <div class="teacher-welcome-box">
            @if(auth()->user()->avatar)
                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="الصورة الشخصية" class="teacher-avatar">
            @else
                <div class="teacher-avatar">👤</div>
            @endif
            <div class="teacher-info">
              <h1>أهلاً بك، أ. {{ Auth::user()->name }}</h1>
              <p id="courseNameHead">تابع مساقك الدراسي وأدر طلابك بكل سهولة.</p>
            </div>
        </div>

        <!-- زر الإشعارات -->
        @php
          $notifications = Auth::user()->customNotifications()->latest()->take(10)->get();
          $unreadCount = Auth::user()->customNotifications()->count();
        @endphp
        <div class="notification-container">
          <button type="button" class="notification-btn" id="notifBtn" onclick="toggleNotifications()">
            <span>🔔 الإشعارات</span>
            @if($unreadCount > 0)
              <span class="notification-badge" id="notifBadge">{{ $unreadCount }}</span>
            @endif
          </button>

          <div class="notification-dropdown" id="notificationDropdown">
            <div class="notification-header">
              <span>التنبيهات الواردة</span>
            </div>
            <div style="max-height: 250px; overflow-y: auto;">
              @forelse($notifications as $notification)
                <div class="notification-item">
                  <div style="display: flex; gap: 8px; align-items: flex-start;">
                    <span>🔔</span>
                    <div>
                      <p style="margin: 0; color: #1e293b;">{{ $notification->message }}</p>
                      <small style="color: #64748b; font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                  </div>
                  <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 1rem; padding: 0;" title="حذف الإشعار">✕</button>
                  </form>
                </div>
              @empty
                <div style="padding: 20px; text-align: center; color: #888; font-size: 0.9rem;">
                  لا توجد إشعارات حالياً
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>

      <!-- الإحصائيات -->
      <div class="stat-grid">
        <div class="card stat-card">
          <span class="glyph">🎥</span>
          <div class="num">{{ $myLessonsCount ?? 0 }}</div>
          <div class="lbl">محاضرة منشورة</div>
        </div>
        <div class="card stat-card">
          <span class="glyph">📝</span>
          <div class="num">{{ $myAssignmentsCount ?? 0 }}</div>
          <div class="lbl">اختبار مجدول</div>
        </div>
        <div class="card stat-card">
          <span class="glyph">🎓</span>
          <div class="num">{{ $totalStudentsCount ?? 0 }}</div>
          <div class="lbl">طالب في المسار</div>
        </div>
      </div>

      <!-- أحدث المحاضرات -->
      <div class="card section-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
          <h2 style="margin: 0; font-size: 1.2rem; color: #1e293b;">أحدث المحاضرات</h2>
          <a href="{{ route('teacher.lessons.index') }}" style="color: #065f46; text-decoration: none; font-weight: 600; font-size: 0.9rem;">عرض الكل ←</a>
        </div>
        <div>
          @isset($myLessons)
            @forelse($myLessons as $lesson)
              <div style="padding: 14px 0; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <strong style="color: #1e293b; font-size: 1rem;">{{ $lesson->title }}</strong>
                  <div style="margin-top: 6px;">
                    <a href="{{ $lesson->description }}" target="_blank" style="color: #0284c7; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px;">
                      <span>رابط المحاضرة</span> 🔗
                    </a>
                  </div>
                </div>
              </div>
            @empty
              <p style="color: #64748b; font-size: 0.95rem; margin: 0; text-align: center; padding: 20px;">لم تقم بنشر أي محاضرات بعد.</p>
            @endforelse
          @endisset
        </div>
      </div>
    </section>

  </main>
</div>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
<script src="{{ asset('js/dashboard-teacher.js') }}"></script>

<script>
  function switchSection(sectionName) {
    document.querySelectorAll('main section').forEach(sec => sec.hidden = true);
    const targetSection = document.querySelector(`section[data-section="${sectionName}"]`);
    if (targetSection) targetSection.hidden = false;

    document.querySelectorAll('.sidebar-nav .nav-item').forEach(btn => btn.classList.remove('active'));
    event.currentTarget?.classList?.add('active');
  }

  function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('active');
  }

  window.addEventListener('click', function(e) {
    const container = document.querySelector('.notification-container');
    if (container && !container.contains(e.target)) {
      document.getElementById('notificationDropdown').classList.remove('active');
    }
  });

  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  const sidebarScrim = document.getElementById('sidebarScrim');

  if (menuToggle && sidebar && sidebarScrim) {
    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      sidebarScrim.classList.toggle('active');
    });

    sidebarScrim.addEventListener('click', () => {
      sidebar.classList.remove('active');
      sidebarScrim.classList.remove('active');
    });
  }
</script>
</body>
</html>