<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>الواجبات والاختبارات — لوحة الطالب</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; margin: 0; }
  .page-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
  .header-card { background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 24px; }
  .header-card h1 { margin: 0 0 6px 0; font-size: 1.6rem; color: #1a1a1a; }
  .header-card p { margin: 0; color: #6c757d; font-size: 0.95rem; }
  .content-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 24px; }
  .assignment-item { padding: 16px 0; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; }
  .assignment-item:last-child { border-bottom: none; }
  .badge-done { background-color: #d1e7dd; color: #0f5132; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; }
  .badge-pending { background-color: #fff3cd; color: #664d03; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; }
  .btn-download { display: inline-flex; align-items: center; gap: 6px; background-color: #e9ecef; color: #333; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; margin-top: 8px; transition: background 0.2s; }
  .btn-download:hover { background-color: #dee2e6; color: #000; }
  .back-link { display: inline-flex; align-items: center; gap: 6px; margin-top: 20px; color: #495057; text-decoration: none; font-weight: 500; }
</style>
</head>
<body>

<div class="page-container">
  <div class="header-card">
    <h1>📝 الواجبات والاختبارات</h1>
    <p>تابع الاختبارات المطلوبة منك وحالة إنجازها وحمل الملفات المرفقة.</p>
  </div>

  <div class="content-card">
    @forelse($assignments as $assignment)
      <div class="assignment-item">
        <div>
          <div style="font-size: 1.1rem; font-weight: bold; color: #212529; margin-bottom: 4px;">{{ $assignment->title }}</div>
          <div style="color: #6c757d; font-size: 0.9rem; margin-bottom: 6px;">{{ $assignment->description ?? 'لا يوجد وصف إضافي' }}</div>
          
          <!-- زر تحميل الملف المرفق إذا وجد -->
          @if(!empty($assignment->file_path))
            <div>
              <a href="{{ asset('storage/' . $assignment->file_path) }}" class="btn-download" download>
                📥 تحميل ملف الواجب
              </a>
            </div>
          @endif
        </div>
        
        <div>
          @if(in_array($assignment->id, $submittedAssignmentIds))
            <span class="badge-done">تم الإنجاز ✓</span>
          @else
            <span class="badge-pending">قيد الانتظار</span>
          @endif
        </div>
      </div>
    @empty
      <div style="text-align: center; padding: 40px; color: #6c757d;">لا توجد واجبات أو اختبارات مطلوبة حالياً</div>
    @endforelse
  </div>

  <a href="{{ route('student.dashboard') }}" class="back-link">→ العودة للوحة التحكم</a>
</div>

</body>
</html>