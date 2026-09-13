<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Subject;
use App\Models\Notification;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        try {
            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'due_date'    => ['required', 'date'],
                'duration'    => ['nullable', 'string', 'max:100'],
                'total_marks' => ['required', 'integer'],
                'description' => ['nullable', 'string'],
                'file'        => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,png,jpg', 'max:10240'],
            ]);

            $teacher = Auth::user();
            
            $subject = $teacher->subjects()->first() ?? Subject::first();

            if (!$subject) {
                Log::warning('محاولة إضافة اختبار بدون وجود مادة دراسية بواسطة المعلم ID: ' . $teacher->id);
                return back()->with('error', 'الرجاء التأكد من وجود مادة دراسية واحدة على الأقل في النظام.');
            }

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('assignments', 'public');
            }

            $assignment = Assignment::create([
                'subject_id'  => $subject->id,
                'teacher_id'  => $teacher->id,
                'title'       => $validated['title'],
                'due_date'    => $validated['due_date'],
                'duration'    => $validated['duration'] ?? null,
                'total_marks' => $validated['total_marks'],
                'description' => $validated['description'] ?? null,
                'file_path'   => $filePath,
            ]);

            // إرسال إشعار لجميع طلاب المادة عند إضافة اختبار جديد
            if ($subject && $subject->students) {
                foreach ($subject->students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'message' => 'تمت إضافة اختبار/واجب جديد: ' . $assignment->title,
                    ]);
                }
            }

            Log::info('تم إضافة اختبار جديد بنجاح بعنوان: "' . $validated['title'] . '" بواسطة المعلم ID: ' . $teacher->id);

            return redirect()->route('teacher.assignments.index')->with('success', 'تم إضافة الاختبار بنجاح وإرسال التنبيهات للطلاب.');

        } catch (\Exception $e) {
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
        try {
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

            // إرسال إشعار للطلاب بتعديل الاختبار
            $subject = Subject::with('students')->find($assignment->subject_id);
            if ($subject && $subject->students) {
                foreach ($subject->students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'message' => 'تم تحديث الاختبار/الواجب: ' . $assignment->title,
                    ]);
                }
            }

            Log::info('تم تحديث بيانات الاختبار ID: ' . $assignment->id . ' بنجاح بواسطة المعلم ID: ' . Auth::id());

            return redirect()->route('teacher.assignments.index')->with('success', 'تم تحديث بيانات الاختبار بنجاح وإرسال التنبيهات.');

        } catch (\Exception $e) {
            Log::error('فشل في تحديث الاختبار ID: ' . $id . ' | الخطأ: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ ما أثناء تحديث الاختبار.');
        }
    }

    // تحميل ملف الاختبار/الواجب للطالب
    public function download($id)
    {
        $assignment = Assignment::findOrFail($id);

        $filePath = $assignment->file_path ?? $assignment->file;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'الملف غير موجود.');
        }

        return Storage::disk('public')->download($filePath);
    }

    // عرض تسليمات الطلاب الخاصة باختبار معين (للمعلم)
    public function viewSubmissions($id)
    {
        $assignment = Assignment::with(['submissions.student'])
            ->where('id', $id)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();

        return view('teacher.assignments.submissions', compact('assignment'));
    }

    // تحميل ملف الحل الخاص بالطالب (للمعلم)
    public function downloadSubmission($id)
    {
        $submission = Submission::with('assignment')->findOrFail($id);
        
        // التأكد أن المعلم الحالي هو مالك الاختبار
        if ($submission->assignment->teacher_id !== Auth::id()) {
            abort(403, 'غير مصرح لك تحميل هذا الملف.');
        }

        if (!$submission->solution_file || !Storage::disk('public')->exists($submission->solution_file)) {
            return back()->with('error', 'ملف الحل غير موجود.');
        }

        return Storage::disk('public')->download($submission->solution_file);
    }

    // حذف الاختبار
    public function destroy($id)
    {
        try {
            $assignment = Assignment::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
            $title = $assignment->title;
            $subjectId = $assignment->subject_id;

            // جلب الطلاب المرتبطين بالمادة قبل الحذف
            $subject = Subject::with('students')->find($subjectId);
            
            if ($assignment->file_path && Storage::disk('public')->exists($assignment->file_path)) {
                Storage::disk('public')->delete($assignment->file_path);
            }

            $assignment->delete();

            // إرسال إشعار للطلاب بحذف الاختبار
            if ($subject && $subject->students) {
                foreach ($subject->students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'message' => 'تم حذف الاختبار/الواجب: ' . $title,
                    ]);
                }
            }

            Log::info('تم حذف الاختبار ID: ' . $id . ' بواسطة المعلم ID: ' . Auth::id());

            return redirect()->route('teacher.assignments.index')->with('success', 'تم حذف الاختبار وإبلاغ الطلاب بنجاح.');
            
        } catch (\Exception $e) {
            Log::error('فشل في حذف الاختبار ID: ' . $id . ' | الخطأ: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ ما أثناء حذف الاختبار.');
        }
    }
}