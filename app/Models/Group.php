<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject_id',
        'teacher_id',
        'schedule',
        'max_students',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع المادة الدراسية
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * العلاقة مع المعلم
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function students()
    {
        return $this->belongsToMany(User::class, 'group_student', 'group_id', 'student_id')
                    ->withPivot('status', 'joined_at')
                    ->withTimestamps();
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order', 'asc');
    }
    // الواجبات الخاصة بهذه المجموعة
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}