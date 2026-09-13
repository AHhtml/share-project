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
        <a href="{{ route('management.dashboard') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📊 لوحة التحكم</a>
        
        <!-- زر إدارة المستخدمين الرئيسي -->
        <button type="button" onclick="toggleMenu('usersMenu')" class="btn btn-ghost nav-item" style="text-align: right; justify-content: space-between; display: flex; width: 100%; background: none; border: none; cursor: pointer;">
          <span>👥 إدارة المستخدمين</span>
          <span>▾</span>
        </button>

        <!-- القائمة الفرعية: إدارة المستخدمين -->
        <div id="usersMenu" class="collapsible-menu open" style="padding-right: 15px; margin-top: 4px;">
          
          <!-- إدارة الطلاب (تفتح قائمة التخصصات بـ Transition) -->
          <button type="button" onclick="toggleMenu('studentsMenu')" class="btn btn-ghost" style="text-align: right; justify-content: space-between; display: flex; width: 100%; font-size: 0.9rem; background: none; border: none; cursor: pointer; color: #cbd5e1;">
            <span>🎓 إدارة الطلاب</span>
            <span>▾</span>
          </button>
          
          <!-- تخصصات الطلاب والقائمة المنسدلة -->
          <div id="studentsMenu" class="collapsible-menu open" style="padding-right: 15px; margin-top: 2px;">
            <!-- رابط لعرض كل الطلاب في المركز -->
            {{-- <a href="{{ route('management.students.index') }}" class="btn btn-ghost" style="text-align: right; font-size: 0.85rem; text-decoration: none; color: #38bdf8; display: block; padding: 4px 0; font-weight: bold;">
                • كل الطلاب
            </a> --}}
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

          <!-- إدارة المعلمين (رابط مباشر لواجهة المعلمين بدون قوائم منسدلة) -->
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

  <main class="main">

    <!-- ===== الرئيسية ===== -->
    <section data-section="overview">
      <div class="main-head">
        <div>
          <h1>لوحة الإدارة</h1>
          <p>أهلاً بك {{ Auth::user()->name }}، متابعة يومية لبيانات الطلاب والمعلمين.</p>
        </div>
      </div>
      <div class="stat-grid">
        <div class="card stat-card">
          <span class="glyph">🎓</span>
          <div class="num">{{ $studentsCount }}</div>
          <div class="lbl">طالب مسجّل</div>
        </div>
        <div class="card stat-card">
          <span class="glyph">📚</span>
          <div class="num">{{ $teachersCount }}</div>
          <div class="lbl">معلم نشط</div>
        </div>
        <div class="card stat-card">
          <span class="glyph">🎥</span>
          <div class="num">{{ $lessonsCount }}</div>
          <div class="lbl">محاضرة منشورة</div>
        </div>
        <div class="card stat-card">
          <span class="glyph">📝</span>
          <div class="num">{{ $assignmentsCount }}</div>
          <div class="lbl">اختبار مجدول</div>
        </div>
      </div>
      <div class="card section-card">
        <div class="section-head">
          <h2>أحدث المستخدمين المسجلين</h2>
        </div>
        <div>
          @forelse($latestUsers as $u)
            <div style="padding: 10px 0; border-bottom: 1px solid var(--border-color, #eee); display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong>{{ $u->name }}</strong>
                <span style="font-size: 0.8rem; color: #777; margin-right: 8px;">({{ $u->email }})</span>
              </div>
              <span class="badge" style="background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem;">
                {{ $u->role }}
              </span>
            </div>
          @empty
            <p style="color: #888; font-size: 0.9rem; margin-top: 10px;">لا يوجد مستخدمون جدد.</p>
          @endforelse
        </div>
      </div>
    </section>

    <!-- ===== الطلاب ===== -->
    <section data-section="students" hidden>
      <div class="main-head">
        <div><h1>إدارة الطلاب</h1><p>إضافة الطلاب وتعديل بياناتهم أو حذفهم.</p></div>
        <button class="btn btn-primary" id="addStudentBtn">+ إضافة طالب</button>
      </div>
      <div class="card section-card">
        <table class="data-table">
          <thead><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>المسار</th><th>تاريخ الالتحاق</th><th>إجراءات</th></tr></thead>
          <tbody id="studentsTable"></tbody>
        </table>
        <div id="studentsEmpty" class="empty-state" hidden><div class="glyph">🎓</div><p>لا يوجد طلاب بعد.</p></div>
      </div>
    </section>

    <!-- ===== المعلمون ===== -->
    <section data-section="teachers" hidden>
      <div class="main-head">
        <div><h1>إدارة المعلمين</h1><p>إضافة المعلمين وتعديل بياناتهم أو حذفهم.</p></div>
        <button class="btn btn-primary" id="addTeacherBtn">+ إضافة معلم</button>
      </div>
      <div class="card section-card">
        <table class="data-table">
          <thead><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>التخصص</th><th>تاريخ الالتحاق</th><th>إجراءات</th></tr></thead>
          <tbody id="teachersTable"></tbody>
        </table>
        <div id="teachersEmpty" class="empty-state" hidden><div class="glyph">📚</div><p>لا يوجد معلمون بعد.</p></div>
      </div>
    </section>

    <!-- ===== الإعلانات ===== -->
    <section data-section="announcements" hidden>
      <div class="main-head">
        <div><h1>الإعلانات</h1><p>انشر تعميماً يصل لكل مستخدمي المنصة.</p></div>
        <button class="btn btn-primary" id="addAnnBtn">+ إعلان جديد</button>
      </div>
      <div class="card section-card" id="annList"></div>
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
    <h3 >إعلان جديد</h3>
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
  if (menu.classList.contains('open')) {
    menu.classList.remove('open');
  } else {
    menu.classList.add('open');
  }
}
</script>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
<script src="{{ asset('js/dashboard-management.js') }}"></script>
</body>
</html>