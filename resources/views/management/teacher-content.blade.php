<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محتوى المعلم: {{ $teacher->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body style="padding: 30px; background-color: #f8f9fa;">
    <div class="container" style="max-width: 900px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>محتوى المعلم: أ. {{ $teacher->name }}</h1>
            <a href="{{ url()->previous() }}" class="btn btn-ghost" style="text-decoration: none;">← عودة</a>
        </div>

        <!-- المحاضرات -->
        <div class="card section-card" style="margin-bottom: 25px; padding: 20px; background: #fff; border-radius: 8px;">
            <h2>🎥 المحاضرات المنشورة</h2>
            @forelse($lessons as $lesson)
                <div style="padding: 10px 0; border-bottom: 1px solid #eee;">
                    <strong>{{ $lesson->title }}</strong>
                    <p style="margin: 4px 0; color: #666; font-size: 0.85rem;">{{ $lesson->description ?? 'لا يوجد وصف' }}</p>
                </div>
            @empty
                <p style="color: #888; margin-top: 10px;">لم ينشر هذا المعلم أي محاضرة بعد.</p>
            @endforelse
        </div>

        <!-- الاختبارات -->
        <div class="card section-card" style="padding: 20px; background: #fff; border-radius: 8px;">
            <h2>📝 الاختبارات المجدولة</h2>
            @forelse($assignments as $assignment)
                <div style="padding: 10px 0; border-bottom: 1px solid #eee;">
                    <strong>{{ $assignment->title }}</strong>
                    <p style="margin: 4px 0; color: #666; font-size: 0.85rem;">العلامة الكاملة: {{ $assignment->marks ?? 10 }}</p>
                </div>
            @empty
                <p style="color: #888; margin-top: 10px;">لا توجد اختبارات مجدولة لهذا المعلم بعد.</p>
            @endforelse
        </div>
    </div>
</body>
</html>