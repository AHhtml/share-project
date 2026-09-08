<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إعلانات الطالب — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
  body {
    background-color: #0f2922;
    font-family: 'Tajawal', sans-serif;
    margin: 0;
    padding: 0;
    color: #f8fafc;
  }
  .page-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
  }
  .top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    background: #13382d;
    padding: 20px 25px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .top-bar h1 {
    font-size: 1.6rem;
    color: #f8fafc;
    margin: 0 0 5px 0;
  }
  .top-bar p {
    color: #a7f3d0;
    margin: 0;
    font-size: 0.95rem;
  }
  .btn-dashboard {
    background-color: #d97706;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s, transform 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .btn-dashboard:hover {
    background-color: #b45309;
    transform: translateY(-1px);
  }
  .announcement-card {
    background: #13382d;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-right: 5px solid #d97706;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .announcement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
  }
  .announcement-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-size: 0.85rem;
    color: #a7f3d0;
  }
  .badge-subject {
    background: rgba(217, 119, 6, 0.2);
    color: #fcd34d;
    padding: 4px 12px;
    border-radius: 6px;
    font-weight: 600;
  }
  .announcement-body {
    font-size: 1.1rem;
    color: #f1f5f9;
    line-height: 1.7;
    margin: 0;
  }
  .empty-state {
    background: #13382d;
    padding: 40px;
    text-align: center;
    border-radius: 12px;
    color: #a7f3d0;
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    font-size: 1.1rem;
  }
</style>
</head>
<body>

<div class="page-container">
  <!-- رأس الصفحة وزر لوحة التحكم -->
  <div class="top-bar">
    <div>
      <h1>الإعلانات</h1>
      <p>تعميمات المعلمين والمادة الدراسية الخاصة بك.</p>
    </div>
    <a href="{{ route('student.dashboard') }}" class="btn-dashboard">
      🏠 لوحة التحكم
    </a>
  </div>

  <!-- قائمة الإعلانات -->
  <div>
    @forelse($announcements as $announcement)
      <div class="announcement-card">
        <!-- معلومات المادة والمعلم والتاريخ -->
        <div class="announcement-meta">
          <div>
            <span class="badge-subject">
              📚 {{ $announcement->subject->name ?? 'عام' }}
            </span>
            <span style="margin-right: 15px; font-weight: 500; color: #e2e8f0;">
              👨‍🏫 المعلم: {{ $announcement->teacher->name ?? 'الإدارة' }}
            </span>
          </div>
          <span style="color: #94a3b8;">{{ $announcement->created_at->format('Y-m-d') }}</span>
        </div>

        <!-- محتوى الإعلان -->
        <p class="announcement-body">
          {{ $announcement->body ?? $announcement->content ?? 'لا يوجد نص للإعلان' }}
        </p>
      </div>
    @empty
      <div class="empty-state">
        <p>لا توجد إعلانات منشورة حتى الآن.</p>
      </div>
    @endforelse
  </div>
</div>

</body>
</html>