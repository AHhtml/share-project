<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
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
        'avatar',
        'status',     // أضفناها لتدعم فلترة الحالة إن وجدت في جدولك
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isManagement(): bool { return $this->role === 'management'; }
    public function isTeacher(): bool { return $this->role === 'teacher'; }
    public function isStudent(): bool { return $this->role === 'student'; }

    /**
     * نطاق البحث والفلترة المتقدم (Query Scope for Clean Code)
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        // 1. البحث النصي (الاسم أو البريد الإلكتروني)
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        });

        // 2. الفلترة حسب الحالة (Status)
        $query->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        });

        // 3. الفلترة حسب نطاق التاريخ (تاريخ التسجيل من / إلى)
        $query->when($filters['date_from'] ?? null, function ($query, $dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        });
        
        $query->when($filters['date_to'] ?? null, function ($query, $dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        });

        // 4. الفرز والترتيب (Sorting)
        $sortBy = $filters['sort_by'] ?? 'latest';
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }
    }

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