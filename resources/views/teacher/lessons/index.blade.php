<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>محاضراتي — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<!-- تضمين مكتبة SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  body {
    font-family: 'Tajawal', sans-serif;
    background-color: #f8f9fa;
    margin: 0;
  }
  .page-container {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 20px;
  }
  .header-card {
    background: #fff;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  .header-card h1 {
    margin: 0 0 6px 0;
    font-size: 1.6rem;
    color: #1a1a1a;
  }
  .header-card p {
    margin: 0;
    color: #6c757d;
    font-size: 0.95rem;
  }
  .lessons-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    padding: 24px;
  }
  .lesson-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid #e9ecef;
  }
  .lesson-item:last-child {
    border-bottom: none;
  }
  .lesson-info h3 {
    margin: 0 0 6px 0;
    font-size: 1.1rem;
    color: #212529;
  }
  .lesson-meta {
    font-size: 0.85rem;
    color: #6c757d;
    display: flex;
    gap: 15px;
    margin-bottom: 6px;
  }
  .lesson-desc {
    margin: 0;
    color: #495057;
    font-size: 0.9rem;
  }
  .empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #6c757d;
  }
  .empty-state .glyph {
    font-size: 3rem;
    margin-bottom: 12px;
  }
  .actions-group {
    display: flex;
    gap: 8px;
  }
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 20px;
    color: #495057;
    text-decoration: none;
    font-weight: 500;
  }
  .back-link:hover {
    color: #0d6efd;
  }
</style>
</head>
<body>

<div class="page-container">

  @if(session('success'))
    <div style="padding: 14px 18px; background-color: #d1e7dd; color: #0f5132; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;">
      {{ session('success') }}
    </div>
  @endif

  <!-- الهيدر العلوي -->
  <div class="header-card">
    <div>
      <h1>🎥 محاضراتي</h1>
      <p>إدارة واستعراض جميع المحاضرات الخاصة بك في المنصة.</p>
    </div>
    <a href="{{ route('teacher.lessons.create') }}" class="btn btn-primary" style="padding: 10px 20px; text-decoration: none;">+ إضافة محاضرة جديدة</a>
    <!-- زر سلة المهملات/الأرشيف -->
    <a href="{{ route('teacher.lessons.trash') }}" style="background: #64748b; color: #fff; padding: 10px 18px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
        🗑️ سلة المهملات
    </a>
  </div>

  <!-- قائمة المحاضرات -->
  <div class="lessons-card">
    @forelse($lessons as $lesson)
      <div class="lesson-item">
        <div class="lesson-info">
          <h3>{{ $lesson->title }}</h3>
          <div class="lesson-meta">
            <span>📅 التاريخ: {{ $lesson->date }}</span>
            <span>⏱️ المدة: {{ $lesson->duration ?? 'غير محددة' }}</span>
          </div>
          <h3 style="margin: 0; font-size: 1rem;">
              <a href="{{ $lesson->description }}" target="_blank" style="color: #0d6efd; text-decoration: none; word-break: break-all; display: inline-flex; align-items: center; gap: 6px;">
                  <span>{{ $lesson->description }}</span>
                  {{-- <span>رابط المحاضرة</span> --}}
                  <span style="font-size: 0.85rem;">🔗</span>
              </a>
          </h3>
        </div>
        <div class="actions-group">
          <a href="{{ route('teacher.lessons.edit', $lesson->id) }}" class="btn btn-outline btn-sm">تعديل</a>
          
          <!-- نموذج الحذف بدون alert المتصفح العادي -->
          <form id="delete-form-{{ $lesson->id }}" action="{{ route('teacher.lessons.destroy', $lesson->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="button" onclick="confirmDelete({{ $lesson->id }})" class="btn btn-sm" style="background-color: #dc3545; color: #fff; border: none; cursor: pointer;">حذف</button>
          </form>

        </div>
      </div>
    @empty
      <div class="empty-state">
        <div class="glyph">🎥</div>
        <p style="font-size: 1.1rem; font-weight: 500; margin-bottom: 6px;">لا توجد محاضرات منشورة بعد</p>
        <p style="font-size: 0.9rem; margin-top: 0;">يمكنك البدء بإضافة أول محاضرة لطلابك عبر الضغط على الزر أعلاه.</p>
      </div>
    @endforelse
  </div>

  <a href="{{ route('teacher.dashboard') }}" class="back-link">→ العودة للوحة التحكم</a>

</div>

<!-- سكربت تأكيد الحذف نافذة احترافية -->
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "لن تتمكن من استعادة هذه المحاضرة بعد الحذف!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، احذفها',
        cancelButtonText: 'إلغاء',
        customClass: {
            popup: 'swal-wide'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>

</body>
</html>