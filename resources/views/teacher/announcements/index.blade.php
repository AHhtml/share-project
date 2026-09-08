<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إعلانات المعلم — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<style>
  .app-shell {
    display: flex !important;
    min-height: 100vh !important;
    position: relative;
    overflow-x: hidden;
  }
  
  .sidebar {
    width: 260px !important;
    flex-shrink: 0;
    background: #132c27 !important;
    color: #fff !important;
    border-left: 1px solid rgba(255,255,255,0.1);
    transition: transform 0.3s ease-in-out;
  }

  .sidebar p, .sidebar span, .sidebar strong {
    color: inherit !important;
  }

  .main {
    flex: 1 !important;
    padding: 30px !important;
    overflow-y: auto;
    width: 100%;
  }

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

  .announcement-card {
    background: #fff;
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }

  .announcement-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  .announcement-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
  }

  .announcement-date {
    font-size: 0.85rem;
    color: #64748b;
  }

  .announcement-body {
    font-size: 0.95rem;
    color: #475569;
    line-height: 1.6;
    margin-bottom: 15px;
  }

  .announcement-footer {
    display: flex;
    justify-content: flex-end;
    border-top: 1px solid #f1f5f9;
    padding-top: 12px;
  }

  @media (max-width: 992px) {
    .app-shell {
      flex-direction: column !important;
    }
    .sidebar {
      position: fixed !important;
      top: 0 !important;
      right: -280px !important;
      width: 280px !important;
      height: 100% !important;
      z-index: 1050 !important;
      box-shadow: -5px 0 15px rgba(0,0,0,0.1);
    }
    .sidebar.active {
      right: 0 !important;
    }
    .mobile-topbar {
      display: flex !important;
      align-items: center;
      justify-content: space-between;
      padding: 12px 16px;
      background: #132c27;
      color: #fff;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .sidebar-scrim.active {
      display: block !important;
    }
    .main {
      padding: 15px !important;
    }
  }
</style>
</head>
<body data-home="{{ url('/') }}" data-login="{{ route('login') }}">

<div class="mobile-topbar">
  <button id="menuToggle" style="color:#fff; font-size:1.4rem; background:none; border:none; cursor:pointer;">☰</button>
  <strong>منارة — إعلانات المعلم</strong>
  <span></span>
</div>
<div class="sidebar-scrim" id="sidebarScrim"></div>

<div class="app-shell">
  <!-- القائمة الجانبية -->
  <aside class="sidebar" id="sidebar">
    <div style="padding: 20px;">
      <p style="margin-bottom: 2px; font-weight: bold;">{{ Auth::user()->name }}</p>
      <p style="font-size: 0.85rem; color: #d97706; margin-bottom: 15px; font-weight: 600;">
        معلم 
        @if(isset($teacherSubjects) && $teacherSubjects->count() > 0)
          {{ $teacherSubjects->pluck('name')->implode('، ') }}
        @endif
      </p>
      
      <!-- قائمة التنقل الجانبية -->
      <nav class="sidebar-nav" style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
        {{-- <button type="button" class="btn btn-ghost nav-item active" onclick="switchSection('overview')" style="text-align: right; justify-content: start;">📊 الرئيسية</button> --}}
        <a href="{{ route('teacher.dashboard') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📊 الرئيسية</a>
        <a href="{{ route('teacher.lessons.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">🎥 محاضراتي</a>
        <a href="{{ route('teacher.assignments.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📝 اختباراتي</a>
        <a href="{{ route('teacher.students.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">🎓 طلابي</a>
        {{-- <a href="#" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">🎓 اعلاناتي</a> --}}
        <a href="{{ route('teacher.announcements.index') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📢 الإعلانات</a>
        <a href="{{ route('teacher.profile') }}" class="btn btn-ghost nav-item" style="text-align: right; justify-content: start; text-decoration: none;">📊 ملفي الشخصي</a>
      </nav>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" style="width: 100%; background-color: #dc3545; border: none;">تسجيل الخروج</button>
      </form>
    </div>
  </aside>

  <!-- المحتوى الرئيسي -->
  <main class="main">
    @if(session('success'))
      <div style="padding: 12px 16px; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
      </div>
    @endif

    <div class="main-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 25px;">
      <div>
        <h1>قائمة الإعلانات</h1>
        <p>استعرض كافة التعاميم والإعلانات الخاصة بالمنصة والمساق.</p>
      </div>
      <button class="btn btn-primary" onclick="UI.openModal('annModal')">+ إعلان جديد</button>
    </div>

    <div id="announcementsList">
      @forelse($announcements ?? [] as $ann)
        <div class="announcement-card">
          <div class="announcement-header">
            <span class="announcement-title">📢 {{ $ann->title }}</span>
            <span class="announcement-date">{{ date('Y-m-d', strtotime($ann->created_at)) }}</span>
          </div>
          <div class="announcement-body">
            <p>{{ $ann->body }}</p>
          </div>
          <div class="announcement-footer">
            <button type="button" class="btn btn-ghost" style="color: #dc3545; border-color: #f8d7da; background-color: #fff5f5; font-size: 0.85rem; padding: 6px 12px;" onclick="prepareDelete('{{ $ann->id }}', '{{ addslashes($ann->title) }}')">
            🗑️ حذف الإعلان
            </button>
          </div>
        </div>
      @empty
        <div class="card" style="padding: 30px; text-align: center; color: #666;">
          <p>لا توجد إعلانات منشورة حتى الآن.</p>
        </div>
      @endforelse
    </div>
  </main>
</div>

<!-- نافذة إضافة إعلان جديد -->
<div class="modal-backdrop" id="annModal">
  <div class="modal">
    <h3>إعلان جديد</h3>
    <form action="{{ route('teacher.announcements.store') }}" method="POST">
      @csrf
      <div class="field"><label>عنوان الإعلان</label><input type="text" name="title" required placeholder="أدخل عنوان الإعلان..."></div>
      <div class="field"><label>نص الإعلان</label><textarea name="body" required rows="4" placeholder="اكتب تفاصيل الإعلان هنا..."></textarea></div>
      <div class="modal-actions">
        <button type="submit" class="btn btn-primary">نشر الإعلان</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('annModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<!-- نافذة تأكيد الحذف الموحدة -->
<div class="modal-backdrop" id="deleteAnnouncementModal">
  <div class="modal" style="text-align: center; max-width: 400px;">
    <div style="font-size: 2.5rem; margin-bottom: 10px;">⚠️</div>
    <h3 style="margin-bottom: 10px; color: #1e293b;">تأكيد الحذف</h3>
    <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 20px;">هل أنت متأكد من رغبتك في حذف الإعلان (<span id="deleteItemTitle" style="font-weight: bold; color: #1e293b;"></span>)؟</p>
    
    <form id="deleteForm" method="POST">
      @csrf
      @method('DELETE')
      <div class="modal-actions" style="display: flex; gap: 10px; justify-content: center;">
        <button type="submit" class="btn btn-primary" style="background-color: #dc3545; border: none; flex: 1;">نعم، حذف</button>
        <button type="button" class="btn btn-ghost" onclick="UI.closeModal('deleteAnnouncementModal')" style="flex: 1;">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>

<script>
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  const sidebarScrim = document.getElementById('sidebarScrim');

  if (menuToggle && sidebar && sidebarScrim) {
    menuToggle.onclick = function() {
      sidebar.classList.toggle('active');
      sidebarScrim.classList.toggle('active');
    };

    sidebarScrim.onclick = function() {
      sidebar.classList.remove('active');
      sidebarScrim.classList.remove('active');
    };
  }

  function prepareDelete(id, title) {
    const form = document.getElementById('deleteForm');
    const titleSpan = document.getElementById('deleteItemTitle');
    
    form.action = "{{ url('teacher/announcements') }}/" + id;
    titleSpan.textContent = title;
    
    console.log("Delete URL is: " + form.action); // تم تصحيح علامة الربط هنا
    UI.openModal('deleteAnnouncementModal');
  }
</script>
</body>
</html>