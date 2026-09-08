<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        Subject::create(['name' => 'الكيمياء']);
        Subject::create(['name' => 'الفيزياء']);
        Subject::create(['name' => 'الرياضيات']);
        Subject::create(['name' => 'الأحياء']);
    }
}