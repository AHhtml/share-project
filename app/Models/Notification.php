<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications'; // تحديد اسم الجدول صراحة لمنع أي خطأ في الربط

    protected $fillable = ['user_id', 'message'];
}