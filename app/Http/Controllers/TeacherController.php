<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use App\Models\Announcement; // تأكد من استدعاء مودل الإعلانات إذا كان موجوداً، أو استبدله بـ DB::table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = Auth::user();

        // جلب مواد هذا المعلم
        $teacherSubjects = $teacher->subjects;

        // إحصائيات المعلم
        $myLessonsCount = Lesson::where('teacher_id', $teacher->id)->count();
        $myAssignmentsCount = Assignment::where('teacher_id', $teacher->id)->count();
        
        // جلب معرفات المواد الخاصة بالمعلم الحالي
        $subjectIds = $teacherSubjects->pluck('id');

        // حساب عدد الطلاب الحقيقيين فقط (استبعاد المعلمين) المرتبطين بمساقات هذا المعلم
        $totalStudentsCount = \DB::table('subject_user')
            ->join('users', 'subject_user.user_id', '=', 'users.id')
            ->whereIn('subject_user.subject_id', $subjectIds)
            ->where('users.role', 'student')
            ->distinct('subject_user.user_id')
            ->count('subject_user.user_id');

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
        // جلب الإعلانات من قاعدة البيانات (مرتبة من الأحدث للأقدم)
        // ملاحظة: تأكد أن جدول الإعلانات عندك اسمه announcements أو قم بتعديله حسب رغبتك
        $announcements = \DB::table('announcements')->latest()->get(); 

        return view('teacher.announcements.index', compact('announcements'));
    }

    /**
     * حفظ ونشر إعلان جديد
     */
        public function announcementsStore(Request $request)
        {
            $request->validate([
                'title' => 'required',
                'body' => 'required',
            ]);

            // جلب مادة المعلم الحالية تلقائياً
            $teacherSubject = auth()->user()->subjects()->first();

            Announcement::create([
                'title' => $request->title,
                'body' => $request->body,
                'subject_id' => $teacherSubject ? $teacherSubject->id : null, // ربط تلقائي بمادة المعلم
                'user_id' => auth()->id(),            // حفظ معرف المعلم
            ]);

            return redirect()->back()->with('success', 'تم نشر الإعلان بنجاح.');
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
     * تحديث بيانات الملف الشخصي للمعلم
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

        return redirect()->back()->with('success', 'تم تحديث بياناتك الشخصية بنجاح');
    }

    /**
     * إلغاء تسجيل طالب من مادة المعلم (فك الربط في الجدول الوسيط دون حذف الحساب)
     */
    public function removeStudent(Subject $subject, User $student)
    {
        // 1. التحقق من أن المادة تخص المعلم الحالي لحماية البيانات
        if ($subject->teacher_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بإدارة هذه المادة.');
        }

        // 2. فك الربط بين الطالب والمادة في جدول subject_user
        $subject->students()->detach($student->id);

        return back()->with('success', 'تم إلغاء تسجيل الطالب من المادة بنجاح.');
    }
 public function destroyAnnouncement($id)
{
    $announcement = \App\Models\Announcement::findOrFail($id);
    $announcement->delete();

    return redirect()->route('teacher.announcements.index')->with('success', 'تم حذف الإعلان بنجاح.');
}

}