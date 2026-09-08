<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تعديل البيانات الشخصية — منارة</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
  body {
    background-color: #0d231b; /* لون الخلفية الأخضر الداكن المطابق للصورة */
    font-family: 'Tajawal', sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
  }
  .centered-container {
    width: 100%;
    max-width: 650px;
    padding: 20px;
    box-sizing: border-box;
  }
  .card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    padding: 30px;
    border: 1px solid #13382d;
  }
  .main-head {
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .main-head h1 {
    font-size: 1.5rem;
    color: #0f172a;
    margin: 0 0 5px 0;
  }
  .main-head p {
    color: #64748b;
    margin: 0;
    font-size: 0.95rem;
  }
</style>
</head>
<body>

<div class="centered-container">

    @if(session('success'))
      <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
        <ul style="margin: 0; padding-right: 20px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card">
      <div class="main-head">
        <div>
          <h1>تعديل البيانات الشخصية</h1>
          <p>يمكنك تعديل اسمك، البريد الإلكتروني، أو تغيير كلمة المرور الخاصة بك.</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline btn-sm" style="text-decoration: none; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #333; font-size: 0.85rem;">العودة للرئيسية</a>
      </div>

      <form action="{{ route('student.profile.update') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="field" style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem;">الاسم الكامل</label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div class="field" style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem;">البريد الإلكتروني</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 15px;">اترك حقل كلمة المرور فارغاً إذا كنت لا تريد تغييرها.</p>

        <div class="field" style="margin-bottom: 15px;">
          <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem;">كلمة المرور الجديدة</label>
          <input type="password" name="password" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div class="field" style="margin-bottom: 20px;">
          <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem;">تأكيد كلمة المرور الجديدة</label>
          <input type="password" name="password_confirmation" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div style="display: flex; gap: 10px;">
          <button type="submit" class="btn btn-primary" style="background: #13382d; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">حفظ التعديلات</button>
          <a href="{{ route('student.dashboard') }}" class="btn btn-ghost" style="padding: 10px 20px; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; color: #333; display: inline-flex; align-items: center;">إلغاء</a>
        </div>
      </form>
    </div>

</div>

<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/layout.js') }}"></script>
</body>
</html>