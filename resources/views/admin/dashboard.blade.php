<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة مدير المركز — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body data-home="{{ url('/') }}" data-login="{{ route('login') }}">

<div class="mobile-topbar">
  <button id="menuToggle" class="btn-ghost" style="color:#fff; font-size:1.3rem; background:none; border:none; cursor:pointer;">☰</button>
  <strong id="mobileTitle">منارة</strong>
  <span></span>
</div>
<div class="sidebar-scrim" id="sidebarScrim"></div>

<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <div style="padding: 20px;">
      <p style="margin-bottom: 5px; font-weight: bold;">{{ Auth::user()->name }}</p>
      <p style="font-size: 0.85rem; color: #888; margin-bottom: 15px;">مدير النظام</p>
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
          <h1>نظرة عامة على المركز</h1>
          <p>أهلاً بك أ. {{ Auth::user()->name }}، تابع من هنا مؤشرات المركز الرئيسية.</p>
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
          <span class="glyph">🧭</span>
          <div class="num">{{ $managementCount }}</div>
          <div class="lbl">فرد إداري</div>
        </div>
        <div class="card stat-card">
          <span class="glyph">👥</span>
          <div class="num">{{ $totalUsers }}</div>
          <div class="lbl">إجمالي مستخدمي المنصة</div>
        </div>
      </div>

      <div class="card section-card">
        <div class="section-head">
          <h2>أحدث الإعلانات</h2>
          <a href="#announcements" class="btn btn-outline btn-sm">عرض الكل</a>
        </div>
        <div id="overviewAnnouncements"></div>
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
        <div id="studentsEmpty" class="empty-state" hidden><div class="glyph">🎓</div><p>لا يوجد طلاب بعد. أضف أول طالب من الزر أعلاه.</p></div>
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

    <!-- ===== فريق الإدارة ===== -->
    <section data-section="management" hidden>
      <div class="main-head">
        <div><h1>فريق الإدارة</h1><p>صلاحية إضافة أعضاء الإدارة وتعديلهم وحذفهم متاحة للمدير فقط.</p></div>
        <button class="btn btn-primary" id="addMgmtBtn">+ إضافة عضو إدارة</button>
      </div>
      <div class="card section-card">
        <table class="data-table">
          <thead><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>القسم</th><th>تاريخ الالتحاق</th><th>إجراءات</th></tr></thead>
          <tbody id="mgmtTable"></tbody>
        </table>
        <div id="mgmtEmpty" class="empty-state" hidden><div class="glyph">🧭</div><p>لا يوجد أعضاء إدارة بعد.</p></div>
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

<!-- ===== نافذة عضو الإدارة ===== -->
<div class="modal-backdrop" id="mgmtModal">
  <div class="modal">
    <h3 id="mgmtModalTitle">إضافة عضو إدارة</h3>
    <form id="mgmtForm">
      <input type="hidden" id="mgmtId">
      <div class="field"><label>الاسم الكامل</label><input type="text" id="mgmtName" required></div>
      <div class="field"><label>البريد الإلكتروني</label><input type="email" id="mgmtEmail" required></div>
      <div class="field"><label>كلمة المرور</label><input type="text" id="mgmtPassword" placeholder="اتركها كما هي عند التعديل" required></div>
      <div class="field"><label>رقم الهاتف</label><input type="tel" id="mgmtPhone"></div>
      <div class="field"><label>القسم</label><input type="text" id="mgmtDept"></div>
      <div class="modal-actions">
        <button type="submit" class="btn btn-primary">حفظ</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('mgmtModal')">إلغاء</button>
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

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
<script src="{{ asset('js/dashboard-admin.js') }}"></script>
</body>
</html>