<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>المحاضرات — لوحة الطالب</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<style>
  body { 
    font-family: 'Tajawal', sans-serif; 
    background-color: #f8fafc; 
    color: #1e293b;
    margin: 0; 
  }
  .page-container { 
    max-width: 900px; 
    margin: 40px auto; 
    padding: 0 20px; 
  }
  .header-card { 
    background: #ffffff; 
    padding: 24px 28px; 
    border-radius: 14px; 
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); 
    border: 1px solid #e2e8f0;
    margin-bottom: 24px; 
  }
  .header-card h1 { 
    margin: 0 0 6px 0; 
    font-size: 1.5rem; 
    color: #0f172a; 
    font-weight: 900;
  }
  .header-card p { 
    margin: 0; 
    color: #64748b; 
    font-size: 0.95rem; 
  }
  .content-card { 
    background: #ffffff; 
    border-radius: 14px; 
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); 
    border: 1px solid #e2e8f0;
    padding: 24px 28px; 
  }
  .lesson-item { 
    padding: 20px 0; 
    border-bottom: 1px solid #f1f5f9; 
  }
  .lesson-item:last-child { 
    border-bottom: none; 
  }
  .lesson-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 6px;
  }
  .lesson-title { 
    font-size: 1.15rem; 
    font-weight: 700; 
    color: #0f172a; 
  }
  .subject-badge {
    background: #f1f5f9;
    color: #334155;
    font-size: 0.78rem;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
  }
  .lesson-desc { 
    color: #475569; 
    font-size: 0.95rem; 
    margin-bottom: 12px; 
    line-height: 1.6;
  }
  .lesson-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
  }
  .lesson-meta { 
    font-size: 0.85rem; 
    color: #64748b; 
  }
  .btn-link { 
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #0f172a;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none; 
    font-size: 0.85rem;
    font-weight: 600;
    transition: background 0.2s;
  }
  .btn-link:hover {
    background: #1e293b;
  }
  .back-link { 
    display: inline-flex; 
    align-items: center; 
    gap: 6px; 
    margin-top: 24px; 
    color: #64748b; 
    text-decoration: none; 
    font-weight: 600; 
    font-size: 0.9rem;
    transition: color 0.2s;
  }
  .back-link:hover {
    color: #0f172a;
  }
</style>
</head>
<body>

<div class="page-container">
  <div class="header-card">
    <h1>📚 أحدث المحاضرات المتاحة</h1>
    <p>استعرض المحاضرات والدروس المرفوعة حديثاً من المعلمين لجميع موادك المسجلة.</p>
  </div>

  <div class="content-card">
    @forelse($lessons as $lesson)
      <div class="lesson-item">
        <div class="lesson-header">
          <div class="lesson-title">{{ $lesson->title }}</div>
          <!-- عرض اسم المادة التابعة لها المحاضرة (تأكد من وجود علاقة subject في موديل Lesson) -->
          @if(isset($lesson->subject))
            <span class="subject-badge">📖 {{ $lesson->subject->name }}</span>
          @endif
        </div>

        <a href="{{ $lesson->description }}" class="lesson-desc">{{ $lesson->description }}</a>

        <div class="lesson-footer">
          <div class="lesson-meta">📅 تاريخ النشر: {{ $lesson->created_at->format('Y-m-d') }}</div>
          
          @if($lesson->video_url)
            <a href="{{ $lesson->video_url }}" target="_blank" class="btn-link">🔗 مشاهدة الفيديو / المحتوى</a>
          @endif
        </div>
      </div>
    @empty
      <div style="text-align: center; padding: 40px; color: #64748b; font-size: 0.95rem;">
        لا توجد محاضرات متاحة حالياً.
      </div>
    @endforelse
  </div>

  <a href="{{ route('student.dashboard') }}" class="back-link">← العودة للوحة التحكم</a>
</div>

</body>
</html>