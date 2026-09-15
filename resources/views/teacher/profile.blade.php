<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي — منارة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #065f46;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --radius: 12px;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            background: var(--bg-card);
            padding: 20px 28px;
            border-radius: var(--radius);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
        }

        .page-header h1 {
            font-size: 1.4rem;
            margin: 0;
            color: var(--text-main);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #f1f5f9;
            color: var(--text-main);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            border: 1px solid var(--border-color);
        }

        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: 'Tajawal', sans-serif;
            font-size: 0.95rem;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
            background-color: #fff;
        }

        .form-control:focus {
            border-color: var(--primary-color);
        }

        /* تنسيق خاص لحقل رفع الملفات لتناسب التصميم */
        input[type="file"].form-control {
            padding: 9px 16px;
            cursor: pointer;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-family: 'Tajawal', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1>⚙️ تعديل الملف الشخصي للمعلم</h1>
            <a href="{{ route('teacher.dashboard') }}" class="btn-back">← عودة لوحة التحكم</a>
        </div>

        <div class="card">
            @if(session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- تم تعديل مسار الـ action هنا ليوجه إلى مسار التحديث الصحيح -->
            <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>الاسم الكامل</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <!-- حقل رفع الصورة الشخصية -->
                <div class="form-group">
                    <label>الصورة الشخصية</label>
                    <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                </div>

                <div class="form-group">
                    <label>كلمة المرور الجديدة (اتركها فارغة إذا لم ترغب بتغييرها)</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                </div>

                <button type="submit" class="btn-submit">حفظ التغييرات</button>
            </form>
        </div>
    </div>

</body>
</html>