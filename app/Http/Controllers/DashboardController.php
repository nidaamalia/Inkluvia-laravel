<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin dashboard dengan statistik
            $stats = [
                'total_users' => User::count(),
                'total_lembagas' => Lembaga::count(),
                'active_devices' => 0, 
                'total_materials' => 0, 
                'users_by_role' => User::selectRaw('role, count(*) as count')
                    ->groupBy('role')
                    ->pluck('count', 'role')
                    ->toArray(),
                'recent_users' => User::with('lembaga')
                    ->latest()
                    ->take(5)
                    ->get()
            ];
            
            return view('admin.dashboard', compact('stats'));
        } else {
            return view('user.dashboard');
        }
    }
}