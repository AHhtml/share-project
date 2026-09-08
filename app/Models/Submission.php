<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'solution_text',
        'solution_file',
        'grade',
        'feedback',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'grade' => 'float',
    ];

    /**
     * الواجب التابع له هذا التسليم
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * الطالب الذي قدم التسليم
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}