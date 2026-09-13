<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>طلابي — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  :root {
    --primary-color: #d97706;
    --primary-hover: #b45309;
    --bg-color: #f8fafc;
    --card-bg: #ffffff;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --danger-color: #ef4444;
    --danger-hover: #dc2626;
  }

  body {
    font-family: 'Tajawal', sans-serif;
    background-color: var(--bg-color);
    color: var(--text-main);
    margin: 0;
    padding: 0;
  }

  .page-container {
    max-width: 1050px;
    margin: 40px auto;
    padding: 0 20px;
  }

  /* رسالة النجاح */
  .alert-success {
    background-color: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }

  /* الهيدر العلوي */
  .header-card {
    background: var(--card-bg);
    padding: 28px 32px;
    border-radius: 16px;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border: 1px solid var(--border-color);
  }

  .header-content h1 {
    margin: 0 0 6px 0;
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-main);
  }

  .header-content p {
    margin: 0;
    color: var(--text-muted);
    font-size: 1rem;
  }

  .btn-add {
    background-color: var(--primary-color);
    color: #fff;
    padding: 12px 24px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);
  }

  .btn-add:hover {
    background-color: var(--primary-hover);
    transform: translateY(-1px);
  }

  /* الحاوية الرئيسية للمحتوى */
  .content-card {
    background: var(--card-bg);
    border-radius: 16px;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    padding: 32px;
    border: 1px solid var(--border-color);
  }

  .subject-section {
    margin-bottom: 40px;
    background: #fafafa;
    border: 1px solid #edf2f7;
    border-radius: 14px;
    padding: 24px;
  }

  .subject-section:last-child {
    margin-bottom: 0;
  }

  .subject-title {
    font-size: 1.35rem;
    color: var(--text-main);
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 12px;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
  }

  .subject-title i {
    color: var(--primary-color);
  }

  /* صف الطالب */
  .item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: #fff;
    border-radius: 12px;
    margin-bottom: 12px;
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
  }

  .item-row:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    border-color: #cbd5e1;
  }

  .item-row:last-child {
    margin-bottom: 0;
  }

  .item-info h3 {
    margin: 0 0 6px 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-main);
  }

  .item-meta {
    font-size: 0.9rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* زر الإزالة */
  .btn-remove {
    background-color: #fef2f2;
    color: var(--danger-color);
    border: 1px solid #fecaca;
    padding: 8px 16px;
    border-radius: 8px;
    font-family: 'Tajawal', sans-serif;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
  }

  .btn-remove:hover {
    background-color: var(--danger-color);
    color: #fff;
    border-color: var(--danger-color);
  }

  /* الحالة الفارغة */
  .empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-muted);
  }

  .empty-state i {
    font-size: 2.5rem;
    color: #cbd5e1;
    margin-bottom: 12px;
  }

  .empty-state p {
    font-size: 1.05rem;
    font-weight: 500;
    margin: 0;
  }

  /* رابط العودة */
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 30px;
    color: var(--text-muted);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s ease;
  }

  .back-link:hover {
    color: var(--text-main);
  }
</style>
</head>
<body>

<div class="page-container">

  @if(session('success'))
    <div class="alert-success">
      <i class="fa-solid fa-circle-check"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <div class="header-card">
    <div class="header-content">
      <h1>🎓 طلابي</h1>
      <p>إدارة ومتابعة الطلاب المسجلين في المساقات الخاصة بك بكل سهولة.</p>
    </div>
    <a href="{{ route('teacher.students.create') }}" class="btn-add">
      <i class="fa-solid fa-user-plus"></i> طالب جديد
    </a>
  </div>

  <div class="content-card">
    @forelse($subjects as $subject)
      <div class="subject-section">
        <h2 class="subject-title">
          <i class="fa-solid fa-book-open"></i> مادة: {{ $subject->name }}
        </h2>
        
        @forelse($subject->students as $student)
          <div class="item-row">
            <div class="item-info">
              <h3>{{ $student->name }}</h3>
              <div class="item-meta">
                <i class="fa-regular fa-envelope"></i> {{ $student->email }}
              </div>
            </div>
            <div>
              <form action="{{ route('teacher.subjects.students.remove', ['subject' => $subject->id, 'student' => $student->id]) }}" method="POST" class="delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="btn-remove delete-btn">
                  <i class="fa-solid fa-user-minus"></i> إزالة من المساق
                </button>
              </form>

         

            </div>
          </div>
        @empty
          <div class="empty-state">
            <i class="fa-regular fa-folder-open"></i>
            <p>لا يوجد طلاب مسجلين في هذه المادة حالياً</p>
          </div>
        @endforelse
      </div>
    @empty
      <div class="empty-state" style="padding: 60px 0;">
        <i class="fa-solid fa-graduation-cap"></i>
        <p style="font-size: 1.15rem;">لا توجد مساقات دراسية مسجلة باسمك حالياً</p>
      </div>
    @endforelse
  </div>

  <a href="{{ route('teacher.dashboard') }}" class="back-link">
    <i class="fa-solid fa-arrow-right"></i> العودة للوحة التحكم
  </a>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'هل تريد إزالة الطالب من هذا المساق؟',
                text: "سيتم إلغاء تسجيل الطالب من هذه المادة فقط!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، إزالة',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) { form.submit(); }
            });
        });
    });
});
</script>
</body>
</html>