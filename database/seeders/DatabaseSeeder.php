<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. تشغيل سيدر المواد أولاً لتوليد المواد في قاعدة البيانات
        $this->call([
            SubjectSeeder::class,
        ]);

        // إنشاء حساب مدير النظام (Management)
        User::firstOrCreate(
            ['email' => 'management@manara.edu'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password123'),
                'role' => 'management',
                'phone' => '0590000000',
            ]
        );

        // جلب مواد الفيزياء والكيمياء المحددة
        $physicsSubject = Subject::where('name', 'الفيزياء')->first();
        $chemistrySubject = Subject::where('name', 'الكيمياء')->first();

        // 2. إنشاء حساب معلم الفيزياء
        $physicsTeacher = User::firstOrCreate(
            ['email' => 'physics.teacher@manara.edu'],
            [
                'name' => 'الأستاذ أحمد (فيزياء)',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'phone' => '0591111111',
                'subject_id' => $physicsSubject ? $physicsSubject->id : null,
            ]
        );
        if ($physicsSubject) {
            $physicsTeacher->subjects()->syncWithoutDetaching([$physicsSubject->id]);
        }

        // إنشاء حساب معلم الكيمياء
        $chemistryTeacher = User::firstOrCreate(
            ['email' => 'chemistry.teacher@manara.edu'],
            [
                'name' => 'الأستاذ محمود (كيمياء)',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'phone' => '0593333333',
                'subject_id' => $chemistrySubject ? $chemistrySubject->id : null,
            ]
        );
        if ($chemistrySubject) {
            $chemistryTeacher->subjects()->syncWithoutDetaching([$chemistrySubject->id]);
        }

        // 3. إنشاء حساب طالب (Student) وربطه بمادة الفيزياء كافتراضي
        $defaultSubject = $physicsSubject ?? Subject::first();
        User::firstOrCreate(
            ['email' => 'student@manara.edu'],
            [
                'name' => 'الطالب محمد',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'phone' => '0592222222',
                'subject_id' => $defaultSubject ? $defaultSubject->id : null,
            ]
        );

        // مستخدم تجريبي إضافي (اختياري)
        // User::firstOrCreate(
        //     ['email' => 'test@example.com'],
        //     [
        //         'name' => 'Test User',
        //         'password' => Hash::make('password123'),
        //         'role' => 'student',
        //     ]
        // );
    }
}