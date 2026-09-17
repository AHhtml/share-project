<?php
namespace App\Observers;

use App\Models\Assignment;
use Illuminate\Support\Facades\Storage;

class AssignmentObserver
{
    public function deleting(Assignment $assignment)
    {
        if ($assignment->file && Storage::disk('public')->exists($assignment->file)) {
            Storage::disk('public')->delete($assignment->file);
        }
    }

    public function updating(Assignment $assignment)
    {
        if ($assignment->isDirty('file')) {
            $oldFile = $assignment->getOriginal('file');
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }
        }
    }
}