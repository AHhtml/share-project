<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    // لوحة تحكم الطالب
    public function dashboard()
    {
        $user = Auth::user();

        // جلب المواد المسجل فيها الطالب مع المحاضرات والاختبارات التابعة لها
        $enrolledSubjects = $user->enrolledSubjects()->with(['lessons', 'assignments'])->get();

        $lessonsCount = Lesson::whereIn('subject_id', $enrolledSubjects->pluck('id'))->count();
        $assignmentsCount = Assignment::whereIn('subject_id', $enrolledSubjects->pluck('id'))->count();
        $completedExamsCount = Submission::where('student_id', $user->id)->count();

        $latestLessons = Lesson::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->with(['subject', 'teacher'])
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'enrolledSubjects',
            'lessonsCount',
            'assignmentsCount',
            'completedExamsCount',
            'latestLessons'
        ));
    }

    // عرض قائمة الطلاب (للمعلم - يظهر فقط الطلاب المسجلين في مواد هذا المعلم)
    public function index()
    {
        $teacher = auth()->user();
        
        // جلب المساقات الخاصة بالمعلم مع الطلاب الذين لديهم دور طالب فقط
        $subjects = $teacher->subjects()->with(['students' => function ($query) {
            $query->where('role', 'student'); // تصفية الطلاب فقط واستبعاد المعلمين
        }])->get();

        return view('teacher.students.index', compact('subjects'));
    }

    // صفحة إضافة طالب جديد (للمعلم)
    public function create()
    {
        return view('teacher.students.create');
    }

    // حفظ الطالب الجديد (للمعلم)
    public function store(Request $request)
    {
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

        // ربط الطالب بالمادة الأولى الخاصة بالمعلم الحالي تلقائياً
        $teacher = Auth::user();
        $subject = $teacher->subjects()->first();
        if ($subject) {
            $subject->students()->attach($student->id);
        }

        return redirect()->route('teacher.students.index')->with('success', 'تم إضافة الطالب وتسجيله في المسار بنجاح.');
    }

    // حذف طالب (للمعلم)
    public function destroy($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        $student->delete();

        return redirect()->route('teacher.students.index')->with('success', 'تم حذف الطالب بنجاح.');
    }

    // جلب جميع المحاضرات بصيغة JSON لطلبات الـ AJAX
    public function getLessons()
    {
        $lessons = Lesson::with('user')->latest()->get();
        return response()->json($lessons);
    }

    // جلب جميع الواجبات بصيغة JSON لطلبات الـ AJAX
    public function getAssignments()
    {
        $assignments = Assignment::with('user')->latest()->get();
        return response()->json($assignments);
    }

    // عرض صفحة المحاضرات للطالب (Blade View)
    public function lessonsIndex()
    {
        $lessons = Lesson::with('teacher')->latest()->get();
        return view('student.lessons', compact('lessons'));
    }

    // عرض صفحة الواجبات والاختبارات للطالب (Blade View)
    public function assignmentsIndex()
    {
        $assignments = Assignment::with('teacher')->latest()->get();
        $user = Auth::user();
        
        // جلب معرفات الواجبات التي قام الطالب بتسليمها مسبقاً
        $submittedAssignmentIds = Submission::where('student_id', $user->id)->pluck('assignment_id')->toArray();

        return view('student.assignments', compact('assignments', 'submittedAssignmentIds'));
    }

    // عرض صفحة الإعلانات للطالب مع بيانات المعلم والمادة
        public function announcementsIndex()
        {
            $student = auth()->user();
            
            // جلب معرفات المواد التي سجل فيها الطالب فقط
            $subjectIds = $student->subjects()->pluck('subjects.id');

            // جلب الإعلانات التابعة لمواد الطالب أو الإعلانات العامة الفارغة
            $announcements = Announcement::whereIn('subject_id', $subjectIds)
                                ->orWhereNull('subject_id')
                                ->latest()
                                ->get();

            return view('student.announcements', compact('announcements')); // أو اسم الview الخاص بإعلانات الطالب لديهم
        }
}