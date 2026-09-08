<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>اختباراتي — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; margin: 0; }
  .page-container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
  .header-card {
    background: #fff; padding: 24px; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex;
    justify-content: space-between; align-items: center; margin-bottom: 24px;
  }
  .header-card h1 { margin: 0 0 6px 0; font-size: 1.6rem; color: #1a1a1a; }
  .header-card p { margin: 0; color: #6c757d; font-size: 0.95rem; }
  .assignments-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 24px; }
  .item-row { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid #e9ecef; }
  .item-row:last-child { border-bottom: none; }
  .item-info h3 { margin: 0 0 6px 0; font-size: 1.1rem; color: #212529; }
  .item-meta { font-size: 0.85rem; color: #6c757d; display: flex; gap: 15px; margin-bottom: 6px; flex-wrap: wrap; }
  .empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
  .empty-state .glyph { font-size: 3rem; margin-bottom: 12px; }
  .actions-group { display: flex; gap: 8px; align-items: center; }
  .btn-add { background-color: #d97706; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; }
  .btn-add:hover { background-color: #b45309; }
  .back-link { display: inline-flex; align-items: center; gap: 6px; margin-top: 20px; color: #495057; text-decoration: none; font-weight: 500; }
  .btn-file { background-color: #0ea5e9; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; }
  .btn-file:hover { background-color: #0284c7; }
  .file-badge { display: inline-flex; align-items: center; gap: 6px; background-color: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; color: #475569; margin-top: 6px; margin-bottom: 6px; border: 1px solid #e2e8f0; }
</style>
</head>
<body>

<div class="page-container">

  @if(session('success'))
    <div style="padding: 14px 18px; background-color: #d1e7dd; color: #0f5132; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;">
      {{ session('success') }}
    </div>
  @endif

  <div class="header-card">
    <div>
      <h1>📝 اختباراتي</h1>
      <p>جدولة وإدارة كافة الاختبارات والتقييمات الخاصة بالمسار.</p>
    </div>
    <a href="{{ route('teacher.assignments.create') }}" class="btn-add">+ اختبار جديد</a>
  </div>

  <div class="assignments-card">
    @forelse($assignments as $assignment)
      <div class="item-row">
        <div class="item-info">
          <h3>{{ $assignment->title }}</h3>
          <div class="item-meta">
            <span>📅 موعد التسليم/الاختبار: {{ $assignment->due_date }}</span>
            <span>⏱️ المدة: {{ $assignment->duration ?? 'غير محددة' }}</span>
            <span>💯 الدرجة الكاملة: {{ $assignment->total_marks }}</span>
          </div>

          {{-- عرض الملف المرفق واسمه مباشرة في الواجهة --}}
          @if($assignment->file_path)
            <div class="file-badge">
              <span>📎 ملف الاختبار: {{ basename($assignment->file_path) }}</span>
            </div>
          @endif

          <p style="margin:0; color:#495057; font-size:0.9rem;">{{ $assignment->description ?? 'لا يوجد وصف مضاف.' }}</p>
        </div>
        
        <div class="actions-group">
          @if($assignment->file_path)
            <a href="{{ asset('storage/' . $assignment->file_path) }}" target="_blank" class="btn-file">
              📎 تحميل
            </a>
          @endif

          <a href="{{ route('teacher.assignments.edit', $assignment->id) }}" class="btn btn-outline btn-sm" style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; color: #333;">تعديل</a>
          
          <form action="{{ route('teacher.assignments.destroy', $assignment->id) }}" method="POST" class="delete-form d-inline" style="margin: 0;">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-sm delete-btn" style="background-color: #dc3545; color: #fff; border: none; cursor: pointer; padding: 6px 12px; border-radius: 6px;">حذف</button>
          </form>
        </div>
      </div>
    @empty
      <div class="empty-state">
        <div class="glyph">📝</div>
        <p style="font-size: 1.1rem; font-weight: 500; margin-bottom: 6px;">لا توجد اختبارات مجدولة بعد</p>
        <p style="font-size: 0.9rem; margin-top: 0;">يمكنك البدء بجدولة أول اختبار عبر الضغط على الزر أعلاه.</p>
      </div>
    @endforelse
  </div>

  <a href="{{ route('teacher.dashboard') }}" class="back-link">→ العودة للوحة التحكم</a>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');

            Swal.fire({
                title: 'هل أنت متأكد من الحذف؟',
                text: "لن يمكنك التراجع عن هذا الإجراء بعد إتمامه!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، قم بالحذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

</body>
</html>