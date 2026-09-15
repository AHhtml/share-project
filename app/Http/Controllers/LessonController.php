<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LessonController extends Controller
{
    // صفحة عرض كل المحاضرات النشطة
    public function index()
    {
        $teacher = Auth::user();
        $lessons = Lesson::where('teacher_id', $teacher->id)->latest()->get();

        return view('teacher.lessons.index', compact('lessons'));
    }

    // صفحة إضافة محاضرة جديدة
    public function create()
    {
        return view('teacher.lessons.create');
    }

    // حفظ المحاضرة الجديدة وإرسال إشعارات للطلاب
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'date'        => ['required', 'date'],
                'duration'    => ['nullable', 'string', 'max:100'],
                'description' => ['nullable', 'string'],
            ]);

            $teacher = Auth::user();
            
            // جلب أول مادة مرتبطة بالعالم، أو جلب أول مادة متوفرة في النظام كقيمة افتراضية
            $subject = $teacher->subjects()->first() ?? Subject::first();

            if (!$subject) {
                return back()->with('error', 'الرجاء التأكد من إنشاء مادة دراسية واحدة على الأقل في النظام.');
            }

            $lesson = Lesson::create([
                'subject_id'  => $subject->id,
                'teacher_id'  => $teacher->id,
                'title'       => $validated['title'],
                'lesson_date' => $validated['date'],
                'duration'    => $validated['duration'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            // إرسال إشعار لجميع طلاب المادة عند إضافة محاضرة جديدة
            $subjectWithStudents = Subject::with('students')->find($subject->id);
            if ($subjectWithStudents && $subjectWithStudents->students) {
                foreach ($subjectWithStudents->students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'message' => 'تم نشر محاضرة جديدة: ' . $lesson->title,
                    ]);
                }
            }

            Log::info('تم إضافة محاضرة جديدة بنجاح: "' . $lesson->title . '" (ID: ' . $lesson->id . ') بواسطة المعلم: ' . $teacher->name . ' (ID: ' . $teacher->id . ')');

            return redirect()->route('teacher.lessons.index')->with('success', 'تم إضافة المحاضرة وإرسال التنبيهات للطلاب بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في إضافة محاضرة جديدة بواسطة المعلم ID: ' . Auth::id() . ' | الخطأ: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ ما أثناء إضافة المحاضرة.');
        }
    }

    // صفحة تعديل المحاضرة
    public function edit($id)
    {
        $lesson = Lesson::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
        return view('teacher.lessons.edit', compact('lesson'));
    }

    // تحديث المحاضرة وإرسال إشعارات للطلاب
    public function update(Request $request, $id)
    {
        try {
            $lesson = Lesson::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();

            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'date'        => ['required', 'date'],
                'duration'    => ['nullable', 'string', 'max:100'],
                'description' => ['nullable', 'string'],
            ]);

            // تحديث البيانات مع مطابقة اسم العمود في قاعدة البيانات (lesson_date)
            $lesson->update([
                'title'       => $validated['title'],
                'lesson_date' => $validated['date'],
                'duration'    => $validated['duration'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            // إرسال إشعار للطلاب بتعديل المحاضرة
            $subject = Subject::with('students')->find($lesson->subject_id);
            if ($subject && $subject->students) {
                foreach ($subject->students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'message' => 'تم تحديث المحاضرة: ' . $lesson->title,
                    ]);
                }
            }

            Log::info('تم تحديث المحاضرة: "' . $lesson->title . '" (ID: ' . $lesson->id . ') بواسطة المعلم ID: ' . Auth::id());

            return redirect()->route('teacher.lessons.index')->with('success', 'تم تحديث المحاضرة وإرسال التنبيهات للطلاب بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في تحديث المحاضرة ID: ' . $id . ' بواسطة المعلم ID: ' . Auth::id() . ' | الخطأ: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ ما أثناء تحديث المحاضرة.');
        }
    }

    // حذف المحاضرة مؤقتاً (Soft Delete) وإرسال إشعارات للطلاب
    public function destroy($id)
    {
        try {
            $lesson = Lesson::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
            $lessonTitle = $lesson->title;
            $subjectId = $lesson->subject_id;

            // جلب الطلاب المرتبطين بالمادة قبل الحذف
            $subject = Subject::with('students')->find($subjectId);

            $lesson->delete(); // سيقوم بالحذف المؤقت Soft Delete بفضل الـ Trait

            // إرسال إشعار للطلاب بحذف المحاضرة
            if ($subject && $subject->students) {
                foreach ($subject->students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'message' => 'تم حذف المحاضرة: ' . $lessonTitle,
                    ]);
                }
            }

            Log::info('تم نقل المحاضرة للأرشيف (حذف مؤقت): "' . $lessonTitle . '" (ID: ' . $id . ') بواسطة المعلم ID: ' . Auth::id());

            return redirect()->route('teacher.lessons.index')->with('success', 'تم نقل المحاضرة إلى سلة المهملات بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في حذف المحاضرة مؤقتاً ID: ' . $id . ' بواسطة المعلم ID: ' . Auth::id() . ' | الخطأ: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ ما أثناء حذف المحاضرة.');
        }
    }

    // ==========================================
    // دوال سلة المهملات والأرشيف (المضاف حديثاً)
    // ==========================================

    // عرض أرشيف المحاضرات المحذوفة خاصة بالمعلم الحالي
    public function trash()
    {
        $teacherId = Auth::id();
        $trashedLessons = Lesson::onlyTrashed()
            ->where('teacher_id', $teacherId)
            ->with('subject')
            ->latest()
            ->get();

        return view('teacher.lessons.trash', compact('trashedLessons'));
    }

    // استعادة المحاضرة من سلة المهملات
    public function restore($id)
    {
        try {
            $lesson = Lesson::onlyTrashed()
                ->where('id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();

            $lesson->restore();

            return redirect()->route('teacher.lessons.trash')->with('success', 'تمت استعادة المحاضرة بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء محاولة استعادة المحاضرة.');
        }
    }

    // الحذف النهائي للمحاضرة من قاعدة البيانات
    public function forceDelete($id)
    {
        try {
            $lesson = Lesson::onlyTrashed()
                ->where('id', $id)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();

            $lesson->forceDelete();

            return redirect()->route('teacher.lessons.trash')->with('success', 'تم حذف المحاضرة نهائياً من النظام.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء الحذف النهائي للمحاضرة.');
        }
    }
    // تفريغ سلة المهملات بالكامل (حذف نهائي لكل محاضرات المعلم المحذوفة)
  public function emptyTrash()
{
    try {
        $teacherId = Auth::id();
        Lesson::onlyTrashed()->where('teacher_id', $teacherId)->forceDelete();
        return redirect()->route('teacher.lessons.trash')->with('success', 'تم تفريغ سلة المهملات نهائياً.');
    } catch (\Exception $e) {
        return back()->with('error', 'حدث خطأ ما.');
    }
}
}