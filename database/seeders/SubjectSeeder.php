<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        Subject::firstOrCreate(['name' => 'الكيمياء']);
        Subject::firstOrCreate(['name' => 'الفيزياء']);
        Subject::firstOrCreate(['name' => 'الرياضيات']);
        Subject::firstOrCreate(['name' => 'الأحياء']);
    }
}