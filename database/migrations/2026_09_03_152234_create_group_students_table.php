<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_student', function (Blueprint $table) {
            $table->id();
            
            // المجموعة الدراسية
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            
            // الطالب التابع للمجموعة (مرتبط بجدول users)
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            
            // حالة الطالب في المجموعة (مستمر، متوقف...)
            $table->enum('status', ['active', 'suspended', 'completed'])->default('active');
            
            // تاريخ الفرز/الانضمام للمجموعة
            $table->timestamp('joined_at')->useCurrent();
            
            $table->timestamps();

            // منع تكرار إضافة نفس الطالب في نفس المجموعة أكثر من مرة
            $table->unique(['group_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_student');
    }
};