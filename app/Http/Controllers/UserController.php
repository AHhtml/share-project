<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;

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

    // ---------------------------------------------------------
    // ميزة 1: تصدير البيانات (Data Export) إلى ملف CSV
    // ---------------------------------------------------------
    public function export(Request $request)
    {
        $fileName = 'users_' . date('Y-m-d') . '.csv';
        
        // إمكانية تصدير البيانات المفترة فقط (مثلاً حسب الدور أو كلها)
        $users = User::all(); 

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($users) {
            $file = fopen('php://output', 'w');
            
            // إضافة UTF-8 BOM لضمان ظهور اللغة العربية بشكل سليم في إكسل
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // رؤوس الأعمدة
            fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Created At']);

            // كتابة البيانات
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id, 
                    $user->name, 
                    $user->email, 
                    $user->role, 
                    $user->created_at
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------
    // ميزة 2: استيراد البيانات والتحقق (Data Import & Validation & Logs)
    // ---------------------------------------------------------
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $data = array_map('str_getcsv', file($path));
        
        if (count($data) <= 1) {
            return redirect()->back()->with('error', 'الملف فارغ أو لا يحتوي على بيانات.');
        }

        $header = array_shift($data); // إزالة سطر العناوين الأول
        $successCount = 0;

        foreach ($data as $index => $row) {
            $lineNumber = $index + 2; // رقم السطر الفعلي في الملف
            
            $name     = $row[0] ?? null;
            $email    = $row[1] ?? null;
            $role     = $row[2] ?? 'student'; // قيمة افتراضية إذا لم تذكر
            $password = $row[3] ?? '12345678'; // كلمة مرور افتراضية

            // قواعد التحقق: إذا كان السطر يحوي بيانات مفقودة (الاسم أو الإيميل)
            if (empty($name) || empty($email)) {
                Log::error("خطأ استيراد [سطر $lineNumber]: بيانات مفقودة (الاسم أو البريد الإلكتروني).");
                continue; // منع دخول هذا السطر واستكمال الباقي
            }

            // قواعد التحقق: منع تكرار البريد الإلكتروني
            if (User::where('email', $email)->exists()) {
                Log::error("خطأ استيراد [سطر $lineNumber]: البريد الإلكتروني مكرر ($email).");
                continue;
            }

            // إدخال البيانات للقاعدة بنجاح
            User::create([
                'name'     => $name,
                'email'    => $email,
                'role'     => $role,
                'password' => Hash::make($password),
            ]);

            $successCount++;
        }

        // إرسال إشعار للأدمن (Notification / Success message) يوضح عدد السجلات المستوردة
        return redirect()->back()->with('success', "تم استيراد [$successCount] سجل بنجاح من الملف. (تم تسجيل الأخطاء إن وجدت في الـ Logs).");
    }
}