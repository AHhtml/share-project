<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // تحديث بيانات الطالب
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // تحديث كلمة المرور فقط إذا قام بإدخالها
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'تم تحديث بياناتك الشخصية بنجاح.');
    }
    // عرض واجهة تعديل البيانات الشخصية
    public function edit()
    {
        $user = auth()->user();
        return view('student.profile.edit', compact('user')); // استبدل المسار حسب مكان ملف الـ Blade عندك
    }
}