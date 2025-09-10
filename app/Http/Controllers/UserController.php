<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('lembaga');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        // Filter by lembaga
        if ($request->has('lembaga') && $request->lembaga) {
            $query->where('lembaga_id', $request->lembaga);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $users = $query->paginate(10)->withQueryString();
        $lembagas = Lembaga::all();
        
        return view('admin.manajemen-pengguna.index', compact('users', 'lembagas'));
    }
    
    public function create()
    {
        $lembagas = Lembaga::all();
        return view('admin.manajemen-pengguna.create', compact('lembagas'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'lembaga_id' => 'required|exists:lembagas,id',
            'role' => 'required|in:admin,user'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        User::create([
            'name' => $request->name,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'lembaga_id' => $request->lembaga_id,
            'role' => $request->role,
        ]);
        
        return redirect()->route('admin.kelola-pengguna')
            ->with('success', 'Pengguna berhasil ditambahkan!');
    }
    
    public function edit(User $user)
    {
        $lembagas = Lembaga::all();
        return view('admin.manajemen-pengguna.edit', compact('user', 'lembagas'));
    }
    
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'lembaga_id' => 'required|exists:lembagas,id',
            'role' => 'required|in:admin,user'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $updateData = [
            'name' => $request->name,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'lembaga_id' => $request->lembaga_id,
            'role' => $request->role,
        ];
        
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        $user->update($updateData);
        
        return redirect()->route('admin.kelola-pengguna')
            ->with('success', 'Pengguna berhasil diperbarui!');
    }
    
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }
        
        $user->delete();
        
        return redirect()->route('admin.kelola-pengguna')
            ->with('success', 'Pengguna berhasil dihapus!');
    }
}