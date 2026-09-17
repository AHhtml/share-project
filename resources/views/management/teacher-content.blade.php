<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محتوى المعلم: أ. {{ $teacher->name }} — منارة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .content-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
        }
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f1f5f9;
        }
        .section-header h2 {
            font-size: 1.25rem;
            color: #1e293b;
            margin: 0;
            font-weight: 700;
        }
        .item-row {
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }
        .item-row:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #f1f5f9;
            color: #334155;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
    </style>
</head>
<body style="background-color: #f8fafc; font-family: 'Tajawal', sans-serif; padding: 40px 20px; margin: 0;">
    <div class="container" style="max-width: 900px; margin: 0 auto;">
        
        <!-- رأس الصفحة -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: #ffffff; padding: 20px 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <div>
                <span style="font-size: 0.85rem; color: #d97706; font-weight: bold; display: block; margin-bottom: 4px;">ملف المعلم</span>
                <h1 style="font-size: 1.5rem; color: #0f172a; margin: 0;">أ. {{ $teacher->name }}</h1>
            </div>
            <a href="{{ route('management.users.index') }}" class="btn-back">
                <span>←</span> عودة لقائمة المعلمين
            </a>
        </div>

        <!-- المحاضرات -->
        <div class="content-card">
            <div class="section-header">
                <span style="font-size: 1.4rem;">🎥</span>
                <h2>المحاضرات المنشورة</h2>
            </div>
            
            @forelse($lessons as $lesson)
                <div class="item-row">
                    <strong style="font-size: 1.05rem; color: #1e293b; display: block; margin-bottom: 4px;">{{ $lesson->title }}</strong>
                    <p style="margin: 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;">{{ $lesson->description ?? 'لا يوجد وصف متاح لهذه المحاضرة.' }}</p>
                </div>
            @empty
                <div style="text-align: center; padding: 30px; color: #94a3b8;">
                    <p style="margin: 0; font-size: 0.95rem;">لم ينشر هذا المعلم أي محاضرة بعد.</p>
                </div>
            @endforelse
        </div>

        <!-- الاختبارات -->
        <div class="content-card">
            <div class="section-header">
                <span style="font-size: 1.4rem;">📝</span>
                <h2>الاختبارات المجدولة</h2>
            </div>
            
            @forelse($assignments as $assignment)
                <div class="item-row" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong style="font-size: 1.05rem; color: #1e293b; display: block; margin-bottom: 4px;">{{ $assignment->title }}</strong>
                        <p style="margin: 0; color: #64748b; font-size: 0.9rem;">العلامة الكاملة: <span style="font-weight: bold; color: #0f172a;">{{ $assignment->marks ?? 10 }}</span> درجة</p>
                    </div>
                    <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">اختبار</span>
                </div>
            @empty
                <div style="text-align: center; padding: 30px; color: #94a3b8;">
                    <p style="margin: 0; font-size: 0.95rem;">لا توجد اختبارات مجدولة لهذا المعلم بعد.</p>
                </div>
            @endforelse
        </div>

    </div>
</body>
</html>