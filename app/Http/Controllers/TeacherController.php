<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use App\Models\Announcement;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = Auth::user();

        // جلب مواد هذا المعلم مع تجنب الأخطاء إذا لم تكن موجودة
        $teacherSubjects = $teacher->subjects ?? collect();

        // إحصائيات المعلم
        $myLessonsCount = Lesson::where('teacher_id', $teacher->id)->count();
        $myAssignmentsCount = Assignment::where('teacher_id', $teacher->id)->count();
        
        // جلب معرفات المواد الخاصة بالمعلم الحالي
        $subjectIds = $teacherSubjects->pluck('id');

        // حساب عدد الطلاب الحقيقيين فقط (استبعاد المعلمين) المرتبطين بمساقات هذا المعلم
        $totalStudentsCount = 0;
        if ($subjectIds->isNotEmpty()) {
            $totalStudentsCount = DB::table('subject_user')
                ->join('users', 'subject_user.user_id', '=', 'users.id')
                ->whereIn('subject_user.subject_id', $subjectIds)
                ->where('users.role', 'student')
                ->distinct('subject_user.user_id')
                ->count('subject_user.user_id');
        }

        // جلب دروس المعلم المرتبطة بالمادة
        $myLessons = Lesson::where('teacher_id', $teacher->id)
            ->with('subject')
            ->latest()
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact(
            'myLessonsCount',
            'myAssignmentsCount',
            'totalStudentsCount',
            'myLessons',
            'teacherSubjects'
        ));
    }

    /**
     * عرض صفحة الإعلانات المستقلة للمعلم
     */
    public function announcementsIndex()
    {
        $announcements = DB::table('announcements')->latest()->get(); 

        return view('teacher.announcements.index', compact('announcements'));
    }

    /**
     * حفظ ونشر إعلان جديد وإرسال إشعارات للطلاب المستهدفين
     */
    public function announcementsStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        // جلب مادة المعلم الحالية تلقائياً
        $teacherSubject = auth()->user()->subjects()->first();

        $announcement = Announcement::create([
            'title' => $request->title,
            'body' => $request->body,
            'subject_id' => $teacherSubject ? $teacherSubject->id : null,
            'user_id' => auth()->id(),
        ]);

        // جلب الطلاب المرتبطين بالمساق أو جميع طلاب المعلم لإرسال الإشعار إليهم
        if ($teacherSubject) {
            $students = $teacherSubject->students;
        } else {
            $teacher = Auth::user();
            $subjectIds = $teacher->subjects()->pluck('subjects.id');
            $students = User::where('role', 'student')
                ->whereHas('enrolledSubjects', function($q) use ($subjectIds) {
                    $q->whereIn('subjects.id', $subjectIds);
                })->get();
        }

        // إنشاء إشعار لكل طالب
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'message' => 'تم نشر إعلان جديد: ' . $announcement->title,
            ]);
        }

        return redirect()->back()->with('success', 'تم نشر الإعلان وإرسال التنبيهات للطلاب بنجاح.');
    }

    /**
     * عرض صفحة الملف الشخصي للمعلم
     */
    public function profile()
    {
        $user = Auth::user();
        return view('teacher.profile', compact('user'));
    }

    /**
     * تحديث بيانات الملف الشخصي للمعلم مع إنشاء إشعار خاص به
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // إنشاء إشعار للمعلم بتحديث بيانات ملفه الشخصي
        Notification::create([
            'user_id' => $user->id,
            'message' => 'تم تحديث بيانات ملفك الشخصي بنجاح.',
        ]);

        return redirect()->back()->with('success', 'تم تحديث بياناتك الشخصية بنجاح');
    }

    /**
     * إلغاء تسجيل طالب من مادة المعلم (فك الربط في الجدول الوسيط دون حذف الحساب)
     */
    public function removeStudent(Subject $subject, User $student)
    {
        $teacher = Auth::user();

        // 1. التحقق من أن المادة تابعة للمعلم الحالي عبر الجدول الوسيط
        if (!$teacher->subjects()->where('subjects.id', $subject->id)->exists()) {
            abort(403, 'غير مصرح لك بإدارة هذه المادة.');
        }

        // 2. فك الربط بين الطالب والمادة في جدول subject_user
        $subject->students()->detach($student->id);

        return back()->with('success', 'تم إلغاء تسجيل الطالب من المادة بنجاح.');
    }

    /**
     * حذف الإعلان وإرسال إشعار للطلاب بحذفه
     */
    public function destroyAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        $title = $announcement->title;

        // إرسال إشعار للطلاب بحذف الإعلان إذا كان مرتبطاً بمادة
        if ($announcement->subject_id) {
            $subject = Subject::with('students')->find($announcement->subject_id);
            if ($subject && $subject->students) {
                foreach ($subject->students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'message' => 'تم حذف الإعلان: ' . $title,
                    ]);
                }
            }
        }

        $announcement->delete();

        return redirect()->route('teacher.announcements.index')->with('success', 'تم حذف الإعلان بنجاح.');
    }
}