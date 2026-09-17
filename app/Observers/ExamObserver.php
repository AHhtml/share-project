<?php

namespace App\Observers;

use App\Models\Exam;
use Illuminate\Support\Facades\Storage;

class ExamObserver
{
    /**
     * Handle the Exam "deleting" event.
     * (حذف الملف من الـ Storage تلقائياً بمجرد حذف سجل الاختبار من قاعدة البيانات)
     */
    public function deleting(Exam $exam)
    {
        if ($exam->file && Storage::disk('public')->exists($exam->file)) {
            Storage::disk('public')->delete($exam->file);
        }
    }

    /**
     * Handle the Exam "updating" event.
     * (اختياري للتميز: حذف الملف القديم تلقائياً إذا تم رفع ملف جديد أثناء تعديل الاختبار)
     */
    public function updating(Exam $exam)
    {
        if ($exam->isDirty('file')) {
            $oldFile = $exam->getOriginal('file');
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }
        }
    }
}