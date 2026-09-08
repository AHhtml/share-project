<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // 1. تم إضافة واجهة الـ Log هنا

class AssignmentController extends Controller
{
    // عرض جميع الاختبارات
    public function index()
    {
        $teacher = Auth::user();
        $assignments = Assignment::where('teacher_id', $teacher->id)->latest()->get();

        return view('teacher.assignments.index', compact('assignments'));
    }

    // صفحة إضافة اختبار جديد
    public function create()
    {
        return view('teacher.assignments.create');
    }

    // حفظ الاختبار الجديد
    public function store(Request $request)
    {
        try { // 2. استخدام try لمراقبة عملية الحفظ وتسجيل السجلات
            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'due_date'    => ['required', 'date'],
                'duration'    => ['nullable', 'string', 'max:100'],
                'total_marks' => ['required', 'integer'],
                'description' => ['nullable', 'string'],
                'file'        => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,png,jpg', 'max:10240'],
            ]);

            $teacher = Auth::user();
            
            $subject = $teacher->subjects()->first() ?? \App\Models\Subject::first();

            if (!$subject) {
                Log::warning('محاولة إضافة اختبار بدون وجود مادة دراسية بواسطة المعلم ID: ' . $teacher->id);
                return back()->with('error', 'الرجاء التأكد من وجود مادة دراسية واحدة على الأقل في النظام.');
            }

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('assignments', 'public');
            }

            Assignment::create([
                'subject_id'  => $subject->id,
                'teacher_id'  => $teacher->id,
                'title'       => $validated['title'],
                'due_date'    => $validated['due_date'],
                'duration'    => $validated['duration'] ?? null,
                'total_marks' => $validated['total_marks'],
                'description' => $validated['description'] ?? null,
                'file_path'   => $filePath,
            ]);

            // 3. تسجيل نجاح العملية في ملفات الـ Logs
            Log::info('تم إضافة اختبار جديد بنجاح بعنوان: "' . $validated['title'] . '" بواسطة المعلم ID: ' . $teacher->id);

            return redirect()->route('teacher.assignments.index')->with('success', 'تم إضافة الاختبار بنجاح.');

        } catch (\Exception $e) {
            // 4. تسجيل الخطأ الفادح في حال حدوث استثناء أو فشل
            Log::error('فشل في حفظ الاختبار بواسطة المعلم ID: ' . Auth::id() . ' | الخطأ: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ ما أثناء حفظ الاختبار.');
        }
    }

    // صفحة تعديل الاختبار
    public function edit($id)
    {
        $assignment = Assignment::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
        return view('teacher.assignments.edit', compact('assignment'));
    }

    // تحديث بيانات الاختبار
    public function update(Request $request, $id)
    {
        try { // استخدام try-catch أيضاً داخل عملية التحديث
            $assignment = Assignment::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();

            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'due_date'    => ['required', 'date'],
                'duration'    => ['nullable', 'string', 'max:100'],
                'total_marks' => ['required', 'integer', 'min:1'],
                'description' => ['nullable', 'string'],
                'file'        => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,png,jpg', 'max:10240'],
            ]);

            if ($request->hasFile('file')) {
                if ($assignment->file_path && Storage::disk('public')->exists($assignment->file_path)) {
                    Storage::disk('public')->delete($assignment->file_path);
                }
                $validated['file_path'] = $request->file('file')->store('assignments', 'public');
            }

            $assignment->update($validated);

            // تسجيل نجاح عملية التحديث
            Log::info('تم تحديث بيانات الاختبار ID: ' . $assignment->id . ' بنجاح بواسطة المعلم ID: ' . Auth::id());

            return redirect()->route('teacher.assignments.index')->with('success', 'تم تحديث بيانات الاختبار بنجاح.');

        } catch (\Exception $e) {
            // تسجيل خطأ التحديث في حال حدثت مشكلة
            Log::error('فشل في تحديث الاختبار ID: ' . $id . ' | الخطأ: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ ما أثناء تحديث الاختبار.');
        }
    }

    // حذف الاختبار
    public function destroy($id)
    {
        try {
            $assignment = Assignment::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
            
            if ($assignment->file_path && Storage::disk('public')->exists($assignment->file_path)) {
                Storage::disk('public')->delete($assignment->file_path);
            }

            $assignment->delete();

            // تسجيل عملية الحذف
            Log::info('تم حذف الاختبار ID: ' . $id . ' بواسطة المعلم ID: ' . Auth::id());

            return redirect()->route('teacher.assignments.index')->with('success', 'تم حذف الاختبار بنجاح.');
            
        } catch (\Exception $e) {
            Log::error('فشل في حذف الاختبار ID: ' . $id . ' | الخطأ: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ ما أثناء حذف الاختبار.');
        }
    }
}