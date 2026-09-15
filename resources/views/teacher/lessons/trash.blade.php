<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أرشيف المحاضرات المحذوفة - منصة منارة</title>
    <!-- خط Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- مكتبة SweetAlert2 للتنبيهات الاحترافية -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Cairo', Tahoma, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: #ffffff;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 5px 0;
        }
        .header p {
            color: #64748b;
            font-size: 13px;
            margin: 0;
        }
        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .back-btn {
            background: #f1f5f9;
            color: #475569;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }
        .back-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .btn-empty-trash {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            font-family: 'Cairo', sans-serif;
            transition: all 0.2s;
        }
        .btn-empty-trash:hover {
            background: #dc2626;
            color: #fff;
        }
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        .table-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }
        thead tr {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-size: 13px;
        }
        th, td {
            padding: 16px 20px;
        }
        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #334155;
            transition: background 0.15s;
        }
        tbody tr:hover {
            background: #fcfcfc;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-restore {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 7px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Cairo', sans-serif;
            transition: all 0.2s;
        }
        .btn-restore:hover {
            background: #059669;
            color: #fff;
        }
        .btn-delete {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 7px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Cairo', sans-serif;
            transition: all 0.2s;
        }
        .btn-delete:hover {
            background: #dc2626;
            color: #fff;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
            font-weight: 600;
        }
        .empty-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>

<div class="container">
    
    <!-- رأس الصفحة -->
    <div class="header">
        <div>
            <h1>🗑️ أرشيف المحاضرات المحذوفة</h1>
            <p>يمكنك استعادة المحاضرات المحذوفة أو حذفها نهائياً من النظام.</p>
        </div>
        <div class="header-actions">
            @if($trashedLessons->isNotEmpty())
                <form id="empty-trash-form" action="{{ route('teacher.lessons.emptyTrash') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmEmptyTrash()" class="btn-empty-trash">
                        🗑️ حذف كل البيانات
                    </button>
                </form>
            @endif

            <a href="{{ route('teacher.lessons.index') }}" class="back-btn">
                ← العودة للمحاضرات النشطة
            </a>
        </div>
    </div>

    <!-- رسائل النجاح -->
    @if(session('success'))
        <div class="alert-success">
            ✨ {{ session('success') }}
        </div>
    @endif

    <!-- جدول المحاضرات المحذوفة -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>عنوان المحاضرة</th>
                    <th>المادة / المساق</th>
                    <th>تاريخ الحذف</th>
                    <th style="text-align: left; padding-left: 30px;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trashedLessons as $lesson)
                    <tr>
                        <td style="font-weight: 700; color: #1e293b;">{{ $lesson->title }}</td>
                        <td>
                            <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #475569;">
                                {{ $lesson->subject->name ?? 'غير محددة' }}
                            </span>
                        </td>
                        <td style="color: #64748b; font-size: 13px;">{{ $lesson->deleted_at->diffForHumans() }}</td>
                        <td>
                            <div class="actions">
                                <!-- زر الاستعادة -->
                                <form action="{{ route('teacher.lessons.restore', $lesson->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-restore">
                                        ♻️ استعادة
                                    </button>
                                </form>

                                <!-- زر الحذف النهائي -->
                                <form id="delete-form-{{ $lesson->id }}" action="{{ route('teacher.lessons.forceDelete', $lesson->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmForceDelete({{ $lesson->id }})" class="btn-delete">
                                        ❌ حذف نهائي
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">
                            <div class="empty-icon">📭</div>
                            سلة المهملات فارغة، لا توجد محاضرات محذوفة حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
    // نافذة تأكيد الحذف النهائي لمحاضرة واحدة
    function confirmForceDelete(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن يمكنك استعادة هذه المحاضرة نهائياً بعد الآن!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، احذف نهائياً',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // نافذة تأكيد تفريغ سلة المهملات بالكامل
    function confirmEmptyTrash() {
        Swal.fire({
            title: '⚠️ تحذير شديد!',
            text: "سيتم حذف جميع المحاضرات الموجودة في سلة المهملات نهائياً من النظام!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، تفريغ السلة',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('empty-trash-form').submit();
            }
        });
    }
</script>

</body>
</html>