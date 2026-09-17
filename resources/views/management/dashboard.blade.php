<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة الإدارة — منارة</title>
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

    /* القائمة الجانبية للإدارة */
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
        justify-content: space-between;
    }

    .sidebar-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
    }

    .sidebar-avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
    }

    .sidebar-nav {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 15px;
    }

    .sidebar-nav .nav-item, 
    .sidebar-nav .btn-ghost {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 10px 14px;
        color: #cbd5e1;
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
    .sidebar-nav .btn-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

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
        max-height: 500px;
        opacity: 1;
    }

    /* محتوى الصفحة الرئيسي */
    .main {
        flex: 1;
        margin-right: 280px;
        padding: 30px;
        overflow-y: auto;
        box-sizing: border-box;
    }

    /* تنسيق جدول المستخدمين الجديد */
    .table-container {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
        background: #ffffff;
    }
    .table-container th {
        background: #f1f5f9;
        color: #334155;
        padding: 14px 16px;
        font-size: 0.9rem;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-container td {
        padding: 14px 16px;
        color: #1e293b;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-container tr:hover td {
        background: #f8fafc;
    }
    .subject-badge {
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
        margin-left: 4px;
        margin-bottom: 4px;
    }
    .role-badge-student {
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .role-badge-teacher {
        background: #dcfce7;
        color: #166534;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* تنسيق الإشعارات الثابتة (دائمة الظهور حتى يتم إغلاقها) */
    .toast-container {
        position: fixed;
        top: 25px;
        left: 25px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 380px;
    }

    .toast-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        border-right: 5px solid #3b82f6;
        animation: slideInRight 0.3s ease-out forwards;
        font-family: 'Tajawal', sans-serif;
        direction: rtl;
    }

    .toast-alert.success {
        border-right-color: #10b981; /* أخضر للنجاح */
    }

    .toast-alert.error {
        border-right-color: #ef4444; /* أحمر للخطأ */
    }

    .toast-icon {
        font-size: 1.25rem;
    }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1e293b;
        margin: 0 0 2px 0;
    }

    .toast-message {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0;
    }

    .toast-close-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        color: #94a3b8;
        padding: 4px;
        transition: color 0.2s;
    }

    .toast-close-btn:hover {
        color: #1e293b;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(-100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }

    /* التجاوب مع الجوال */
    .mobile-topbar {
        display: none;
        align-items: center;
        justify-content: space-between;
        padding: 15px 20px;
        background: #065f46;
        color: #fff;
        position: sticky;
        top: 0;
        z-index: 1000;
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
        }
        .sidebar-scrim.active {
            display: block;
        }
    }
</style>
</head>
<body data-home="{{ url('/') }}" data-login="{{ route('login') }}">

<!-- حاوية الإشعارات الثابتة (تبقى ظاهرة حتى يتم النقر على زر الإغلاق ✕) -->
<div class="toast-container" id="toastContainer">
    @if(session('success'))
        <div class="toast-alert success" id="toastMessageAlert">
            <div class="toast-icon">✅</div>
            <div class="toast-content">
                <p class="toast-title">تمت العملية بنجاح</p>
                <p class="toast-message">{{ session('success') }}</p>
            </div>
            <button type="button" class="toast-close-btn" onclick="closeToast('toastMessageAlert')" title="إغلاق">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="toast-alert error" id="toastMessageAlertError">
            <div class="toast-icon">❌</div>
            <div class="toast-content">
                <p class="toast-title">عذراً، حدث خطأ</p>
                <p class="toast-message">{{ session('error') }}</p>
            </div>
            <button type="button" class="toast-close-btn" onclick="closeToast('toastMessageAlertError')" title="إغلاق">✕</button>
        </div>
    @endif
</div>

<div class="mobile-topbar">
  <button id="menuToggle" style="color:#fff; font-size:1.4rem; background:none; border:none; cursor:pointer;">☰</button>
  <strong id="mobileTitle">منارة — لوحة الإدارة</strong>
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
        <a href="{{ route('management.dashboard') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📊 لوحة التحكم</a>
        
        <button type="button" onclick="toggleMenu('usersMenu')" class="btn btn-ghost nav-item" style="text-align: right; justify-content: space-between; display: flex; width: 100%; background: none; border: none; cursor: pointer;">
          <span>👥 إدارة المستخدمين</span>
          <span>▾</span>
        </button>

        <div id="usersMenu" class="collapsible-menu open" style="padding-right: 15px; margin-top: 4px;">
          <button type="button" onclick="toggleMenu('studentsMenu')" class="btn btn-ghost" style="text-align: right; justify-content: space-between; display: flex; width: 100%; font-size: 0.9rem; background: none; border: none; cursor: pointer; color: #cbd5e1;">
            <span>🎓 إدارة الطلاب</span>
            <span>▾</span>
          </button>
          
          <div id="studentsMenu" class="collapsible-menu open" style="padding-right: 15px; margin-top: 2px;">
            <a href="{{ route('management.students.index') }}" class="btn btn-ghost" style="text-align: right; font-size: 0.85rem; text-decoration: none; color: #38bdf8; display: block; padding: 4px 0; font-weight: bold;">
                • كل الطلاب
            </a>
            @php
                $sidebarSubjects = App\Models\Subject::all();
            @endphp
            @forelse($sidebarSubjects as $sub)
                <a href="{{ route('management.subjects.students', $sub->id) }}" class="btn btn-ghost" style="text-align: right; font-size: 0.85rem; text-decoration: none; color: #94a3b8; display: block; padding: 4px 0;">
                    • {{ $sub->name }}
                </a>
            @empty
                <span style="font-size: 0.8rem; color: #777; padding: 4px 0;">لا توجد مواد مضافة</span>
            @endforelse
          </div>

          <a href="{{ route('management.users.index') }}" class="btn btn-ghost" style="text-align: right; justify-content: start; text-decoration: none; font-size: 0.9rem; color: #cbd5e1; margin-top: 4px; display: block;">
            📚 إدارة المعلمين
          </a>
        </div>
        <a href="{{ route('management.profile') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📊 ملفي الشخصي</a>
      </nav>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #dc3545; border: none;">تسجيل الخروج</button>
      </form>
    </div>
 </aside>

 <!-- المحتوى الرئيسي -->
 <main class="main">

    <!-- ===== الجزء العلوي (الإحصائيات) ===== -->
    <section data-section="overview">
      <div class="main-head" style="margin-bottom: 25px;">
        <div>
          <h1 style="margin: 0 0 5px 0; color: #1e293b;">لوحة الإدارة</h1>
          <p style="margin: 0; color: #64748b;">أهلاً بك {{ Auth::user()->name }}، متابعة يومية لبيانات الطلاب والمعلمين.</p>
        </div>
      </div>

      <div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div class="card stat-card" style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
          <span class="glyph" style="font-size: 1.8rem;">🎓</span>
          <div class="num" style="font-size: 1.8rem; font-weight: bold; color: #1e293b; margin: 8px 0 4px 0;">{{ $studentsCount }}</div>
          <div class="lbl" style="color: #64748b; font-size: 0.9rem;">طالب مسجّل</div>
        </div>
        <div class="card stat-card" style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
          <span class="glyph" style="font-size: 1.8rem;">📚</span>
          <div class="num" style="font-size: 1.8rem; font-weight: bold; color: #1e293b; margin: 8px 0 4px 0;">{{ $teachersCount }}</div>
          <div class="lbl" style="color: #64748b; font-size: 0.9rem;">معلم نشط</div>
        </div>
        <div class="card stat-card" style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
          <span class="glyph" style="font-size: 1.8rem;">🎥</span>
          <div class="num" style="font-size: 1.8rem; font-weight: bold; color: #1e293b; margin: 8px 0 4px 0;">{{ $lessonsCount }}</div>
          <div class="lbl" style="color: #64748b; font-size: 0.9rem;">محاضرة منشورة</div>
        </div>
        <div class="card stat-card" style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
          <span class="glyph" style="font-size: 1.8rem;">📝</span>
          <div class="num" style="font-size: 1.8rem; font-weight: bold; color: #1e293b; margin: 8px 0 4px 0;">{{ $assignmentsCount }}</div>
          <div class="lbl" style="color: #64748b; font-size: 0.9rem;">اختبار مجدول</div>
        </div>
      </div>
    </section>

    <!-- ===== الجزء السفلي (سجل أحدث المستخدمين: طلاب ومعلمين) ===== -->
    <section data-section="users-list">
      <div class="main-head" style="margin-bottom: 15px;">
        <h2 style="margin: 0 0 5px 0; font-size: 1.2rem; color: #1e293b;">أحدث المستخدمين المسجلين</h2>
        <p style="margin: 0; color: #64748b; font-size: 0.9rem;">الاسم، البريد الإلكتروني، رقم الجوال، نوع المستخدم، والمادة المرتبطة.</p>
      </div>

      <div class="card section-card" style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <table class="table-container">
          <thead>
            <tr>
              <th>اسم المستخدم</th>
              <th>البريد الإلكتروني</th>
              <th>رقم الجوال</th>
              <th>نوع المستخدم</th>
              <th>المادة المرتبطة</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users ?? [] as $user)
              <tr>
                <td style="font-weight: 700; color: #0f172a;">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? 'غير متوفر' }}</td>
                <td>
                  @if($user->role === 'teacher')
                    <span class="role-badge-teacher">معلم</span>
                  @else
                    <span class="role-badge-student">طالب</span>
                  @endif
                </td>
                <td>
                  @if($user->subjects && $user->subjects->count() > 0)
                    @foreach($user->subjects as $subject)
                      <span class="subject-badge">{{ $subject->name }}</span>
                    @endforeach
                  @else
                    <span style="color: #94a3b8; font-size: 0.85rem;">لا توجد مواد</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                  <div style="font-size: 2rem; margin-bottom: 8px;">👥</div>
                  لا توجد بيانات مستخدمين مسجلة حالياً.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

  </main>
</div>

<script>
  // دالة إغلاق الإشعار يدوياً عند النقر على زر ✕
  function closeToast(elementId) {
    const toast = document.getElementById(elementId);
    if (toast) {
      toast.style.animation = 'fadeOut 0.3s ease-out forwards';
      setTimeout(() => toast.remove(), 300);
    }
  }

  function toggleMenu(menuId) {
    const menu = document.getElementById(menuId);
    if (menu) {
      menu.classList.toggle('open');
    }
  }

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

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
<script src="{{ asset('js/dashboard-management.js') }}"></script>
</body>
</html>