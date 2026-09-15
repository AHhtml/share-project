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

    /* القائمة الجانبية للإدارة (مطابقة تماماً للون وتصميم قائمة المعلم) */
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

    /* قسم الملف الشخصي المطابق لتصميم المعلم */
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

    /* تأثير الانتقال السلس للقائمة المنسدلة */
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

<div class="mobile-topbar">
  <button id="menuToggle" style="color:#fff; font-size:1.4rem; background:none; border:none; cursor:pointer;">☰</button>
  <strong id="mobileTitle">منارة — لوحة الإدارة</strong>
  <span></span>
</div>
<div class="sidebar-scrim" id="sidebarScrim"></div>

<div class="app-shell">
  <!-- القائمة الجانبية للإدارة -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-content">
      <div>
        <!-- عرض الصورة الشخصية واسم المستخدم (مطابق للوحة المعلم) -->
        <div class="sidebar-profile">
          @if(!empty(Auth::user()->avatar))
              <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="الصورة الشخصية" class="sidebar-avatar">
          @else
              <div class="sidebar-avatar-placeholder">👤</div>
          @endif
          <div>
            <p style="margin: 0 0 2px 0; font-weight: bold; font-size: 1rem; color: #fff;">{{ Auth::user()->name }}</p>
            <p style="font-size: 0.8rem; color: #38bdf8; margin: 0; font-weight: 600;">الإدارة العامة</p>
          </div>
        </div>
        
        <nav class="sidebar-nav">
          <a href="{{ route('management.dashboard') }}" class="nav-item">📊 لوحة التحكم</a>
          
          <!-- زر إدارة المستخدمين الرئيسي -->
          <button type="button" onclick="toggleMenu('usersMenu')" class="btn-ghost" style="justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 10px;">👥 إدارة المستخدمين</span>
            <span>▾</span>
          </button>

          <!-- القائمة الفرعية: إدارة المستخدمين -->
          <div id="usersMenu" class="collapsible-menu open" style="padding-right: 15px; margin-top: 4px;">
            
            <!-- إدارة الطلاب -->
            <button type="button" onclick="toggleMenu('studentsMenu')" class="btn-ghost" style="justify-content: space-between; font-size: 0.9rem; color: #cbd5e1;">
              <span style="display: flex; align-items: center; gap: 8px;">🎓 إدارة الطلاب</span>
              <span>▾</span>
            </button>
            
            <!-- تخصصات الطلاب والقائمة المنسدلة -->
            <div id="studentsMenu" class="collapsible-menu open" style="padding-right: 15px; margin-top: 2px;">
              @php
                  $sidebarSubjects = App\Models\Subject::all();
              @endphp
              @forelse($sidebarSubjects as $sub)
                  <a href="{{ route('management.subjects.students', $sub->id) }}" class="btn-ghost" style="font-size: 0.85rem; color: #94a3b8; padding: 6px 10px;">
                      • {{ $sub->name }}
                  </a>
              @empty
                  <span style="font-size: 0.8rem; color: #777; padding: 4px 10px;">لا توجد مواد مضافة</span>
              @endforelse
            </div>

            <!-- إدارة المعلمين -->
            <a href="{{ route('management.users.index') }}" class="btn-ghost" style="font-size: 0.9rem; color: #cbd5e1; margin-top: 4px;">
              📚 إدارة المعلمين
            </a>
          </div>

          <a href="{{ route('management.profile') }}" class="nav-item">⚙️ ملفي الشخصي</a>
        </nav>
      </div>

      <form action="{{ route('logout') }}" method="POST" style="margin-top: 20px;">
        @csrf
        <button type="submit" style="width: 100%; background-color: #ef4444; color: #fff; border: none; padding: 12px; border-radius: 8px; font-family: 'Tajawal', sans-serif; font-weight: bold; cursor: pointer;">تسجيل الخروج</button>
      </form>
    </div>
  </aside>

  <!-- المحتوى الرئيسي -->
  <main class="main">

    <!-- ===== الرئيسية ===== -->
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

      <div class="card section-card" style="background:#fff; padding:24px; border-radius:12px; border:1px solid #e2e8f0;">
        <div class="section-head" style="margin-bottom: 15px;">
          <h2 style="margin: 0; font-size: 1.2rem; color: #1e293b;">أحدث المستخدمين المسجلين</h2>
        </div>
        <div>
          @forelse($latestUsers as $u)
            <div style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="color: #1e293b;">{{ $u->name }}</strong>
                <span style="font-size: 0.85rem; color: #64748b; margin-right: 8px;">({{ $u->email }})</span>
              </div>
              <span class="badge" style="background: #e2e8f0; color: #334155; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                {{ $u->role }}
              </span>
            </div>
          @empty
            <p style="color: #64748b; font-size: 0.9rem; margin: 10px 0; text-align: center;">لا يوجد مستخدمون جدد.</p>
          @endforelse
        </div>
      </div>
    </section>

    <!-- ===== الطلاب ===== -->
    <section data-section="students" hidden>
      <div class="main-head" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div><h1 style="margin:0 0 5px 0;">إدارة الطلاب</h1><p style="margin:0; color:#64748b;">إضافة الطلاب وتعديل بياناتهم أو حذفهم.</p></div>
        <button class="btn btn-primary" id="addStudentBtn">+ إضافة طالب</button>
      </div>
      <div class="card section-card" style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
          <thead><tr style="border-bottom: 2px solid #e2e8f0; text-align: right;"><th style="padding: 10px;">الاسم</th><th style="padding: 10px;">البريد الإلكتروني</th><th style="padding: 10px;">المسار</th><th style="padding: 10px;">تاريخ الالتحاق</th><th style="padding: 10px;">إجراءات</th></tr></thead>
          <tbody id="studentsTable"></tbody>
        </table>
        <div id="studentsEmpty" class="empty-state" hidden style="text-align: center; padding: 30px;"><div class="glyph" style="font-size: 2rem;">🎓</div><p style="color: #64748b;">لا يوجد طلاب بعد.</p></div>
      </div>
    </section>

    <!-- ===== المعلمون ===== -->
    <section data-section="teachers" hidden>
      <div class="main-head" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div><h1 style="margin:0 0 5px 0;">إدارة المعلمين</h1><p style="margin:0; color:#64748b;">إضافة المعلمين وتعديل بياناتهم أو حذفهم.</p></div>
        <button class="btn btn-primary" id="addTeacherBtn">+ إضافة معلم</button>
      </div>
      <div class="card section-card" style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
          <thead><tr style="border-bottom: 2px solid #e2e8f0; text-align: right;"><th style="padding: 10px;">الاسم</th><th style="padding: 10px;">البريد الإلكتروني</th><th style="padding: 10px;">التخصص</th><th style="padding: 10px;">تاريخ الالتحاق</th><th style="padding: 10px;">إجراءات</th></tr></thead>
          <tbody id="teachersTable"></tbody>
        </table>
        <div id="teachersEmpty" class="empty-state" hidden style="text-align: center; padding: 30px;"><div class="glyph" style="font-size: 2rem;">📚</div><p style="color: #64748b;">لا يوجد معلمون بعد.</p></div>
      </div>
    </section>

    <!-- ===== الإعلانات ===== -->
    <section data-section="announcements" hidden>
      <div class="main-head" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div><h1 style="margin:0 0 5px 0;">الإعلانات</h1><p style="margin:0; color:#64748b;">انشر تعميماً يصل لكل مستخدمي المنصة.</p></div>
        <button class="btn btn-primary" id="addAnnBtn">+ إعلان جديد</button>
      </div>
      <div class="card section-card" id="annList" style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0;"></div>
    </section>

  </main>
</div>

<!-- ===== نافذة الطالب ===== -->
<div class="modal-backdrop" id="studentModal">
  <div class="modal">
    <h3 id="studentModalTitle">إضافة طالب</h3>
    <form id="studentForm">
      <input type="hidden" id="studentId">
      <div class="field"><label>الاسم الكامل</label><input type="text" id="studentName" required></div>
      <div class="field"><label>البريد الإلكتروني</label><input type="email" id="studentEmail" required></div>
      <div class="field"><label>كلمة المرور</label><input type="text" id="studentPassword" placeholder="اتركها كما هي عند التعديل" required></div>
      <div class="field"><label>رقم الهاتف</label><input type="tel" id="studentPhone"></div>
      <div class="field">
        <label>المسار الدراسي</label>
        <select id="studentTrack">
          <option>علمي</option>
          <option>أدبي</option>
        </select>
      </div>
      <div class="modal-actions">
        <button type="submit" class="btn btn-primary">حفظ</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('studentModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<!-- ===== نافذة المعلم ===== -->
<div class="modal-backdrop" id="teacherModal">
  <div class="modal">
    <h3 id="teacherModalTitle">إضافة معلم</h3>
    <form id="teacherForm">
      <input type="hidden" id="teacherId">
      <div class="field"><label>الاسم الكامل</label><input type="text" id="teacherName" required></div>
      <div class="field"><label>البريد الإلكتروني</label><input type="email" id="teacherEmail" required></div>
      <div class="field"><label>كلمة المرور</label><input type="text" id="teacherPassword" placeholder="اتركها كما هي عند التعديل" required></div>
      <div class="field"><label>رقم الهاتف</label><input type="tel" id="teacherPhone"></div>
      <div class="field"><label>التخصص / المادة</label><input type="text" id="teacherSubject"></div>
      <div class="modal-actions">
        <button type="submit" class="btn btn-primary">حفظ</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('teacherModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<!-- ===== نافذة الإعلان ===== -->
<div class="modal-backdrop" id="annModal">
  <div class="modal">
    <h3>إعلان جديد</h3>
    <form id="annForm">
      <div class="field"><label>عنوان الإعلان</label><input type="text" id="annTitle" required></div>
      <div class="field"><label>نص الإعلان</label><textarea id="annBody" required></textarea></div>
      <div class="modal-actions">
        <button type="submit" class="btn btn-primary">نشر</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('annModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<script>
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