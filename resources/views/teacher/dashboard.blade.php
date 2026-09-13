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

<!-- تنسيقات إضافية لضمان التجاوب الكامل مع كافة الشاشات -->
<style>
  *, *:before, *:after {
    box-sizing: border-box;
  }
  body {
    overflow-x: hidden;
  }
  
  @media (max-width: 768px) {
    .app-shell {
      flex-direction: column;
    }
    .sidebar {
      position: fixed;
      top: 0;
      right: -280px;
      width: 280px;
      height: 100%;
      background: #fff;
      z-index: 1050;
      transition: right 0.3s ease-in-out;
      box-shadow: -2px 0 10px rgba(0,0,0,0.1);
    }
    .sidebar.active {
      right: 0;
    }
    .mobile-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 16px;
      background: #1e293b;
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
    .sidebar-scrim.active {
      display: block;
    }
    .main {
      padding: 15px !important;
    }
    .stat-grid {
      grid-template-columns: 1fr !important;
    }
  }

  @media (min-width: 769px) {
    .mobile-topbar {
      display: none;
    }
    .sidebar-scrim {
      display: none !important;
    }
  }

  .table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .data-table {
    width: 100%;
    border-collapse: collapse;
    white-space: nowrap;
  }

  .modal {
    max-width: 90%;
    width: 450px;
    padding: 20px;
    box-sizing: border-box;
  }

  .topbar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
  }
  .notification-container {
    position: relative;
  }
  .notification-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Tajawal', sans-serif;
    font-size: 0.9rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }
  .notification-badge {
    background-color: #ef4444;
    color: white;
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 50%;
    font-weight: bold;
  }
  .notification-dropdown {
    display: none;
    position: absolute;
    left: 0;
    top: 110%;
    width: 320px;
    background: #ffffff;
    color: #333;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    z-index: 1100;
    border: 1px solid #e2e8f0;
  }
  .notification-dropdown.active {
    display: block;
  }
  .notification-header {
    background: #f8fafc;
    padding: 10px 12px;
    font-size: 0.85rem;
    font-weight: bold;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .notification-item {
    padding: 10px 12px;
    font-size: 0.82rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    text-align: right;
  }
</style>
</head>
<body data-home="{{ url('/') }}" data-login="{{ route('login') }}">

<div class="mobile-topbar">
  <button id="menuToggle" class="btn-ghost" style="color:#fff; font-size:1.3rem; background:none; border:none; cursor:pointer;">☰</button>
  <strong id="mobileTitle">منارة — لوحة المعلم</strong>
  <span></span>
</div>
<div class="sidebar-scrim" id="sidebarScrim"></div>

<div class="app-shell" style="display: flex; min-height: 100vh;">
  <aside class="sidebar" id="sidebar">
    <div style="padding: 20px;">
      <p style="margin-bottom: 2px; font-weight: bold;">{{ Auth::user()->name }}</p>
      <p style="font-size: 0.85rem; color: #d97706; margin-bottom: 15px; font-weight: 600;">
        معلم 
        @if(isset($teacherSubjects) && $teacherSubjects->count() > 0)
          {{ $teacherSubjects->pluck('name')->implode('، ') }}
        @endif
      </p>
      
      <nav class="sidebar-nav" style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
        <button type="button" class="btn btn-ghost nav-item active" onclick="switchSection('overview')" style="text-align: right; justify-content: start;">📊 الرئيسية</button>
        <a href="{{ route('teacher.lessons.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">🎥 محاضراتي</a>
        <a href="{{ route('teacher.assignments.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📝 اختباراتي</a>
        <a href="{{ route('teacher.students.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">🎓 طلابي</a>
        <a href="{{ route('teacher.announcements.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📢 الإعلانات</a>
        <a href="{{ route('teacher.profile') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📊 ملفي الشخصي</a>
      </nav>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #dc3545; border: none;">تسجيل الخروج</button>
      </form>
    </div>
  </aside>

  <main class="main" style="flex: 1; padding: 30px; overflow-y: auto;">

    <!-- ===== تنبيهات النجاح والأخطاء ===== -->
    @if(session('success'))
      <div style="padding: 12px 16px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div style="padding: 12px 16px; background-color: #f8d7da; color: #721c24; border-radius: 8px; margin-bottom: 20px;">
        {{ session('error') }}
      </div>
    @endif

    <!-- ===== الرئيسية ===== -->
    <section data-section="overview">
      <div class="topbar-header">
        <div>
          <h1>أهلاً بك، أ. {{ Auth::user()->name }}</h1>
          <p id="courseNameHead">تابع مساقك الدراسي وأدر طلابك من هنا.</p>
        </div>

        <!-- زر الإشعارات والعداد الديناميكي باستخدام العلاقة المخصصة -->
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
              <span>التنبيهات</span>
            </div>
            <div id="notificationsList" style="max-height: 250px; overflow-y: auto;">
              @forelse($notifications as $notification)
                <div class="notification-item">
                  <div style="display: flex; gap: 8px; align-items: flex-start;">
                    <span>🔔</span>
                    <div>
                      <p style="margin: 0; color: #333;">{{ $notification->message }}</p>
                      <small style="color: #64748b; font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                  </div>
                  <!-- زر حذف الإشعار الفردي -->
                  <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.9rem; padding: 0;" title="حذف الإشعار">✕</button>
                  </form>
                </div>
              @empty
                <div style="padding: 15px; text-align: center; color: #888; font-size: 0.85rem;">
                  لا توجد إشعارات حالياً
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>

      <div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 20px;">
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

      <div class="card section-card">
        <div class="section-head" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
          <h2>أحدث المحاضرات</h2>
          <a href="{{ route('teacher.lessons.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">عرض الكل</a>
        </div>
        <div id="overviewLectures">
          @isset($myLessons)
            @forelse($myLessons as $lesson)
              <div style="padding: 12px 0; border-bottom: 1px solid var(--border-color, #eee);">
                <strong>{{ $lesson->title }}</strong>
                <br> <br>
                <h3 style="margin: 0; font-size: 1rem;">
                  <a href="{{ $lesson->description }}" target="_blank" style="color: #0d6efd; text-decoration: none; word-break: break-all; display: inline-flex; align-items: center; gap: 6px;">
                      <span>رابط المحاضرة</span>
                      <span style="font-size: 0.85rem;">🔗</span>
                  </a>
                </h3>          
              </div>
            @empty
              <p style="color: #888; font-size: 0.9rem; margin-top: 10px;">لم تقم بنشر أي محاضرات بعد.</p>
            @endforelse
          @endisset
        </div>
      </div>
    </section>

    <!-- ===== محاضراتي ===== -->
    <section data-section="lectures" hidden>
      <div class="main-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div><h1>محاضراتي</h1><p>انشر محاضرة جديدة أو حدّث محتوى محاضرة سابقة.</p></div>
        <button class="btn btn-primary" id="addLectureBtn" onclick="UI.openModal('lectureModal')">+ محاضرة جديدة</button>
      </div>
      <div id="lecturesList"></div>
      <div id="lecturesEmpty" class="card empty-state" hidden><div class="glyph">🎥</div><p>لم تنشر أي محاضرة بعد.</p></div>
    </section>

    <!-- ===== اختباراتي ===== -->
    <section data-section="exams" hidden>
      <div class="main-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div><h1>اختباراتي</h1><p>جدول اختباراً جديداً لطلاب مسارك.</p></div>
        <button class="btn btn-primary" id="addExamBtn" onclick="UI.openModal('examModal')">+ اختبار جديد</button>
      </div>
      <div id="examsList"></div>
      <div id="examsEmpty" class="card empty-state" hidden><div class="glyph">📝</div><p>لا يوجد اختبارات مجدولة بعد.</p></div>
    </section>

    <!-- ===== طلابي ===== -->
    <section data-section="students" hidden>
      <div class="main-head">
        <div><h1>طلابي</h1><p>إدارة الطلاب المسجلين في موادك الدراسية.</p></div>
      </div>
      <div class="card section-card">
        @php $hasStudents = false; @endphp
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>الاسم</th>
                <th>البريد الإلكتروني</th>
                <th>المادة</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody id="studentsTable">
              @isset($teacherSubjects)
                @foreach($teacherSubjects as $subject)
                  @foreach($subject->students as $student)
                    @php $hasStudents = true; @endphp
                    <tr>
                      <td>{{ $student->name }}</td>
                      <td>{{ $student->email }}</td>
                      <td>{{ $subject->name }}</td>
                      <td>
                        <form action="{{ route('teacher.subjects.students.remove', ['subject' => $subject->id, 'student' => $student->id]) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm" style="background-color: #dc3545; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; cursor: pointer;">
                            إلغاء التسجيل
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                @endforeach
              @endisset
            </tbody>
          </table>
        </div>

        @if(!$hasStudents)
          <div id="studentsEmpty" class="empty-state">
            <div class="glyph">🎓</div>
            <p>لا يوجد طلاب مسجلون في موادك حالياً.</p>
          </div>
        @endif
      </div>
    </section>

  </main>
</div>

<!-- ===== نافذة المحاضرة ===== -->
<div class="modal-backdrop" id="lectureModal">
  <div class="modal">
    <h3 id="lectureModalTitle">محاضرة جديدة</h3>
    <form id="lectureForm">
      <input type="hidden" id="lectureId">
      <div class="field"><label>عنوان المحاضرة</label><input type="text" id="lectureTitle" required></div>
      <div class="field"><label>التاريخ</label><input type="date" id="lectureDate" required></div>
      <div class="field"><label>المدة</label><input type="text" id="lectureDuration" placeholder="مثال: 90 دقيقة"></div>
      <div class="field"><label>ملاحظات / محتوى المحاضرة</label><textarea id="lectureNotes"></textarea></div>
      <div class="modal-actions">
        <button type="submit" class="btn btn-primary">نشر المحاضرة</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('lectureModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<!-- ===== نافذة الاختبار ===== -->
<div class="modal-backdrop" id="examModal">
  <div class="modal">
    <h3 id="examModalTitle">اختبار جديد</h3>
    <form id="examForm">
      <input type="hidden" id="examId">
      <div class="field"><label>عنوان الاختبار</label><input type="text" id="examTitle" required></div>
      <div class="field"><label>التاريخ</label><input type="date" id="examDate" required></div>
      <div class="field"><label>المدة</label><input type="text" id="examDuration" placeholder="مثال: 45 دقيقة"></div>
      <div class="field"><label>العلامة الكاملة</label><input type="number" id="examMarks" min="1" value="10"></div>
      <div class="modal-actions">
        <button type="submit" class="btn btn-primary">جدولة الاختبار</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('examModal')">إلغاء</button>
      </div>
    </form>
  </div>
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