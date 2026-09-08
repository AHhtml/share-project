<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Subject;

class UserController extends Controller
{
    // جلب قائمة المستخدمين لعرضها في صفحة الـ Blade
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10);
        $subjects = Subject::all(); // جلب كافة المساقات لتعمل القائمة المنسدلة بدون خطأ

        return view('management.users.index', compact('users', 'subjects'));
    }

    // إضافة مستخدم جديد (طالب / معلم / إدارة)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'subject_id' => 'nullable|exists:subjects,id', // جعلناه اختيارياً حتى لا يتسبب بخطأ لباقي المستخدمين
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject_id' => $request->subject_id, // حفظ رقم المساق إذا وجد
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'تم إضافة المستخدم بنجاح');
    }

    // تحديث بيانات المستخدم
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'subject_id' => 'nullable|exists:subjects,id', // اختيارياً
            'role' => 'required',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'subject_id' => $request->subject_id, // تحديث رقم المساق
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    // حذف مستخدم
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الشخصي أثناء تسجيل الدخول.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
    }
}