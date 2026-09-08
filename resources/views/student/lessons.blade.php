<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>المحاضرات — لوحة الطالب</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; margin: 0; }
  .page-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
  .header-card { background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 24px; }
  .header-card h1 { margin: 0 0 6px 0; font-size: 1.6rem; color: #1a1a1a; }
  .header-card p { margin: 0; color: #6c757d; font-size: 0.95rem; }
  .content-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 24px; }
  .lesson-item { padding: 16px 0; border-bottom: 1px solid #e9ecef; }
  .lesson-item:last-child { border-bottom: none; }
  .lesson-title { font-size: 1.1rem; font-weight: bold; color: #212529; margin-bottom: 6px; }
  .lesson-desc { color: #495057; font-size: 0.95rem; margin-bottom: 8px; }
  .lesson-meta { font-size: 0.85rem; color: #6c757d; }
  .btn-link { display: inline-block; margin-top: 8px; color: #d97706; text-decoration: none; font-weight: bold; }
  .back-link { display: inline-flex; align-items: center; gap: 6px; margin-top: 20px; color: #495057; text-decoration: none; font-weight: 500; }
</style>
</head>
<body>

<div class="page-container">
  <div class="header-card">
    <h1>📚 المحاضرات المتاحة</h1>
    <p>استعرض المحاضرات والدروس المرفوعة من المعلمين.</p>
  </div>

  <div class="content-card">
    @forelse($lessons as $lesson)
      <div class="lesson-item">
        <div class="lesson-title">{{ $lesson->title }}</div>
        <div class="lesson-desc">{{ $lesson->description }}</div>
        @if($lesson->video_url)
          <a href="{{ $lesson->video_url }}" target="_blank" class="btn-link">🔗 مشاهدة الفيديو / المحتوى</a>
        @endif
        <div class="lesson-meta" style="margin-top: 6px;">📅 التاريخ: {{ $lesson->created_at->format('Y-m-d') }}</div>
      </div>
    @empty
      <div style="text-align: center; padding: 40px; color: #6c757d;">لا توجد محاضرات متاحة حالياً</div>
    @endforelse
  </div>

  <a href="{{ route('student.dashboard') }}" class="back-link">→ العودة للوحة التحكم</a>
</div>

</body>
</html>