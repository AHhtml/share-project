<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة الطالب — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
  /* تنسيق القائمة المنسدلة الفرعية للمواد */
  .sub-menu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    padding-right: 15px;
    background: rgba(0, 0, 0, 0.02);
  }
  .sub-menu.open {
    max-height: 300px; /* تفتح بحسب عدد المواد */
    transition: max-height 0.3s ease-in;
  }
  .sub-menu a {
    font-size: 0.9rem !important;
    padding: 8px 12px !important;
    opacity: 0.85;
  }
  .sub-menu a:hover {
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
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <h2>منارة</h2>
      <span>لوحة الطالب</span>
    </div>
    <nav class="sidebar-nav">
      <a href="#" class="active" data-target="overview" onclick="showOverviewSection()">🏠 الرئيسية</a>
      
      <!-- خيار المواد المسجلة الذي يتحكم بالقائمة المنسدلة -->
      <a href="#" id="toggleSubjectsMenu" onclick="toggleSubMenu(event)" style="display: flex; justify-content: space-between; align-items: center;">
        <span>📖 المواد المسجلة</span>
        <span id="menuArrow" style="font-size: 0.8rem; transition: transform 0.3s;">▼</span>
      </a>
      
      <!-- القائمة الفرعية التي تظهر تحت المواد المسجلة وتجلب المواد ديناميكياً -->
      <div class="sub-menu" id="subjectsSubMenu">
        @forelse($enrolledSubjects as $subject)
          <a href="#" onclick="goToSubjectView('{{ $subject->name }}', {{ json_encode($subject->lessons) }}, {{ json_encode($subject->assignments) }})" style="display: block; text-decoration: none; color: inherit;">
            ▪ {{ $subject->name }}
          </a>
        @empty
          <span style="display: block; padding: 8px 12px; font-size: 0.85rem; color: #888;">لا توجد مواد</span>
        @endforelse
      </div>

      <a href="{{ route('student.announcements.index') }}" data-target="announcements" >📢 الإعلانات</a>
      
      <!-- زر تعديل البيانات الشخصية -->
      <a href="{{ route('student.profile.edit') }}">تعديل بياناتي الشخصية</a>
    </nav>
    <div style="padding: 20px; border-top: 1px solid rgba(0,0,0,0.05); margin-top: auto;">
      <p style="margin-bottom: 10px; font-weight: bold; font-size: 0.9rem;">{{ Auth::user()->name }}</p>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #dc3545; border: none;">تسجيل الخروج</button>
      </form>
    </div>
  </aside>

  <main class="main">

    @if(session('success'))
      <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
        {{ session('success') }}
      </div>
    @endif

    <!-- ===== الرئيسية ===== -->
    <section data-section="overview">
      <div class="main-head">
        <div>
          <h1>مرحباً بك، {{ Auth::user()->name }}</h1>
        </div>
      </div>

      <!-- مواد الطالب في الرئيسية -->
      <div style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 12px; font-size: 1.2rem; color: #1a1a1a;">موادي الدراسية</h3>
        <div class="stat-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
          @forelse($enrolledSubjects as $subject)
            <div class="card stat-card" onclick="goToSubjectView('{{ $subject->name }}', {{ json_encode($subject->lessons) }}, {{ json_encode($subject->assignments) }})" style="cursor: pointer; text-align: right; padding: 20px;">
              <div style="font-size: 1.8rem; margin-bottom: 8px;">📖</div>
              <div class="num" style="font-size: 1.2rem; margin-bottom: 4px; color: #1a1a1a;">{{ $subject->name }}</div>
              <div class="lbl" style="font-size: 0.85rem; color: #666;">
                {{ $subject->lessons->count() }} محاضرة | {{ $subject->assignments->count() }} اختبار
              </div>
            </div>
          @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 30px; color: #666;">
              لست مسجلاً في أي مادة دراسية حتى الآن.
            </div>
          @endforelse
        </div>
      </div>

      <div class="card section-card">
        <div class="section-head">
          <h2>أحدث ما نزّله معلموك</h2>
          <a href="#lectures" class="btn btn-outline btn-sm" onclick="showSection(event, 'lectures')">عرض كل المحاضرات</a>
        </div>
        <div id="overviewLectures">
          @forelse($latestLessons as $lesson)
            <div style="padding: 12px 0; border-bottom: 1px solid var(--border-color, #eee);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <strong>{{ $lesson->title }}</strong>
                @if($lesson->subject)
                  <span style="background: #e2e8f0; color: #1e293b; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">
                    📚 {{ $lesson->subject->name }}
                  </span>
                @endif
              </div>
              <a href="{{ $lesson->description }}" target="_blank" style="margin: 0 0 6px 0; font-size: 0.85rem; color: #666;">🔗 رابط المحاضرة</a>
              @if(!empty($lesson->video_url))
                <div style="margin-top: 4px;">
                  <a href="{{ $lesson->video_url }}" target="_blank" style="font-size: 0.85rem; color: #2563eb; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    🔗 رابط الفيديو
                  </a>
                </div>
              @endif
            </div>
          @empty
            <p style="color: #888; font-size: 0.9rem; margin-top: 10px;">لا يوجد محاضرات صادرة حديثاً.</p>
          @endforelse
        </div>
      </div>
    </section>

    <!-- ===== واجهة تفاصيل المادة المحددة ===== -->
    <section data-section="subject-details" hidden>
      <div class="main-head">
        <div>
          <h1 id="viewSubjectTitle">اسم المادة</h1>
          <p>جميع المحاضرات والاختبارات التي أنزلها المعلم لهذه المادة.</p>
        </div>
        <button class="btn btn-outline btn-sm" onclick="showOverviewSection()">العودة للرئيسية</button>
      </div>

      <!-- قسم المحاضرات الخاصة بالمادة -->
      <div class="card section-card" style="margin-bottom: 20px;">
        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;">📚 محاضرات المادة</h3>
        <div id="viewSubjectLessonsList">
          <!-- سيتم تعبئتها ديناميكياً -->
        </div>
      </div>

      <!-- قسم الاختبارات الخاصة بالمادة -->
      <div class="card section-card">
        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;">📝 اختبارات وواجبات المادة</h3>
        <div id="viewSubjectAssignmentsList">
          <!-- سيتم تعبئتها ديناميكياً -->
        </div>
      </div>
    </section>

    <!-- ===== المحاضرات ===== -->
    <section data-section="lectures" hidden>
      <div class="main-head">
        <div><h1>المحاضرات</h1><p>كل محاضرة ينزّله معلمك تصلك هنا فور نشرها، مرتبة بحسب موادك المسجلة.</p></div>
      </div>
      <div id="lecturesList"></div>
      <div id="lecturesEmpty" class="card empty-state" hidden><div class="glyph">🎥</div><p>لم يُنشر أي محتوى بعد. تابع لاحقاً.</p></div>
    </section>

    <!-- ===== الاختبارات ===== -->
    <section data-section="exams" hidden>
      <div class="main-head">
        <div><h1>الاختبارات</h1><p>اختبارات مسارك المجدولة، مع نتيجتك عند توفرها.</p></div>
      </div>
      <div id="examsList"></div>
      <div id="examsEmpty" class="card empty-state" hidden><div class="glyph">📝</div><p>لا يوجد اختبارات مجدولة حالياً.</p></div>
    </section>

    <!-- ===== الإعلانات ===== -->
    <section data-section="announcements" hidden>
      <div class="main-head">
        <div><h1>الإعلانات</h1><p>تعميمات المركز والإدارة والمعلمين.</p></div>
      </div>
      <div class="card section-card" id="annList">
        @forelse($announcements ?? [] as $announcement)
          <div style="padding: 16px 0; border-bottom: 1px solid var(--border-color, #eee);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <strong style="font-size: 1.05rem; color: #0f172a;">{{ $announcement->title }}</strong>
              @if($announcement->subject)
                <span style="background: #e2e8f0; color: #1e293b; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">
                  📚 {{ $announcement->subject->name }}
                </span>
              @endif
            </div>
            <p style="margin: 0 0 8px 0; font-size: 0.9rem; color: #334155; line-height: 1.5;">{{ $announcement->body }}</p>
            <div style="font-size: 0.75rem; color: #64748b; display: flex; gap: 12px;">
              @if($announcement->teacher)
                <span>👨‍🏫 المعلم: {{ $announcement->teacher->name }}</span>
              @endif
              <span>📅 {{ $announcement->created_at->diffForHumans() }}</span>
            </div>
          </div>
        @empty
          <div class="empty-state" style="text-align: center; padding: 30px; color: #666;">
            <div class="glyph" style="font-size: 2rem; margin-bottom: 8px;">📢</div>
            <p style="margin: 0; font-size: 0.9rem;">لا توجد إعلانات منشورة حتى الآن.</p>
          </div>
        @endforelse
      </div>
    </section>

    <!-- ===== تعديل البيانات الشخصية ===== -->
    <section data-section="profile" hidden>
      <div class="main-head" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h1>تعديل البيانات الشخصية</h1>
          <p style="color: #666; margin-top: 4px;">يمكنك تعديل اسمك، البريد الإلكتروني، أو تغيير كلمة المرور الخاصة بك.</p>
        </div>
        <button class="btn btn-outline btn-sm" onclick="showOverviewSection()">العودة للرئيسية</button>
      </div>

      <div class="card section-card" style="max-width: 600px;">
        <form action="{{ route('student.profile.update') }}" method="POST">
          @csrf
          @method('PATCH')

          <div class="field" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem;">الاسم الكامل</label>
            <input type="text" name="name" value="{{ Auth::user()->name }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
          </div>

          <div class="field" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem;">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ Auth::user()->email }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
          </div>

          <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

          <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 15px;">اترك حقل كلمة المرور فارغاً إذا كنت لا تريد تغييرها.</p>

          <div class="field" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem;">كلمة المرور الجديدة</label>
            <input type="password" name="password" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
          </div>

          <div class="field" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem;">تأكيد كلمة المرور الجديدة</label>
            <input type="password" name="password_confirmation" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
          </div>

          <button type="submit" class="btn btn-primary" style="background: #0f172a; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">حفظ التعديلات</button>
        </form>
      </div>
    </section>

  </main>
</div>

<!-- ===== نافذة تفاصيل المحاضرة ===== -->
<div class="modal-backdrop" id="lectureViewModal">
  <div class="modal">
    <h3 id="lvTitle">عنوان المحاضرة</h3>
    <p id="lvMeta" style="color:var(--ink-soft); font-size:0.88rem; margin-bottom:1rem;"></p>
    <p id="lvNotes"></p>
    <div class="modal-actions">
      <button type="button" class="btn btn-primary" onclick="UI.closeModal('lectureViewModal')">تم</button>
    </div>
  </div>
</div>

<!-- ===== نافذة أداء الاختبار ===== -->
<div class="modal-backdrop" id="examTakeModal">
  <div class="modal">
    <h3 id="etTitle">اختبار</h3>
    <p style="color:var(--ink-soft); margin-bottom:1rem;" id="etMeta"></p>
    <form id="examTakeForm">
      <input type="hidden" id="etExamId">
      <div class="field">
        <label id="etScoreLabel">علامتك</label>
        <input type="number" id="etScore" min="0" required>
      </div>
      <p style="font-size:0.8rem; color:var(--ink-soft);">بيئة تجريبية: أدخل علامتك لمحاكاة رصدها في سجلك.</p>
      <div class="modal-actions">
        <button type="submit" class="btn btn-primary">حفظ العلامة</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('examTakeModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<script>
  // دالة فتح وإغلاق القائمة المنسدلة الفرعية للمواد
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

  // الانتقال الأقسام العامة (مثل الإعلانات والمحاضرات)
  function showSection(event, sectionName) {
    event.preventDefault();
    document.querySelectorAll('main > section').forEach(sec => sec.hidden = true);
    let targetSec = document.querySelector(`section[data-section="${sectionName}"]`);
    if (targetSec) targetSec.hidden = false;
  }

  // إظهار قسم تعديل الملف الشخصي
  function showProfileSection(event) {
    event.preventDefault();
    document.querySelectorAll('main > section').forEach(sec => sec.hidden = true);
    document.querySelector('section[data-section="profile"]').hidden = false;
  }

  // الانتقال لواجهة تفاصيل المادة المحددة وتعبئة بياناتها
  function goToSubjectView(subjectName, lessons, assignments) {
    document.getElementById('viewSubjectTitle').innerText = 'مادة: ' + subjectName;
    
    // تعبئة المحاضرات مع رابط المحاضرة
    let lessonsContainer = document.getElementById('viewSubjectLessonsList');
    if (lessons.length > 0) {
      lessonsContainer.innerHTML = lessons.map(l => `
        <div style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
          <strong>${l.title}</strong>
          <br> <br>
          <a href="${l.description}" target="_blank" style="margin: 4px 0 4px 0; font-size: 0.85rem;">🔗 رابط المحاضرة</a>
          ${l.video_url ? `<a href="${l.video_url}" target="_blank" style="font-size: 0.85rem; color: #2563eb; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-right: 10px;">🔗 رابط الفيديو</a>` : ''}
        </div>
      `).join('');
    } else {
      lessonsContainer.innerHTML = '<p style="color: #888; font-size: 0.9rem;">لم ينزل المعلم أي محاضرات لهذه المادة بعد.</p>';
    }

    // تعبئة الاختبارات والواجبات مع زر تحميل الملف المرفق إن وجد
    let assignmentsContainer = document.getElementById('viewSubjectAssignmentsList');
    if (assignments.length > 0) {
      assignmentsContainer.innerHTML = assignments.map(a => `
        <div style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <strong>${a.title}</strong>
            <p style="margin: 4px 0 0; font-size: 0.85rem; color: #666;">${a.description || 'لا يوجد وصف'}</p>
            
            ${a.file_path ? `
              <div style="margin-top: 8px;">
                <a href="/student/assignments/${a.id}/download" style="background-color: #0ea5e9; color: #fff; padding: 5px 10px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                  📎 تحميل ملف الاختبار
                </a>
              </div>
            ` : ''}
          </div>
          <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: bold;">اختبار نشط</span>
        </div>
      `).join('');
    } else {
      assignmentsContainer.innerHTML = '<p style="color: #888; font-size: 0.9rem;">لا توجد اختبارات لهذه المادة حالياً.</p>';
    }

    // إخفاء كافة الأقسام وإظهار قسم تفاصيل المادة
    document.querySelectorAll('main > section').forEach(sec => sec.hidden = true);
    document.querySelector('section[data-section="subject-details"]').hidden = false;
  }

  // العودة للرئيسية
  function showOverviewSection() {
    document.querySelectorAll('main > section').forEach(sec => sec.hidden = true);
    document.querySelector('section[data-section="overview"]').hidden = false;
  }
</script>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
<script src="{{ asset('js/dashboard-student.js') }}"></script>
</body>
</html>