<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Student\ProfileController;

// الصفحة الرئيسية
Route::get('/', [AuthController::class, 'showLogin']);

// مسار مؤقت لعرض السجلات مباشرة من المتصفح (للتحقق من الـ Logging)
Route::get('/check-logs', function () {
    $logPath = storage_path('logs/laravel.log');
    
    if (!File::exists($logPath)) {
        return "ملف السجلات غير موجود بعد.";
    }

    $lines = file($logPath);
    $lastLines = array_slice($lines, -15);

    return '<pre style="background:#111; color:#0f0; padding:20px; direction:ltr; font-size:14px;">' . implode("", $lastLines) . '</pre>';
});

// مسارات الضيوف (غير المسجلين)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// مسارات المستخدمين المسجلين (تتطلب تسجيل دخول)
Route::middleware('auth')->group(function () {
    
    // تم تعديل راوت الخروج هنا ليقبل GET و POST معاً لمنع ظهور خطأ 419 نهائياً
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    // مسار عام لحذف الإشعارات لأي مستخدم مسجل
    Route::delete('/notifications/{id}', [StudentController::class, 'destroyNotification'])->name('notifications.destroy');

    // مسار مخصص لحذف الإشعارات للـ Teacher لتجنب خطأ RouteNotFoundException
    Route::delete('/teacher/notifications/{id}', [StudentController::class, 'destroyNotification'])->name('teacher.notifications.destroy');

    // 1. لوحة تحكم الأدمن
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    });

    // 2. لوحة تحكم الإدارة والأدمن
    Route::middleware('role:management,admin')->prefix('management')->name('management.')->group(function () {
        Route::get('/dashboard', [ManagementController::class, 'dashboard'])->name('dashboard');
        
        // إدارة المستخدمين
        Route::get('/users', [ManagementController::class, 'usersIndex'])->name('users.index');
        Route::post('/users', [ManagementController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [ManagementController::class, 'update'])->name('users.update');
        
        // مسار الحذف
        Route::delete('/users/{user}', [ManagementController::class, 'destroy'])->name('users.destroy');

        // مسارات تصدير واستيراد المستخدمين (Data Export & Import)
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import');

        // مسارات عرض وإدارة جميع طلاب المركز
        Route::get('/students', [ManagementController::class, 'allStudentsIndex'])->name('students.index');

        // مسارات تعديل الطالب
        Route::get('/students/{id}/edit', [ManagementController::class, 'editStudent'])->name('students.edit');
        Route::put('/students/{id}', [ManagementController::class, 'updateStudent'])->name('students.update');

        // مسار عرض محاضرات واختبارات المعلم
        Route::get('/teachers/{id}/content', [ManagementController::class, 'teacherContent'])->name('teachers.content');

        // مسار عرض الطلاب المسجلين في مادة معينة
        Route::get('/subjects/{id}/students', [ManagementController::class, 'subjectStudents'])->name('subjects.students');
        
        // مسارات إضافة طالب إلى مساق معين
        Route::get('/subjects/{id}/students/create', [ManagementController::class, 'createSubjectStudent'])->name('subjects.students.create');
        Route::post('/subjects/{id}/students', [ManagementController::class, 'storeSubjectStudent'])->name('subjects.students.store');

        // مسار إزالة الطالب من المساق
        Route::delete('/subjects/{subject}/students/{student}', [ManagementController::class, 'removeStudentFromSubject'])->name('subjects.students.destroy');
        
        // مسارات الملف الشخصي للإدارة
        Route::get('/profile', [ManagementController::class, 'profile'])->name('profile');
        Route::put('/profile', [ManagementController::class, 'updateProfile'])->name('profile.update');
    });

    // 3. لوحة تحكم المعلم
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');

        // مسارات الملف الشخصي للمعلم
        Route::get('/profile', [TeacherController::class, 'profile'])->name('profile');
        Route::put('/profile', [TeacherController::class, 'updateProfile'])->name('profile.update');

        // إدارة الإعلانات للمعلم
        Route::get('/announcements', [TeacherController::class, 'announcementsIndex'])->name('announcements.index');
        Route::post('/announcements', [TeacherController::class, 'announcementsStore'])->name('announcements.store');
        Route::delete('/announcements/{id}', [TeacherController::class, 'destroyAnnouncement'])->name('announcements.destroy');

        // إدارة الطلاب
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        
        // مسار تصدير قائمة طلاب المعلم إلى إكسل / CSV
        Route::get('/students/export', [StudentController::class, 'exportStudents'])->name('students.export');

        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

        // إلغاء تسجيل طالب من المادة
        Route::delete('/subjects/{subject}/students/{student}', [StudentController::class, 'removeStudent'])->name('subjects.students.remove');
        
        // المحاضرات (الأرشيف، الاستعادة، والحذف النهائي وتفريغ السلة)
        Route::get('/lessons/trash', [LessonController::class, 'trash'])->name('lessons.trash');
        Route::patch('/lessons/{id}/restore', [LessonController::class, 'restore'])->name('lessons.restore');
        Route::delete('/lessons/{id}/force-delete', [LessonController::class, 'forceDelete'])->name('lessons.forceDelete');
        Route::delete('/lessons/empty-trash', [LessonController::class, 'emptyTrash'])->name('lessons.emptyTrash');

        Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
        Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
        Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
        Route::get('/lessons/{id}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/lessons/{id}', [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])->name('lessons.destroy');

        // الواجبات والاختبارات
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{id}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/assignments/{id}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

        // مسارات المعلم لاستعراض وتحميل تسليمات الطلاب
        Route::get('/assignments/{id}/submissions', [AssignmentController::class, 'viewSubmissions'])->name('assignments.submissions');
        Route::get('/submissions/{id}/download', [AssignmentController::class, 'downloadSubmission'])->name('submissions.download');
    });

    // 4. لوحة تحكم الطالب
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        
        // عرض تفاصيل مادة معينة للطالب
        Route::get('/subjects/{id}', [StudentController::class, 'showSubject'])->name('subject.show');

        // تعديل البيانات الشخصية للطالب
        Route::get('/profile', [StudentController::class, 'editProfile'])->name('profile.edit');
        Route::patch('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');

        // واجهات Blade المستقلة للطالب
        Route::get('/lessons', [StudentController::class, 'lessons'])->name('lessons');
        Route::get('/assignments', [StudentController::class, 'assignments'])->name('assignments');
        
        // مسار تحميل ملف الواجب/الاختبار
        Route::get('/assignments/{id}/download', [AssignmentController::class, 'download'])->name('assignments.download');

        // مسار إرسال وتسليم حل الواجب/الاختبار من الطالب
        Route::post('/assignments/{id}/submit', [StudentController::class, 'submitAssignment'])->name('assignments.submit');

        // مسار إعلانات الطالب
        Route::get('/announcements', [StudentController::class, 'announcements'])->name('announcements');

        // مسار حذف الإشعارات للطالب
        Route::delete('/notifications/{id}', [StudentController::class, 'destroyNotification'])->name('notifications.destroy');
    });
});