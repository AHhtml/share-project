namespace App\Observers;

use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

class LessonObserver
{
    /**
     * Handle the Lesson "deleting" event.
     * (يحذف ملف المحاضرة من الـ Storage فوراً عند حذف المحاضرة من القاعدة)
     */
    public function deleting(Lesson $lesson)
    {
        // استبدل 'video' باسم حقل الملف أو الفيديو في جدول المحاضرات لديك
        if ($lesson->video && Storage::disk('public')->exists($lesson->video)) {
            Storage::disk('public')->delete($lesson->video);
        }
    }

    /**
     * Handle the Lesson "updating" event.
     * (يحذف الملف القديم تلقائياً إذا قام المعلم برفع فيديو أو ملف جديد أثناء التعديل)
     */
    public function updating(Lesson $lesson)
    {
        if ($lesson->isDirty('video')) {
            $oldVideo = $lesson->getOriginal('video');
            if ($oldVideo && Storage::disk('public')->exists($oldVideo)) {
                Storage::disk('public')->delete($oldVideo);
            }
        }
    }
}