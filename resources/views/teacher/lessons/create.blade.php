<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إضافة محاضرة جديدة — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<style>
  body {
    font-family: 'Tajawal', sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 0;
    color: #333;
  }

  .form-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
    box-sizing: border-box;
  }

  .form-card {
    background: #ffffff;
    width: 100%;
    max-width: 600px;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    padding: 32px;
    border: 1px solid #e1e4e8;
  }

  .form-header {
    margin-bottom: 24px;
    border-bottom: 2px solid #f0f2f5;
    padding-bottom: 16px;
  }

  .form-header h1 {
    margin: 0 0 8px 0;
    font-size: 1.5rem;
    color: #1f2937;
  }

  .form-header p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
  }

  .field {
    margin-bottom: 20px;
  }

  .field label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 0.95rem;
  }

  .field input, 
  .field textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-sizing: border-box;
    font-family: inherit;
    font-size: 0.95rem;
    background-color: #fafafa;
    transition: all 0.2s ease;
  }

  .field input:focus, 
  .field textarea:focus {
    border-color: #d97706;
    background-color: #fff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
  }

  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 28px;
  }

  .btn-submit {
    background-color: #d97706;
    color: #fff;
    border: none;
    padding: 11px 24px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background-color 0.2s;
  }

  .btn-submit:hover {
    background-color: #b45309;
  }

  .btn-cancel {
    background-color: #f3f4f6;
    color: #4b5563;
    text-decoration: none;
    padding: 11px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-block;
    text-align: center;
    transition: background-color 0.2s;
  }

  .btn-cancel:hover {
    background-color: #e5e7eb;
    color: #1f2937;
  }

  .alert-error {
    padding: 12px 16px;
    background-color: #fde8e8;
    color: #9b1c1c;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
  }
</style>
</head>
<body>

<div class="form-wrapper">
  <div class="form-card">
    
    <div class="form-header">
      <h1>➕ إضافة محاضرة جديدة</h1>
      <p>أدخل بيانات المحاضرة لنشرها لطلابك مباشرة.</p>
    </div>

    @if(session('error'))
      <div class="alert-error">
        {{ session('error') }}
      </div>
    @endif

    <form action="{{ route('teacher.lessons.store') }}" method="POST">
      @csrf
      
      <div class="field">
        <label>عنوان المحاضرة</label>
        <input type="text" name="title" placeholder="أدخل عنوان المحاضرة" required>
      </div>

      <div class="field">
        <label>التاريخ</label>
        <input type="date" name="date" required>
      </div>

      <div class="field">
        <label>المدة المتوقعة</label>
        <input type="text" name="duration" placeholder="مثال: 90 دقيقة">
      </div>

      <div class="field">
        {{-- <label>الوصف / الملاحظات</label> --}}
        <label> رابط المحاضرة </label>
        <textarea name="description" rows="4" placeholder="اكتب وصفاً قصيراً للمحاضرة أو الموضوعات التي سيتم تغطيتها..."></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">نشر المحاضرة</button>
        <a href="{{ route('teacher.lessons.index') }}" class="btn-cancel">إلغاء</a>
      </div>
    </form>

  </div>
</div>

</body>
</html>