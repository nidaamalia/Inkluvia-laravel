<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use Illuminate\Support\Facades\Mail;
use App\Mail\InstitutionLoginKeyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LembagaController extends Controller
{
    public function index(Request $request)
    {
        $query = Lembaga::withCount('users');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $lembagas = $query->paginate(10)->withQueryString();
        
        return view('admin.manajemen-lembaga.index', compact('lembagas'));
    }
    
    public function create()
    {
        return view('admin.manajemen-lembaga.create');
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'alamat' => 'required|string|max:500',
            'email' => 'nullable|email|max:150',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $loginKey = bin2hex(random_bytes(16));

        $lembaga = Lembaga::create([
            'nama' => $request->nama,
            'type' => $request->type,
            'alamat' => $request->alamat,
            'deskripsi' => $request->deskripsi,
            'email' => $request->email,
            'login_key' => $loginKey,
        ]);

        if ($request->boolean('send_key') && $lembaga->email) {
            Mail::to($lembaga->email)->send(new InstitutionLoginKeyMail($lembaga->nama, $loginKey));
        }

        return redirect()->route('admin.manajemen-lembaga')
            ->with('success', 'Lembaga berhasil ditambahkan!');
    }
    
    public function edit(Lembaga $lembaga)
    {
        return view('admin.manajemen-lembaga.edit', compact('lembaga'));
    }
    
    public function update(Request $request, Lembaga $lembaga)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'alamat' => 'required|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $lembaga->update([
            'nama' => $request->nama,
            'type' => $request->type,
            'alamat' => $request->alamat,
        ]);
        
        return redirect()->route('admin.manajemen-lembaga')
            ->with('success', 'Lembaga berhasil diperbarui!');
    }
    
    public function destroy(Lembaga $lembaga)
    {
        if ($lembaga->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus lembaga yang masih memiliki pengguna!');
        }
        
        $lembaga->delete();
        
        return redirect()->route('admin.manajemen-lembaga')
            ->with('success', 'Lembaga berhasil dihapus!');
    }
}