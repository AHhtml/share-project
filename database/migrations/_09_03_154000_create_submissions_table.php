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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            
            // الواجب المرتبط به التسليم
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            
            // الطالب صاحب التسليم
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            
            $table->text('solution_text')->nullable(); // نص الحل إذا كان كتابياً
            $table->string('solution_file')->nullable(); // ملف الحل المرفق من الطالب (PDF / صورة)
            
            $table->decimal('grade', 5, 2)->nullable(); // الدرجة التي وضعها الأستاذ
            $table->text('feedback')->nullable(); // ملاحظات المعلم للطالب
            
            $table->timestamp('submitted_at')->useCurrent(); // وقت التسليم
            $table->timestamps();

            // ضمان أن كل طالب يملك تسليماً واحداً فقط للواجب الواحد
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};