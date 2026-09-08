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
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
        Route::delete('/users/{id}', [ManagementController::class, 'destroyUser'])->name('users.destroy');

        // مسارات تعديل الطالب
        Route::get('/students/{id}/edit', [ManagementController::class, 'editStudent'])->name('students.edit');
        Route::put('/students/{id}', [ManagementController::class, 'updateStudent'])->name('students.update');

        // مسار عرض محاضرات واختبارات المعلم
        Route::get('/teachers/{id}/content', [ManagementController::class, 'teacherContent'])->name('teachers.content');

        // مسار عرض الطلاب المسجلين في مادة معينة
        Route::get('/subjects/{id}/students', [ManagementController::class, 'subjectStudents'])->name('subjects.students');
        
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

        // إدارة الطلاب (عرض، إضافة، وحذف الطلاب)
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

        // إلغاء تسجيل طالب من المادة
        Route::delete('/subjects/{subject}/students/{student}', [TeacherController::class, 'removeStudent'])->name('subjects.students.remove');

        // المحاضرات
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
    });

    // 4. لوحة تحكم الطالب
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        
        // تعديل البيانات الشخصية للطالب
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // واجهات Blade المستقلة للطالب
        Route::get('/lessons', [StudentController::class, 'lessonsIndex'])->name('lessons.index');
        Route::get('/assignments', [StudentController::class, 'assignmentsIndex'])->name('assignments.index');
        
        // مسار إعلانات الطالب المضاف حديثاً
        Route::get('/announcements', [StudentController::class, 'announcementsIndex'])->name('announcements.index');

        // جلب البيانات (AJAX)
        Route::get('/api/lessons', [StudentController::class, 'getLessons'])->name('lessons.json');
        Route::get('/api/assignments', [StudentController::class, 'getAssignments'])->name('assignments.json');
    });
});