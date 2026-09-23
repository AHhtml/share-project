<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Subject;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    // لوحة تحكم الطالب
    public function dashboard()
    {
        $user = Auth::user();

        $enrolledSubjects = $user->enrolledSubjects()->with(['lessons', 'assignments'])->get();

        $lessonsCount = Lesson::whereIn('subject_id', $enrolledSubjects->pluck('id'))->count();
        $assignmentsCount = Assignment::whereIn('subject_id', $enrolledSubjects->pluck('id'))->count();
        $completedExamsCount = Submission::where('user_id', $user->id)->count();

        $latestLessons = Lesson::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->with(['subject', 'teacher'])
            ->latest()
            ->take(5)
            ->get();

        $subjectIds = $user->subjects()->pluck('subjects.id');
        $announcements = Announcement::whereIn('subject_id', $subjectIds)
            ->orWhereNull('subject_id')
            ->latest()
            ->get();

        // جلب الإشعارات والعدد الكلي لها بدون استخدام read_at
        $notifications = Notification::where('user_id', $user->id)->latest()->take(10)->get();
        $unreadCount = Notification::where('user_id', $user->id)->count();

        return view('student.dashboard', compact(
            'enrolledSubjects',
            'lessonsCount',
            'assignmentsCount',
            'completedExamsCount',
            'latestLessons',
            'announcements',
            'notifications',
            'unreadCount'
        ));
    }

    // عرض تفاصيل مادة محددة للطالب
    public function showSubject($id)
    {
        $user = Auth::user();
        
        // التحقق أن الطالب مسجل في هذه المادة فعلياً
        $subject = $user->enrolledSubjects()->with(['lessons', 'assignments'])->findOrFail($id);

        // جلب الإشعارات بالطريقة الصحيحة بناءً على user_id لتجنب خطأ أعمدة لارافيل الافتراضية
        $notifications = Notification::where('user_id', $user->id)->latest()->take(10)->get();

        return view('student.show', compact('subject', 'notifications'));
    }

    // عرض قائمة الطلاب (للمعلم)
    public function index()
    {
        $teacher = auth()->user();
        
        $subjects = $teacher->subjects()->with(['students' => function ($query) {
            $query->where('role', 'student');
        }])->get();

        return view('teacher.students.index', compact('subjects'));
    }

    // ---------------------------------------------------------
    // ميزة تصدير طلاب المعلم إلى ملف Excel / CSV
    // ---------------------------------------------------------
    public function exportStudents()
    {
        $teacher = Auth::user();
        
        // جلب الطلاب المرتبطين بمساقات المعلم فقط
        $subjects = $teacher->subjects()->with(['students' => function ($query) {
            $query->where('role', 'student');
        }])->get();

        // تجميع كل الطلاب بدون تكرار
        $students = $subjects->flatMap->students->unique('id');

        $fileName = 'teacher_students_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // إضافة UTF-8 BOM لضمان دعم ظهور اللغة العربية في إكسل
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // عناوين الأعمدة مع الفاصلة المنقوطة لتوزيعها في أعمدة إكسل
            fputcsv($file, ['الايميل', 'الاسم', 'البريد الإلكتروني'], ';');

            // تفاصيل الطلاب
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->id,
                    $student->name,
                    $student->email
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // صفحة إضافة طالب جديد (للمعلم)
    public function create()
    {
        return view('teacher.students.create');
    }

    // حفظ الطالب الجديد (للمعلم)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6'],
            ]);

            $student = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'student',
            ]);

            $teacher = Auth::user();
            $subject = $teacher->subjects()->first();
            if ($subject) {
                $subject->students()->attach($student->id);
            }

            Notification::create([
                'user_id' => $teacher->id,
                'message' => 'تم إضافة الطالب بنجاح: ' . $student->name,
            ]);

            Log::info('تم إضافة طالب جديد بنجاح: ' . $student->name);

            return redirect()->route('teacher.students.index')->with('success', 'تم إضافة الطالب وتسجيله في المسار بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في إضافة طالب جديد: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ ما أثناء إضافة الطالب.');
        }
    }

    // تعديل وتحديث بيانات الطالب (للمعلم)
    public function update(Request $request, $id)
    {
        try {
            $student = User::where('id', $id)->where('role', 'student')->firstOrFail();

            $validated = $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $student->id],
                'password' => ['nullable', 'string', 'min:6'],
            ]);

            $student->name = $validated['name'];
            $student->email = $validated['email'];
            
            if (!empty($validated['password'])) {
                $student->password = Hash::make($validated['password']);
            }
            
            $student->save();

            Notification::create([
                'user_id' => Auth::id(),
                'message' => 'تم تعديل بيانات الطالب بنجاح: ' . $student->name,
            ]);

            return redirect()->route('teacher.students.index')->with('success', 'تم تعديل بيانات الطالب بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في تعديل بيانات الطالب: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ ما أثناء تعديل بيانات الطالب.');
        }
    }

    // إزالة الطالب من المساق
    public function removeStudent($subjectId, $studentId)
    {
        try {
            $subject = Subject::findOrFail($subjectId);
            $student = User::where('id', $studentId)->where('role', 'student')->firstOrFail();

            $subject->students()->detach($studentId);

            Notification::create([
                'user_id' => Auth::id(),
                'message' => 'تم إلغاء تسجيل الطالب من المادة بنجاح: ' . $student->name,
            ]);

            Log::info('تم إزالة الطالب: ' . $student->name . ' من المساق ID: ' . $subjectId);

            return redirect()->back()->with('success', 'تم إلغاء تسجيل الطالب من المادة بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في إزالة الطالب من المساق: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ ما أثناء إزالة الطالب.');
        }
    }

    // حذف طالب نهائياً من النظام
    public function destroy($id)
    {
        try {
            $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
            $studentName = $student->name;
            
            $student->delete();

            Notification::create([
                'user_id' => Auth::id(),
                'message' => 'تم حذف الطالب بنجاح: ' . $studentName,
            ]);

            return redirect()->route('teacher.students.index')->with('success', 'تم حذف الطالب بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في حذف الطالب: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ ما أثناء حذف الطالب.');
        }
    }

    // حذف إشعار محدد عند الضغط على زر الحذف بجانبه
    public function destroyNotification($id)
    {
        $notification = Notification::where('id', $id)
                    ->where('user_id', auth()->id())
                    ->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    // جلب المحاضرات بصيغة JSON
    public function getLessons()
    {
        $lessons = Lesson::with('teacher')->latest()->get();
        return response()->json($lessons);
    }

    // جلب الواجبات بصيغة JSON
    public function getAssignments()
    {
        $assignments = Assignment::with('teacher')->latest()->get();
        return response()->json($assignments);
    }

    // عرض صفحة المحاضرات للطالب
    public function lessons()
    {
        $user = Auth::user();
        $enrolledSubjects = $user->enrolledSubjects;
        $subjectIds = $enrolledSubjects->pluck('id');
        
        $lessons = Lesson::whereIn('subject_id', $subjectIds)->with(['subject', 'teacher'])->latest()->get();
        
        // التعديل هنا لتجنب الخطأ
        $notifications = Notification::where('user_id', $user->id)->latest()->take(10)->get();

        return view('student.lessons', compact('enrolledSubjects', 'lessons', 'notifications'));
    }

    // عرض صفحة الواجبات والاختبارات للطالب
    public function assignments()
    {
        $user = Auth::user();
        $enrolledSubjects = $user->enrolledSubjects;
        $subjectIds = $enrolledSubjects->pluck('id');

        $assignments = Assignment::whereIn('subject_id', $subjectIds)->with(['subject', 'teacher'])->latest()->get();
        $submittedAssignmentIds = Submission::where('user_id', $user->id)->pluck('assignment_id')->toArray();
        
        // التعديل هنا لتجنب الخطأ
        $notifications = Notification::where('user_id', $user->id)->latest()->take(10)->get();

        return view('student.assignments', compact('assignments', 'submittedAssignmentIds', 'notifications'));
    }

    // عرض صفحة الإعلانات للطالب
    public function announcements()
    {
        $student = Auth::user();
        $subjectIds = $student->subjects()->pluck('subjects.id');

        $announcements = Announcement::whereIn('subject_id', $subjectIds)
            ->orWhereNull('subject_id')
            ->latest()
            ->get();
        
        $notifications = Notification::where('user_id', $student->id)->latest()->take(10)->get();

        return view('student.announcements', compact('announcements', 'notifications'));
    }
    
    public function downloadAssignment($id)
    {
        $assignment = Assignment::findOrFail($id);
        
        if (!$assignment->file || !Storage::disk('public')->exists($assignment->file)) {
            return back()->with('error', 'الملف غير موجود.');
        }

        return Storage::disk('public')->download($assignment->file);
    }

    // دالة تسليم حل الاختبار أو الواجب من قبل الطالب
    public function submitAssignment(Request $request, $id)
    {
        $request->validate([
            'solution_file' => ['required', 'file', 'mimes:pdf,doc,docx,zip,png,jpg,jpeg', 'max:10240'],
            'notes'         => ['nullable', 'string'],
        ]);

        $filePath = $request->file('solution_file')->store('submissions', 'public');

        Submission::updateOrCreate(
            [
                'assignment_id' => $id,
                'user_id'       => Auth::id(),
            ],
            [
                'solution_file' => $filePath,
                'solution_text' => $request->notes,
                'submitted_at'  => now(),
            ]
        );

        return back()->with('success', 'تم تسليم حل الواجب/الاختبار بنجاح.');
    }

    // عرض صفحة تعديل الملف الشخصي للطالب
    public function editProfile()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)->latest()->take(10)->get();
        
        return view('student.profile.edit', compact('user', 'notifications'));
    }

    // معالجة تحديث الملف الشخصي للطالب
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'تم تحديث البيانات الشخصية بنجاح.');
    }
}