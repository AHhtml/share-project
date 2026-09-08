<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    // صفحة عرض كل المحاضرات
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

    // حفظ المحاضرة الجديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'duration'    => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $teacher = Auth::user();
        
        // جلب أول مادة مرتبطة بالمعلم، أو جلب أول مادة متوفرة في النظام كقيمة افتراضية
        $subject = $teacher->subjects()->first() ?? \App\Models\Subject::first();

        if (!$subject) {
            return back()->with('error', 'الرجاء التأكد من إنشاء مادة دراسية واحدة على الأقل في النظام.');
        }

        Lesson::create([
            'subject_id'  => $subject->id,
            'teacher_id'  => $teacher->id,
            'title'       => $validated['title'],
            'lesson_date' => $validated['date'],
            'duration'    => $validated['duration'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('teacher.lessons.index')->with('success', 'تم إضافة المحاضرة بنجاح.');
    }

    // صفحة تعديل المحاضرة
    public function edit($id)
    {
        $lesson = Lesson::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
        return view('teacher.lessons.edit', compact('lesson'));
    }

    // تحديث المحاضرة
  // تحديث المحاضرة
    public function update(Request $request, $id)
    {
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

        return redirect()->route('teacher.lessons.index')->with('success', 'تم تحديث المحاضرة بنجاح.');
    }

    // حذف المحاضرة
    public function destroy($id)
    {
        $lesson = Lesson::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
        $lesson->delete();

        return redirect()->route('teacher.lessons.index')->with('success', 'تم حذف المحاضرة بنجاح.');
    }
}