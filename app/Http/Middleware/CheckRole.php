<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        return match ($user->role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'management' => redirect()->route('management.dashboard'),
            'teacher'    => redirect()->route('teacher.dashboard'),
            'student'    => redirect()->route('student.dashboard'),
            default      => redirect()->route('login')->withErrors(['error' => 'غير مصرح لك بالوصول لهذه الصفحة.']),
        };
    }
}