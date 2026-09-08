<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Lesson;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $studentsCount = User::where('role', 'student')->count();
        $teachersCount = User::where('role', 'teacher')->count();
        $managementCount = User::where('role', 'management')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'studentsCount',
            'teachersCount',
            'managementCount'
        ));
    }
}