<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'branch',
        'role',
        'subject_id', // إضافة هذا الحقل ليكون قابل للتعبئة
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isManagement(): bool { return $this->role === 'management'; }
    public function isTeacher(): bool { return $this->role === 'teacher'; }
    public function isStudent(): bool { return $this->role === 'student'; }

    // المجموعات التي ينتمي إليها الطالب
    public function studentGroups()
    {
        return $this->belongsToMany(Group::class, 'group_student', 'student_id', 'group_id')
                    ->withPivot('status', 'joined_at')
                    ->withTimestamps();
    }

    // جميع تسليمات الطالب للواجبات
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    // جلب المواد التي يدرسها هذا المعلم
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_user', 'user_id', 'subject_id');
    }

    // المواد التي ينتمي إليها الطالب
    public function enrolledSubjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_user', 'user_id', 'subject_id');
    }
    
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function customNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function userNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}