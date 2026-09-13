<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Subject;
use App\Models\Notification; // استدعاء موديل الإشعارات
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
        // $completedExamsCount = Submission::where('student_id', $user->id)->count();
        $completedExamsCount = Submission::where('user_id', $user->id)->count();

        $latestLessons = Lesson::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->with(['subject', 'teacher'])
            ->latest()
            ->take(5)
            ->get();

        // جلب الإعلانات الخاصة بمواد الطالب أو العامة
        $subjectIds = $user->subjects()->pluck('subjects.id');
        $announcements = Announcement::whereIn('subject_id', $subjectIds)
            ->orWhereNull('subject_id')
            ->latest()
            ->get();

        return view('student.dashboard', compact(
            'enrolledSubjects',
            'lessonsCount',
            'assignmentsCount',
            'completedExamsCount',
            'latestLessons',
            'announcements'
        ));
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
        try {
            $notification = Notification::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $notification->delete();

            return redirect()->back()->with('success', 'تم حذف الإشعار بنجاح.');
        } catch (\Exception $e) {
            Log::error('فشل في حذف الإشعار: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ ما أثناء حذف الإشعار.');
        }
    }

    // جلب المحاضرات بصيغة JSON
    public function getLessons()
    {
        $lessons = Lesson::with('user')->latest()->get();
        return response()->json($lessons);
    }

    // جلب الواجبات بصيغة JSON
    public function getAssignments()
    {
        $assignments = Assignment::with('user')->latest()->get();
        return response()->json($assignments);
    }

    // عرض صفحة المحاضرات للطالب
    public function lessonsIndex()
    {
        $lessons = Lesson::with('teacher')->latest()->get();
        return view('student.lessons', compact('lessons'));
    }

    // عرض صفحة الواجبات والاختبارات للطالب
    public function assignmentsIndex()
    {
        $assignments = Assignment::with('teacher')->latest()->get();
        $user = Auth::user();
        
        $submittedAssignmentIds = Submission::where('student_id', $user->id)->pluck('assignment_id')->toArray();

        return view('student.assignments', compact('assignments', 'submittedAssignmentIds'));
    }

    // عرض صفحة الإعلانات للطالب
    public function announcementsIndex()
    {
        $student = auth()->user();
        
        $subjectIds = $student->subjects()->pluck('subjects.id');

        $announcements = Announcement::whereIn('subject_id', $subjectIds)
                                   ->orWhereNull('subject_id')
                                   ->latest()
                                   ->get();

        return view('student.announcements', compact('announcements'));
    }
    
    public function downloadAssignment($id)
    {
        $assignment = Assignment::findOrFail($id);
        
        if (!$assignment->file || !\Storage::disk('public')->exists($assignment->file)) {
            return back()->with('error', 'الملف غير موجود.');
        }

        return Storage::disk('public')->download($assignment->file);
    }

    // دالة تسليم حل الاختبار أو الواجب من قبل الطالب
    public function storeSubmission(Request $request, $id)
    {
        $request->validate([
            'file'  => ['required', 'file', 'mimes:pdf,doc,docx,zip,png,jpg,jpeg', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ]);

        $filePath = $request->file('file')->store('submissions', 'public');

        Submission::updateOrCreate(
            [
                'assignment_id' => $id,
                'student_id'    => Auth::id(),
            ],
            [
                'solution_file' => $filePath,
                'solution_text' => $request->notes,
                'submitted_at'  => now(),
            ]
        );

        return back()->with('success', 'تم تسليم حل الاختبار بنجاح.');
    }
}