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
        
        // Redirect ke dashboard yang tepat berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
    
    public function adminDashboard()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized. You do not have permission to access this resource.');
        }
        
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
    }
    
    public function userDashboard()
    {
        $user = Auth::user();
        
        if ($user->role !== 'user') {
            abort(403, 'Unauthorized. You do not have permission to access this resource.');
        }
        
        return view('user.dashboard');
    }
}