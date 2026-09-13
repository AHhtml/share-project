<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // استدعاء مكتبة الـ Log
use App\Notifications\LoginNotification; // استدعاء إشعار تسجيل الدخول

class AuthController extends Controller
{
    // عرض صفحة تسجيل الدخول
    public function showLogin()
    {
        return view('pages.login');
    }

    // معالجة تسجيل الدخول
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                
                $user = Auth::user();

                // إرسال إشعار الأمان عند تسجيل الدخول الناجح
                $user->notify(new LoginNotification());

                // تسجيل عملية الدخول الناجحة في ملفات الـ Logs
                // Log::info('تم تسجيل الدخول بنجاح للمستخدم: ' . $user->email . ' (ID: ' . $user->id . ')');
                Log::info('تم تسجيل الدخول بنجاح للمستخدم: ' . $user->name . ' - الإيميل: ' . $user->email  . ' (ID: ' . $user->id . ')');
                // Log::info('تم تسجيل الدخول بنجاح للمستخدم: ' . $user->name . ' - الإيميل: ' . $user->email . ' (ID: ' . $user->id . ')');

                return $this->redirectBasedOnRole($user);
            }

            // تسجيل محاولة دخول فاشلة في الـ Logs لأسباب أمنية
            Log::warning('محاولة تسجيل دخول فاشلة للبريد الإلكتروني: ' . $request->email);

            return back()->withErrors([
                'email' => 'بيانات الدخول غير صحيحة.',
            ])->onlyInput('email');

        } catch (\Exception $e) {
            Log::error('حدث خطأ أثناء محاولة تسجيل الدخول | الخطأ: ' . $e->getMessage());
            return back()->withErrors(['email' => 'حدث خطأ ما، يرجى المحاولة لاحقاً.']);
        }
    }

    // عرض صفحة إنشاء حساب جديد
    public function showRegister()
    {
        return view('pages.register');
    }

    // معالجة إنشاء حساب جديد
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'          => ['required', 'string', 'max:255'],
                'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'phone'         => ['nullable', 'string', 'max:20'],
                'branch'        => ['nullable', 'string'],
                'role'          => ['required', 'string', 'in:admin,teacher,management,student'],
                'subject_id'    => ['nullable', 'exists:subjects,id'], // مادة المعلم
                'subject_ids'   => ['nullable', 'array'],              // مواد الطالب المتعددة
                'subject_ids.*' => ['exists:subjects,id'],
                'password'      => ['required', 'string', 'min:6', 'confirmed'],
            ]);

            // إنشاء المستخدم مع حفظ الـ subject_id مباشرة في جدول users
            $user = User::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'phone'      => $validated['phone'] ?? null,
                'branch'     => $validated['branch'] ?? null,
                'role'       => $validated['role'],
                'subject_id' => $validated['role'] === 'teacher' ? ($validated['subject_id'] ?? null) : null,
                'password'   => Hash::make($validated['password']),
            ]);

            // 1. إذا كان الدور معلماً وتم تحديد مادة، يتم ربطها عبر الجدول الوسيط أيضاً
            if ($user->role === 'teacher' && !empty($validated['subject_id'])) {
                $user->subjects()->sync([$validated['subject_id']]);
            }

            // 2. إذا كان الدور طالباً وتم تحديد مواد، يتم ربط الطالب بالمواد في الجدول الوسيط
            if ($user->role === 'student' && !empty($validated['subject_ids'])) {
                $user->subjects()->attach($validated['subject_ids']);
            }

            // إرسال إشعار تسجيل الدخول للحساب الجديد
            $user->notify(new LoginNotification());

            // تسجيل عملية التسجيل في الـ Logs
            Log::info('تم إنشاء حساب جديد بنجاح للمستخدم: ' . $user->email . ' برتبة: ' . $user->role);

            // تسجيل الدخول مباشرة
            Auth::login($user);

            // التوجيه للوحة التحكم المناسبة
            return $this->redirectBasedOnRole($user);

        } catch (\Exception $e) {
            Log::error('فشل في إنشاء الحساب الجديد | الخطأ: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ ما أثناء إنشاء الحساب.')->withInput();
        }
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $userId = Auth::id();
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('تم تسجيل الخروج للمستخدم ID: ' . $userId);

        return redirect()->route('login');
    }

    // التوجيه حسب الدور
    private function redirectBasedOnRole($user)
    {
        return match ($user->role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'management' => redirect()->route('management.dashboard'),
            'teacher'    => redirect()->route('teacher.dashboard'),
            'student'    => redirect()->route('student.dashboard'),
            default      => redirect()->route('login'),
        };
    }
}