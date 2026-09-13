<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسليمات الطلاب — منارة</title>
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
    max-height: 300px;
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
  /* تنسيق أيقونة الإشعارات العلوية */
  .topbar-notifications {
    position: relative;
    display: inline-block;
  }
  .notifications-dropdown {
    position: absolute;
    right: 0;
    top: 45px;
    width: 320px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    display: none;
    z-index: 1000;
    border: 1px solid #e2e8f0;
    text-align: right;
  }
  .notifications-dropdown.show {
    display: block;
  }
  .notifications-header {
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
    font-weight: bold;
    font-size: 0.95rem;
    color: #1e293b;
    background: #f8fafc;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }
  .notifications-body {
    max-height: 300px;
    overflow-y: auto;
  }
  .notification-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
    color: #334155;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    position: relative;
    text-decoration: none;
    transition: background 0.2s;
  }
  .notification-item:hover {
    background: #f8fafc;
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
      <span>لوحة المعلم</span>
    </div>
    <nav class="sidebar-nav">
      <a href="{{ route('teacher.dashboard') }}">🏠 الرئيسية</a>
      <a href="{{ route('teacher.assignments.index') }}" class="active">📝 الاختبارات والواجبات</a>
      <a href="{{ route('teacher.announcements.index') }}">📢 الإعلانات</a>
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

    @if(session('error'))
      <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
        {{ session('error') }}
      </div>
    @endif

    <!-- ===== محتوى صفحة تسليمات الطلاب ===== -->
    <div class="main-head">
        <div>
            <h1>تسليمات الطلاب للاختبار: {{ $assignment->title }}</h1>
            <p>مادة: {{ $assignment->subject->name ?? 'غير محددة' }} | العلامة الكلية: {{ $assignment->total_marks }}</p>
        </div>
        <a href="{{ route('teacher.assignments.index') }}" class="btn btn-outline btn-sm">العودة للاختبارات</a>
    </div>

    <div class="card section-card">
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse; text-align: right;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                        <th style="padding: 12px;">اسم الطالب</th>
                        <th style="padding: 12px;">البريد الإلكتروني</th>
                        <th style="padding: 12px;">تاريخ التسليم</th>
                        <th style="padding: 12px;">ملاحظات الطالب</th>
                        <th style="padding: 12px;">ملف الحل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignment->submissions as $submission)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px; font-weight: 500;">{{ $submission->student->name ?? 'مستخدم محذوف' }}</td>
                            <td style="padding: 12px; color: #64748b;">{{ $submission->student->email ?? '-' }}</td>
                            <td style="padding: 12px; font-size: 0.85rem; color: #64748b;">{{ $submission->created_at->diffForHumans() }}</td>
                            <td style="padding: 12px; font-size: 0.85rem;">{{ $submission->notes ?? 'لا توجد ملاحظات' }}</td>
                            <td style="padding: 12px;">
                                @if($submission->solution_file)
                                    <a href="{{ route('teacher.assignments.submissions.download', $submission->id) }}" style="background-color: #0ea5e9; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                        📎 تحميل الحل
                                    </a>
                                @else
                                    <span style="color: #94a3b8; font-size: 0.85rem;">لا يوجد ملف</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #666;">
                                لم يقم أي طالب بتسليم هذا الواجب/الاختبار حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

  </main>
</div>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
</body>
</html>