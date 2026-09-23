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
        $lessons = Lesson::where('teacher_id', $teacher->id)->with('subject')->latest()->get();

        return view('teacher.lessons.index', compact('lessons'));
    }

    // صفحة إضافة محاضرة جديدة (تمرير مواد المعلم للواجهة)
    public function create()
    {
        $teacher = Auth::user();
        // جلب المواد الخاصة بهذا المعلم
        $subjects = $teacher->subjects; 

        return view('teacher.lessons.create', compact('subjects'));
    }

    // حفظ المحاضرة الجديدة وإرسال إشعارات للطلاب (تلقائياً إذا لم يتم تحديد المادة)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'subject_id'  => ['nullable', 'exists:subjects,id'], // أصبحت اختيارية
                'date'        => ['required', 'date'],
                'duration'    => ['nullable', 'string', 'max:100'],
                'description' => ['nullable', 'string'],
            ]);

            $teacher = Auth::user();
            
            // إذا لم يتم تحديد المادة من الواجهة، نأخذ المادة الأولى المرتبطة بهذا المعلم تلقائياً
            $subjectId = $validated['subject_id'] ?? $teacher->subjects()->value('id');

            if (!$subjectId) {
                return back()->with('error', 'عذراً، لا توجد أي مادة مرتبطة بحسابك ليتم نشر المحاضرة لها.');
            }

            $subject = Subject::findOrFail($subjectId);

            $lesson = Lesson::create([
                'subject_id'  => $subject->id,
                'teacher_id'  => $teacher->id,
                'title'       => $validated['title'],
                'lesson_date' => $validated['date'],
                'duration'    => $validated['duration'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            // إرسال إشعار لجميع طلاب المادة عند إضافة محاضرة جديدة
            $students = $subject->users()->where('role', 'student')->get(); 
            
            if ($students->isNotEmpty()) {
                foreach ($students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'title'   => 'محاضرة جديدة',
                        'message' => 'تم نشر محاضرة جديدة (' . $lesson->title . ') في مساق: ' . $subject->name,
                        'type'    => 'lesson',
                        'is_read' => false,
                    ]);
                }
            }

            Log::info('تم إضافة محاضرة جديدة بنجاح: "' . $lesson->title . '" (ID: ' . $lesson->id . ') بواسطة المعلم: ' . $teacher->name);

            return redirect()->route('teacher.lessons.index')->with('success', 'تم إضافة المحاضرة وإرسال التنبيهات للطلاب بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في إضافة محاضرة جديدة بواسطة المعلم ID: ' . Auth::id() . ' | الخطأ: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ ما أثناء إضافة المحاضرة: ' . $e->getMessage());
        }
    }

    // صفحة تعديل المحاضرة
    public function edit($id)
    {
        $teacher = Auth::user();
        $lesson = Lesson::where('id', $id)->where('teacher_id', $teacher->id)->firstOrFail();
        $subjects = $teacher->subjects;

        return view('teacher.lessons.edit', compact('lesson', 'subjects'));
    }

    // تحديث المحاضرة وإرسال إشعارات للطلاب
    public function update(Request $request, $id)
    {
        try {
            $lesson = Lesson::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();

            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'subject_id'  => ['required', 'exists:subjects,id'],
                'date'        => ['required', 'date'],
                'duration'    => ['nullable', 'string', 'max:100'],
                'description' => ['nullable', 'string'],
            ]);

            $lesson->update([
                'title'       => $validated['title'],
                'subject_id'  => $validated['subject_id'],
                'lesson_date' => $validated['date'],
                'duration'    => $validated['duration'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            // إرسال إشعار للطلاب بتعديل المحاضرة
            $subject = Subject::find($lesson->subject_id);
            if ($subject) {
                $students = $subject->users()->where('role', 'student')->get();
                foreach ($students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'title'   => 'تحديث محاضرة',
                        'message' => 'تم تحديث المحاضرة: ' . $lesson->title,
                        'type'    => 'lesson',
                        'is_read' => false,
                    ]);
                }
            }

            Log::info('تم تحديث المحاضرة: "' . $lesson->title . '" (ID: ' . $lesson->id . ') بواسطة المعلم ID: ' . Auth::id());

            return redirect()->route('teacher.lessons.index')->with('success', 'تم تحديث المحاضرة وإرسال التنبيهات للطلاب بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في تحديث المحاضرة ID: ' . $id . ' | الخطأ: ' . $e->getMessage());

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

            $subject = Subject::find($subjectId);
            $lesson->delete(); 

            // إرسال إشعار للطلاب بحذف المحاضرة
            if ($subject) {
                $students = $subject->users()->where('role', 'student')->get();
                foreach ($students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'title'   => 'حذف محاضرة',
                        'message' => 'تم حذف المحاضرة: ' . $lessonTitle,
                        'type'    => 'lesson',
                        'is_read' => false,
                    ]);
                }
            }

            Log::info('تم نقل المحاضرة للأرشيف: "' . $lessonTitle . '" (ID: ' . $id . ') بواسطة المعلم ID: ' . Auth::id());

            return redirect()->route('teacher.lessons.index')->with('success', 'تم نقل المحاضرة إلى سلة المهملات بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل في حذف المحاضرة مؤقتاً ID: ' . $id . ' | الخطأ: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ ما أثناء حذف المحاضرة.');
        }
    }

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

    // تفريغ سلة المهملات بالكامل
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