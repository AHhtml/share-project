<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ManagementController extends Controller
{
    // عرض لوحة تحكم الإدارة مع الإحصائيات وجلب قائمة الطلاب المسجلين
    public function dashboard()
    {
        $studentsCount = User::where('role', 'student')->count();
        $teachersCount = User::where('role', 'teacher')->count();
        $lessonsCount = Lesson::count();
        $assignmentsCount = Assignment::count();

        // جلب أحدث المستخدمين (معلمين وطلاب) مع جلب المواد المرتبطة بهم
        $users = User::whereIn('role', ['student', 'teacher'])
                     ->with('subjects')
                     ->latest()
                     ->take(10)
                     ->get();

        return view('management.dashboard', compact(
            'studentsCount',
            'teachersCount',
            'lessonsCount',
            'assignmentsCount',
            'users'
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
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'تم تحديث بياناتك الشخصية والصورة بنجاح');
    }

    // عرض قائمة جميع المستخدمين مع دعم البحث والفلترة المتقدمة (Clean Code & Query Scopes)
    public function usersIndex(Request $request)
    {
        $filters = $request->only(['search', 'status', 'date_from', 'date_to', 'sort_by']);

        $users = User::filter($filters)
                     ->with('subjects')
                     ->paginate(10)
                     ->withQueryString();

        return view('management.users.index', compact('users'));
    }

    // عرض قائمة جميع الطلاب في المركز مع المساقات المتاحة ودعم البحث والفلترة المتقدمة
    public function allStudentsIndex(Request $request)
    {
        $filters = $request->only(['search', 'status', 'date_from', 'date_to', 'sort_by']);

        $students = User::where('role', 'student')
                        ->filter($filters)
                        ->with('subjects')
                        ->paginate(10)
                        ->withQueryString();
                        
        $subjects = Subject::all();

        return view('management.students.index', compact('students', 'subjects'));
    }

    // عرض محاضرات واختبارات المعلم المحدد
    public function teacherContent($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        
        Log::info("الإدارة (" . auth()->user()->name . ") قامت بعرض محتوى المعلم: {$teacher->name} (ID: {$teacher->id})");

        $lessons = Lesson::where('teacher_id', $teacher->id)->with('subject')->get();
        $assignments = Assignment::where('teacher_id', $teacher->id)->get();

        return view('management.teacher-content', compact('teacher', 'lessons', 'assignments'));
    }

    // عرض الطلاب المسجلين في مادة معينة
    public function subjectStudents($id)
    {
        $subject = Subject::findOrFail($id);
        $students = $subject->users()->where('role', 'student')->get();

        return view('management.subject-students', compact('subject', 'students'));
    }

    // عرض واجهة إضافة طالب لمساق معين
    public function createSubjectStudent($id)
    {
        $subject = Subject::findOrFail($id);
        $students = User::where('role', 'student')->get();

        return view('management.create', compact('subject', 'students'));
    }

    // حفظ طالب جديد وربطه بالمساق مباشرة
    public function storeSubjectStudent(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $subject = Subject::findOrFail($id);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $student = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'avatar' => $avatarPath,
        ]);

        $subject->users()->syncWithoutDetaching([$student->id]);

        return redirect()->route('management.subjects.students', $id)->with('success', 'تم تسجيل الطالب الجديد وإضافته للمساق بنجاح');
    }

    // إزالة طالب من المساق الأكاديمي
    public function removeStudentFromSubject($subjectId, $studentId)
    {
        $subject = Subject::findOrFail($subjectId);
        $subject->users()->detach($studentId);

        return redirect()->back()->with('success', 'تم إزالة الطالب من المساق بنجاح');
    }

    // تخزين مستخدم جديد (معلم أو طالب) من قبل الإدارة
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6',
            'role' => 'required|in:teacher,student,admin',
            'subject_id' => 'nullable|exists:subjects,id', // للمعلم القديم أو الاحتياط
            'subjects' => 'nullable|array', // للمساقات المتعددة للطالب
            'subjects.*' => 'exists:subjects,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'subject_id' => $request->subject_id,
            'avatar' => $avatarPath,
        ]);

        // ربط المساقات حسب دور المستخدم
        if ($request->role === 'teacher' && $request->filled('subject_id')) {
            $user->subjects()->sync([$request->subject_id]);
        } elseif ($request->role === 'student' && $request->has('subjects')) {
            $user->subjects()->sync($request->subjects);
        }

        Log::info("الإدارة (" . auth()->user()->name . ") قامت بإضافة {$request->role} جديد باسم: {$user->name} (ID: {$user->id})");

        return redirect()->back()->with('success', 'تم إضافة المستخدم بنجاح');
    }

    // حذف المستخدم من النظام
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الشخصي أثناء تسجيل الدخول.');
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // فك ارتباطات المعلم أو الطالب بالمساقات قبل الحذف لمنع أخطاء قاعدة البيانات
        $user->subjects()->detach();

        $userName = $user->name;
        $userId = $user->id;
        $userRole = $user->role;

        $user->delete();

        Log::warning("الإدارة (" . auth()->user()->name . ") قامت بحذف المستخدم [{$userRole}] المسمى: {$userName} (ID: {$userId})");

        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
    }

    // عرض واجهة تعديل بيانات الطالب
    public function editStudent($id)
    {
        $student = User::where('role', 'student')->with('subjects')->findOrFail($id);
        $subjects = Subject::all();
        return view('management.edit-student', compact('student', 'subjects'));
    }

    // تحديث بيانات الطالب في قاعدة البيانات
    public function updateStudent(Request $request, $id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
                Storage::disk('public')->delete($student->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $student->update($data);

        // تحديث المساقات الخاصة بال학생
        $student->subjects()->sync($request->input('subjects', []));

        Log::info("الإدارة (" . auth()->user()->name . ") قامت بتحديث بيانات الطالب: {$student->name} (ID: {$student->id})");

        return redirect()->back()->with('success', 'تم تعديل بيانات الطالب بنجاح');
    }

    // تحديث بيانات المستخدم (معلم أو طالب) بالكامل
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6',
            'subject_id' => 'nullable|exists:subjects,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject_id' => $request->subject_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        if ($user->role === 'teacher') {
            if ($request->filled('subject_id')) {
                $user->subjects()->sync([$request->subject_id]);
            } else {
                $user->subjects()->detach();
            }
        } elseif ($user->role === 'student') {
            $user->subjects()->sync($request->input('subjects', []));
        }

        Log::info("الإدارة (" . auth()->user()->name . ") قامت بتحديث بيانات المستخدم: {$user->name} (ID: {$user->id})");

        return redirect()->route('management.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }
}