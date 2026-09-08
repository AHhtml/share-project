<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تعديل الاختبار — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f8; margin: 0; padding: 0; color: #333; }
  .form-wrapper { min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 40px 20px; box-sizing: border-box; }
  .form-card { background: #ffffff; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 32px; border: 1px solid #e1e4e8; }
  .form-header { margin-bottom: 24px; border-bottom: 2px solid #f0f2f5; padding-bottom: 16px; }
  .form-header h1 { margin: 0 0 8px 0; font-size: 1.5rem; color: #1f2937; }
  .form-header p { margin: 0; color: #6b7280; font-size: 0.9rem; }
  .field { margin-bottom: 20px; }
  .field label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 0.95rem; }
  .field input, .field textarea { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; box-sizing: border-box; font-family: inherit; font-size: 0.95rem; background-color: #fafafa; }
  .field input:focus, .field textarea:focus { border-color: #d97706; background-color: #fff; outline: none; }
  .form-actions { display: flex; gap: 12px; margin-top: 28px; }
  .btn-submit { background-color: #d97706; color: #fff; border: none; padding: 11px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.95rem; }
  .btn-cancel { background-color: #f3f4f6; color: #4b5563; text-decoration: none; padding: 11px 20px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; display: inline-block; }
</style>
</head>
<body>

<div class="form-wrapper">
  <div class="form-card">
    <div class="form-header">
      <h1>✏️ تعديل الاختبار</h1>
      <p>حدث بيانات الاختبار ثم اضغط حفظ التعديلات.</p>
    </div>

    <form action="{{ route('teacher.assignments.update', $assignment->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="field">
        <label>عنوان الاختبار</label>
        <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required>
      </div>

      <div class="field">
        <label>تاريخ وموعد الاختبار / التسليم</label>
        <input type="date" name="due_date" value="{{ old('due_date', $assignment->due_date) }}" required>
      </div>

      <div class="field">
        <label>مدة الاختبار</label>
        <input type="text" name="duration" value="{{ old('duration', $assignment->duration) }}">
      </div>

      <div class="field">
        <label>الدرجة الكلية</label>
        <input type="number" name="total_marks" min="1" value="{{ old('total_marks', $assignment->total_marks) }}" required>
      </div>

      <div class="field">
        <label>التعليمات / التفاصيل</label>
        <textarea name="description" rows="4">{{ old('description', $assignment->description) }}</textarea>
      </div>

      <!-- حقل رفع الملف المضاف حديثاً -->
      <div class="field">
        <label>ملف الاختبار المرفق (اختياري)</label>
        <input type="file" name="file">
        @if($assignment->file_path)
          <small style="color: #6b7280; display: block; margin-top: 6px;">الملف الحالي مرفق مسبقاً. رفع ملف جديد سيقوم باستبداله.</small>
        @endif
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">حفظ التعديلات</button>
        <a href="{{ route('teacher.assignments.index') }}" class="btn-cancel">إلغاء</a>
      </div>
    </form>
  </div>
</div>

</body>
</html>