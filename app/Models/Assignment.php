<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

  protected $fillable = [
        'subject_id',
        'group_id', // أضف هذا السطر هنا
        'teacher_id',
        'title',
        'due_date',
        'duration',
        'total_marks',
        'description',
        'file_path',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * المجموعة التابع لها الواجب
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * المعلم صاحب الواجب
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    // التسليمات الخاصة بهذا الواجب
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}