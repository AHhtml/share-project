<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'teacher_id'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
    // الطلاب المسجلين في هذه المادة
public function students()
{
    return $this->belongsToMany(User::class, 'subject_user', 'subject_id', 'user_id');
}
public function assignments()
{
    return $this->hasMany(Assignment::class); // أو Exam::class حسب اسم الموديل عندك
}
public function users()
{
    return $this->belongsToMany(User::class, 'subject_user', 'subject_id', 'user_id'); 
    // ملاحظة: قم بتعديل اسم الجدول الوسيط (Pivot Table) وأسماء الأعمدة بما يطابق قاعدة البيانات لديك إذا كانت مختلفة
}
}