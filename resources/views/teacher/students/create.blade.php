<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إضافة طالب جديد — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; margin: 0; }
  .page-container { max-width: 700px; margin: 40px auto; padding: 0 20px; }
  .form-card { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
  .form-group { margin-bottom: 20px; }
  .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
  .form-control { width: 100%; padding: 10px 14px; border: 1px solid #ced4da; border-radius: 8px; font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
  .btn-submit { background-color: #d97706; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; font-family: 'Tajawal', sans-serif; }
  .btn-submit:hover { background-color: #b45309; }
  .back-link { display: inline-flex; align-items: center; gap: 6px; margin-top: 20px; color: #495057; text-decoration: none; font-weight: 500; }
</style>
</head>
<body>

<div class="page-container">
  <div class="form-card">
    <h2 style="margin-top: 0; color: #1a1a1a;">➕ إضافة طالب جديد</h2>

    @if ($errors->any())
      <div style="background-color: #f8d7da; color: #842029; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-right: 20px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('teacher.students.store') }}" method="POST">
      @csrf
      <div class="form-group">
        <label>اسم الطالب</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
      </div>

      <div class="form-group">
        <label>البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
      </div>

      <div class="form-group">
        <label>كلمة المرور المؤقتة</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn-submit">حفظ الطالب</button>
    </form>
  </div>

  <a href="{{ route('teacher.students.index') }}" class="back-link">→ العودة لقائمة الطلاب</a>
</div>

</body>
</html>