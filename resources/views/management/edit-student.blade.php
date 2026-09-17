<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات الطالب — منارة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- مكتبة SweetAlert2 للنوافذ الاحترافية -->
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
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border-color);
            padding: 30px;
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }

        .form-header h1 {
            font-size: 1.3rem;
            margin: 0;
            color: var(--text-main);
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        .field input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: 'Tajawal', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
            background-color: #f8fafc;
        }

        .field input:focus {
            border-color: var(--primary-color);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-family: 'Tajawal', sans-serif;
            font-weight: 500;
            font-size: 0.95rem;
            flex: 1;
            transition: opacity 0.2s;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        .btn-cancel {
            background-color: #f1f5f9;
            color: var(--text-main);
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-weight: 500;
            font-size: 0.95rem;
            border: 1px solid var(--border-color);
            display: inline-block;
            box-sizing: border-box;
            line-height: normal;
        }

        .btn-cancel:hover {
            background-color: #e2e8f0;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .btn-icon {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 6px 10px;
            cursor: pointer;
            font-family: 'Tajawal', sans-serif;
            font-size: 0.85rem;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: var(--text-main);
        }

        .btn-icon:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <div class="form-header">
                <!-- زر العودة المباشر لصفحة الطلاب -->
                <a href="{{ route('management.students.index') }}" class="btn-icon">← عودة</a>
                <h1>تعديل بيانات الطالب: {{ $student->name }} ✏️</h1>
            </div>

            @if(session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-danger">
                    <ul style="margin: 0; padding-right: 15px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- نموذج تعديل البيانات -->
            <form action="{{ route('management.students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="field">
                    <label>اسم الطالب الكامل</label>
                    <input type="text" name="name" value="{{ old('name', $student->name) }}" required>
                </div>

                <div class="field">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email', $student->email) }}" required>
                </div>

                <div class="field">
                    <label>رقم الجوال</label>
                    <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" placeholder="مثال: 0591234567" dir="ltr" style="text-align: right;">
                </div>

                <div class="field">
                    <label>كلمة المرور الجديدة (اختياري)</label>
                    <input type="password" name="password" placeholder="اتركها فارغة إذا لم تقم برغبة تغييرها">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">حفظ كافة التعديلات</button>
                    <!-- زر الإلغاء المباشر لصفحة الطلاب -->
                    <a href="{{ route('management.students.index') }}" class="btn-cancel">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>