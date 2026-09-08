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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المجموعة (مثلاً: مجموعة أ - فيزياء)
            
            // المادة الدراسية التابعة لها المجموعة
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            
            // المعلم المسؤول عن المجموعة (مرتبط بجدول المستخدمين)
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('schedule')->nullable(); // مواعيد الشعبة (مثلاً: السبت والأربعاء 4-6)
            $table->integer('max_students')->default(30); // أقصى عدد طلاب في المجموعة
            $table->boolean('is_active')->default(true); // حالة المجموعة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};