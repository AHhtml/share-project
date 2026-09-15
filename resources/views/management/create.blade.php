<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة طالب جديد للمساق — منارة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- مكتبة SweetAlert2 للتنبيهات -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-light: #eff6ff;
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

        /* رأس الصفحة */
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

        .page-title-wrapper h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: var(--text-main);
        }

        .page-title-wrapper p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* زر العودة */
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
            transition: all 0.2s ease;
            border: 1px solid var(--border-color);
        }

        .btn-back:hover {
            background: #e2e8f0;
            color: var(--primary-color);
        }

        /* صندوق النموذج */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border-color);
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-family: 'Tajawal', sans-serif;
            font-size: 0.95rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: #f8fafc;
            color: var(--text-main);
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
        }

        .btn-submit {
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: 'Tajawal', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- ترويسة الصفحة -->
        <div class="page-header">
            <div class="page-title-wrapper">
                <h1>➕ تسجيل طالب جديد لمساق: {{ $subject->name }}</h1>
                <p>أدخل بيانات الطالب الجديد لإنشاء حسابه وإضافته مباشرة إلى هذا المساق الأكاديمي.</p>
            </div>
            <a href="{{ route('management.subjects.students', $subject->id) }}" class="btn-back">
                <span>←</span> عودة لقائمة الطلاب
            </a>
        </div>

        <!-- فورم الإضافة -->
        <div class="card">
            @if ($errors->any())
                <div class="alert-danger">
                    <ul style="margin: 0; padding-right: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('management.subjects.students.store', $subject->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">اسم الطالب الكامل</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="أدخل اسم الطالب...">
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="student@domain.com">
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="أدخل كلمة المرور (6 أحرف على الأقل)">
                </div>

                <div class="form-actions">
                    <a href="{{ route('management.subjects.students', $subject->id) }}" class="btn-back" style="padding: 12px 20px; text-decoration: none;">إلغاء</a>
                    <button type="submit" class="btn-submit">حفظ وتسجيل الطالب</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                title: 'تم بنجاح!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#2563eb'
            });
        </script>
    @endif

</body>
</html>