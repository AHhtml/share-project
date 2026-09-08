<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;

class ManagementController extends Controller
{
    // عرض لوحة تحكم الإدارة مع الإحصائيات وأحدث المستخدمين
    public function dashboard()
    {
        $studentsCount = User::where('role', 'student')->count();
        $teachersCount = User::where('role', 'teacher')->count();
        $lessonsCount = Lesson::count();
        $assignmentsCount = Assignment::count();

        // إحضار أحدث المستخدمين المسجلين
        $latestUsers = User::latest()->take(10)->get();

        return view('management.dashboard', compact(
            'studentsCount',
            'teachersCount',
            'lessonsCount',
            'assignmentsCount',
            'latestUsers'
        ));
    }
    
    // عرض صفحة الملف الشخصي
    public function profile()
    {
        $user = auth()->user();
        return view('management.profile', compact('user'));
    }

    // تحديث بيانات الملف الشخصي للإدارة
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'subject_id' => $request->subject_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'تم تحديث بياناتك الشخصية بنجاح');
    }

    // عرض قائمة جميع المستخدمين (مع إمكانية البحث أو الفرز لاحقاً)
    public function usersIndex()
    {
        $users = User::latest()->paginate(10);
        return view('management.users.index', compact('users'));
    }

    // عرض محاضرات واختبارات المعلم المحدد
    public function teacherContent($id)
    {
        $teacher = User::findOrFail($id);
        
        // جلب محاضرات واختبارات هذا المعلم
        $lessons = Lesson::where('teacher_id', $teacher->id)->with('subject')->get();
        $assignments = Assignment::where('teacher_id', $teacher->id)->get();

        return view('management.teacher-content', compact('teacher', 'lessons', 'assignments'));
    }

    // عرض الطلاب المسجلين في مادة معينة
    public function subjectStudents($id)
    {
        $subject = Subject::findOrFail($id);
        
        // جلب الطلاب المرتبطين بهذه المادة حصرياً
        $students = $subject->users()->where('role', 'student')->get();

        return view('management.subject-students', compact('subject', 'students'));
    }

    // إزالة طالب من المساق الأكاديمي
    public function removeStudentFromSubject($subjectId, $studentId)
    {
        $subject = Subject::findOrFail($subjectId);
        
        // إزالة الارتباط بين الطالب والمادة من جدول الربط
        $subject->users()->detach($studentId);

        return redirect()->back()->with('success', 'تم إزالة الطالب من المساق بنجاح');
    }

    // تخزين مستخدم جديد (معلم أو طالب) من قبل الإدارة
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:teacher,student,admin',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'subject_id' => $request->subject_id,
        ]);

        return redirect()->back()->with('success', 'تم إضافة المستخدم بنجاح');
    }

    // حذف المستخدم من النظام
    public function destroyUser(User $user)
    {
        // حماية إضافية لعدم حذف الحساب الحالي أو حسابات حساسة عن طريق الخطأ إذا لزم الأمر
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الشخصي أثناء تسجيل الدخول.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
    }

    // عرض واجهة تعديل بيانات الطالب
    public function editStudent($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        return view('management.edit-student', compact('student'));
    }

    // تحديث بيانات الطالب في قاعدة البيانات
    public function updateStudent(Request $request, $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // تحديث كلمة المرور فقط في حال أدخل الإداري قيمة جديدة لها
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $student->update($data);

        return redirect()->back()->with('success', 'تم تعديل بيانات الطالب بنجاح');
    }

    // تحديث بيانات المستخدم (معلم أو طالب) بالكامل بما في ذلك المادة الدراسية
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'subject_id' => $request->subject_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('management.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }
}