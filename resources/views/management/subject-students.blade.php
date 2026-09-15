<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلاب مساق: {{ $subject->name }} — منارة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- مكتبة SweetAlert2 للنوافذ المنبثقة والتأكيد الاحترافية -->
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
            max-width: 1050px;
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
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title-wrapper p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .badge-count {
            background-color: var(--primary-light);
            color: var(--primary-color);
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* أجراءات الرأس (زر الإضافة والعودة) */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* زر إضافة طالب للمساق */
        .btn-add-student {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--primary-color);
            color: #ffffff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-add-student:hover {
            opacity: 0.9;
            color: #ffffff;
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

        /* البطاقة والجدول */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        .data-table th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 16px 20px;
            color: var(--text-main);
            font-size: 0.95rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .data-table tbody tr {
            transition: background-color 0.15s ease;
        }

        .data-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* خلية الطالب مع أيقونة */
        .student-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-avatar {
            width: 38px;
            height: 38px;
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
        }

        /* أزرار الإجراءات */
        .action-btns {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: opacity 0.2s;
            border: none;
            cursor: pointer;
            font-family: 'Tajawal', sans-serif;
        }

        .btn-action:hover {
            opacity: 0.85;
        }

        .btn-show { background-color: #e0f2fe; color: #0369a1; }
        .btn-edit { background-color: #fef3c7; color: #b45309; }
        .btn-delete { background-color: #fee2e2; color: #991b1b; }

        /* الحالة الفارغة */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state .glyph {
            font-size: 3rem;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1rem;
            margin: 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- ترويسة الصفحة -->
        <div class="page-header">
            <div class="page-title-wrapper">
                <h1>
                    <span>📚 مساق: {{ $subject->name }}</span>
                    <span class="badge-count">{{ $students->count() }} طالب</span>
                </h1>
                <p>قائمة الطلاب المسجلين رسمياً في هذا المساق الأكاديمي.</p>
            </div>
            
            <!-- أزرار الترويسة (إضافة طالب + العودة) -->
            <div class="header-actions">
                <a href="{{ route('management.subjects.students.create', $subject->id) }}" class="btn-add-student">
                    ➕ إضافة طالب للمساق
                </a>
                <a href="{{ route('management.dashboard') }}" class="btn-back">
                    <span>←</span> عودة
                </a>
            </div>
        </div>

        <!-- محتوى الجدول -->
        <div class="card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>اسم الطالب</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ التسجيل</th>
                        <th style="text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <div class="student-info">
                                    <div class="student-avatar">
                                        {{ mb_substr($student->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;">{{ $student->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="color: var(--text-muted);">{{ $student->email }}</td>
                            <td style="color: var(--text-muted); font-size: 0.9rem;">
                                {{ $student->created_at ? $student->created_at->format('Y-m-d') : 'غير متوفر' }}
                            </td>
                            <td style="text-align: center;">
                                <div class="action-btns">
                                    <!-- زر العرض (نافذة منبثقة SweetAlert2) -->
                                    <button type="button" class="btn-action btn-show" onclick="showStudentDetails('{{ $student->name }}', '{{ $student->email }}', '{{ $student->created_at ? $student->created_at->format('Y-m-d') : 'غير متوفر' }}')">
                                        👁️ عرض
                                    </button>

                                    <!-- زر التعديل -->
                                    <a href="{{ route('management.students.edit', $student->id) }}" class="btn-action btn-edit" title="تعديل بيانات الطالب">
                                        ✏️ تعديل
                                    </a>

                                    <!-- زر الحذف من المساق -->
                                    <button type="button" class="btn-action btn-delete" onclick="confirmRemoveStudent({{ $subject->id }}, {{ $student->id }})" title="حذف من المساق">
                                        🗑️ حذف
                                    </button>

                                    <!-- فورم مخفي لتنفيذ عملية الحذف من المساق -->
                                    <form id="remove-form-{{ $student->id }}" action="{{ route('management.subjects.students.destroy', [$subject->id, $student->id]) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="glyph">🎓</div>
                                    <p>لا يوجد طلاب مسجلين في هذا المساق حالياً.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // دالة عرض بيانات الطالب في نافذة منبثقة تفصيلية
        function showStudentDetails(name, email, createdAt) {
            Swal.fire({
                title: 'معلومات الطالب',
                html: `
                    <div style="text-align: right; direction: rtl; line-height: 1.8; font-family: 'Tajawal', sans-serif;">
                        <p><strong>الاسم الكامل:</strong> ${name}</p>
                        <p><strong>البريد الإلكتروني:</strong> ${email}</p>
                        <p><strong>نوع الحساب:</strong> طالب (Student)</p>
                        <p><strong>تاريخ التسجيل:</strong> ${createdAt}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'إغلاق',
                confirmButtonColor: '#2563eb'
            });
        }

        // دالة نافذة الحذف الاحترافية لإزالة الطالب من المساق فقط
        function confirmRemoveStudent(subjectId, studentId) {
            Swal.fire({
                title: 'هل أنت متأكد من إزالة الطالب من المساق؟',
                text: "لن يتم حذف حساب الطالب بالكامل، بل سيتم إزالته من هذا المساق فقط!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، قم بالإزالة',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('remove-form-' + studentId).submit();
                }
            });
        }
    </script>
</body>
</html>