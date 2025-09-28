<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Device;
use App\Models\Material;
use App\Models\MaterialPage;
use App\Models\BraillePattern;
use App\Services\MqttService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    protected $mqttService;

    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    public function index(Request $request)
    {
        $query = Jadwal::where('user_id', Auth::id())->with('user');
        
        // Filter by date
        if ($request->filled('filter_tanggal')) {
            if ($request->filter_tanggal === 'hari_ini') {
                $query->whereDate('tanggal', today());
            }
            // 'semua' = no date filter
        }
        
        // Filter by status
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }
        
        // Sort: upcoming first (by date & time), then completed (newest first)
        $jadwals = $query->get()->sortBy(function($jadwal) {
            if ($jadwal->status === 'selesai') {
                // Completed: sort by date DESC (newest first), add large timestamp
                return $jadwal->tanggal->timestamp + 999999999;
            } else {
                // Active/upcoming: sort by date & time ASC (nearest first)
                $datetime = \Carbon\Carbon::parse($jadwal->tanggal->format('Y-m-d') . ' ' . $jadwal->waktu_mulai->format('H:i:s'));
                return $datetime->timestamp;
            }
        })->values();

        return view('user.jadwal-belajar.index', compact('jadwals'));
    }

    public function create()
    {
        // Ambil materi yang dapat diakses oleh user berdasarkan aturan akses
        $materials = Material::published()
            ->accessibleBy(Auth::user())
            ->orderBy('judul')
            ->pluck('judul', 'judul')
            ->toArray();

        return view('user.jadwal-belajar.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'material_id' => 'required|exists:materials,id',
            'pengulangan' => 'required|in:tidak,harian,mingguan',
        ]);

        // Verify that the material is in user's saved list
        $material = Material::findOrFail($validated['material_id']);
        $isSaved = UserSavedMaterial::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->exists();
            
        if (!$isSaved) {
            return back()->withErrors(['material_id' => 'Anda hanya dapat membuat jadwal dari materi yang tersimpan.']);
        }

        // Generate judul dari material
        $validated['judul'] = $material->judul;
        $validated['materi'] = $material->judul; // Keep compatibility
        $validated['user_id'] = Auth::id();
        
        // Remove material_id as it's not in the table
        unset($validated['material_id']);

        Jadwal::create($validated);

        return redirect()->route('user.jadwal-belajar')
            ->with('success', 'Jadwal berhasil dibuat!');
    }

    public function edit(Jadwal $jadwal)
    {
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            abort(403);
        }

        // Ambil materi yang dapat diakses oleh user berdasarkan aturan akses
        $materials = Material::published()
            ->accessibleBy(Auth::user())
            ->orderBy('judul')
            ->pluck('judul', 'judul')
            ->toArray();

        return view('user.jadwal-belajar.edit', compact('jadwal', 'savedMaterials'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'material_id' => 'required|exists:materials,id',
            'pengulangan' => 'required|in:tidak,harian,mingguan',
        ]);

        // Verify that the material is in user's saved list
        $material = Material::findOrFail($validated['material_id']);
        $isSaved = UserSavedMaterial::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->exists();
            
        if (!$isSaved) {
            return back()->withErrors(['material_id' => 'Anda hanya dapat membuat jadwal dari materi yang tersimpan.']);
        }

        // Update judul dari material
        $validated['judul'] = $material->judul;
        $validated['materi'] = $material->judul; // Keep compatibility
        
        // Remove material_id as it's not in the table
        unset($validated['material_id']);

        $jadwal->update($validated);

        return redirect()->route('user.jadwal-belajar')
            ->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Jadwal $jadwal)
    {
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            abort(403);
        }

        $jadwal->delete();

        return redirect()->route('user.jadwal-belajar')
            ->with('success', 'Jadwal berhasil dihapus!');
    }

    public function startSession(Jadwal $jadwal)
    {
        // Get available devices for this user's lembaga
        $devices = Device::where('status', 'aktif')
            ->where(function($query) {
                $query->where('lembaga_id', Auth::user()->lembaga_id)
                      ->orWhere('user_id', Auth::id());
            })
            ->get();

        return view('user.jadwal-belajar.select-device', compact('jadwal', 'devices'));
    }

    public function sendToDevices(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'devices' => 'required|array',
            'devices.*' => 'exists:devices,id'
        ]);

        $devices = Device::whereIn('id', $validated['devices'])->get();

        // Send material to selected devices via MQTT
        foreach ($devices as $device) {
            try {
                $this->mqttService->sendMaterial($device->serial_number, [
                    'jadwal_id' => $jadwal->id,
                    'judul' => $jadwal->judul,
                    'materi' => $jadwal->materi,
                    'user' => Auth::user()->nama_lengkap,
                    'timestamp' => now()->toISOString()
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send material to device: ' . $e->getMessage());
            }
        }

        // Update jadwal status
        $jadwal->update(['status' => 'sedang_berlangsung']);

        return redirect()->route('user.jadwal-belajar.learn', [
            'jadwal' => $jadwal->id,
            'page' => 1,
            'line' => 1
        ])->with('success', 'Materi berhasil dikirim ke perangkat!');
    }

    public function learn(Jadwal $jadwal, Request $request)
    {
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            abort(403);
        }

        // Cari material berdasarkan judul dari jadwal
        $material = Material::where('judul', $jadwal->materi)
            ->published()
            ->accessibleBy(Auth::user())
            ->first();

        if (!$material) {
            return redirect()->route('user.jadwal-belajar')
                ->with('error', 'Materi tidak ditemukan atau tidak dapat diakses.');
        }

        // Get page number and line index from request (default to 1)
        $pageNumber = $request->get('page', 1);
        $lineIndex = $request->get('line', 1) - 1; // Convert to 0-based index

        // Get material page data and total pages in one query
        $materialPage = MaterialPage::where('material_id', $material->id)
            ->where('page_number', $pageNumber)
            ->first();

        $totalPages = MaterialPage::where('material_id', $material->id)
            ->max('page_number') ?? 1;

        // Prepare data for view
        if ($materialPage && $materialPage->lines && !empty($materialPage->lines)) {
            $lines = $materialPage->lines;
            $totalLines = count($lines);
            $currentLineIndex = ($lineIndex >= 0 && $lineIndex < $totalLines) ? $lineIndex : 0;
            $currentLineText = $lines[$currentLineIndex] ?? '';
        } else {
            // No material page data found
            $lines = [];
            $totalLines = 0;
            $currentLineIndex = 0;
            $currentLineText = '';
        }

        // Get braille patterns for current line characters
        $braillePatterns = [];
        $brailleBinaryPatterns = [];
        $brailleDecimalPatterns = [];

        if (!empty($currentLineText)) {
            $characters = str_split($currentLineText);
            foreach ($characters as $char) {
                if ($char === ' ') {
                    // Space handled locally without DB lookup
                    $braillePatterns[$char] = '⠀';
                    $brailleBinaryPatterns[$char] = '000000';
                    $brailleDecimalPatterns[$char] = 0;
                    continue;
                }

                $pattern = BraillePattern::getByCharacter($char);
                $braillePatterns[$char] = $pattern ? $pattern->braille_unicode : '⠀';
                $brailleBinaryPatterns[$char] = $pattern ? $pattern->dots_binary : '000000';
                $brailleDecimalPatterns[$char] = $pattern ? $pattern->dots_decimal : 0;
            }
        }

        return view('user.jadwal-belajar.learn', compact(
            'jadwal', 
            'material', 
            'pageNumber', 
            'totalPages',
            'lines',
            'totalLines',
            'currentLineIndex',
            'currentLineText',
            'braillePatterns',
            'brailleBinaryPatterns',
            'brailleDecimalPatterns'
        ));
    }

    /**
     * Method helper untuk mendapatkan materi yang dapat diakses user
     * Digunakan untuk debugging dan testing
     */
    public function getAccessibleMaterials()
    {
        $user = Auth::user();
        $materials = Material::published()
            ->accessibleBy($user)
            ->with('creator.lembaga')
            ->get();

        return response()->json([
            'user_id' => $user->id,
            'user_lembaga_id' => $user->lembaga_id,
            'user_lembaga_type' => $user->lembaga ? $user->lembaga->type : null,
            'accessible_materials' => $materials->map(function($material) {
                return [
                    'id' => $material->id,
                    'judul' => $material->judul,
                    'akses' => $material->akses,
                    'creator_id' => $material->created_by,
                    'creator_lembaga_id' => $material->creator->lembaga_id ?? null,
                    'creator_lembaga_type' => $material->creator->lembaga->type ?? null,
                ];
            })
        ]);
    }

    /**
     * Navigate to specific page of material
     */
    public function navigatePage(Jadwal $jadwal, Request $request)
    {
        $page = $request->get('page', 1);
        $line = $request->get('line', 1);
        
        return redirect()->route('user.jadwal-belajar.learn', [
            'jadwal' => $jadwal->id,
            'page' => $page,
            'line' => $line
        ]);
    }

    /**
     * Get material page data via AJAX
     */
    public function getMaterialPage(Jadwal $jadwal, Request $request)
    {
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $pageNumber = $request->get('page', 1);
        $lineIndex = $request->get('line', 1) - 1; // Convert to 0-based index

        // Cari material berdasarkan judul dari jadwal
        $material = Material::where('judul', $jadwal->materi)
            ->published()
            ->accessibleBy(Auth::user())
            ->first();

        if (!$material) {
            return response()->json(['error' => 'Materi tidak ditemukan'], 404);
        }

        // Get material page data
        $materialPage = MaterialPage::where('material_id', $material->id)
            ->where('page_number', $pageNumber)
            ->first();

        if (!$materialPage) {
            return response()->json(['error' => 'Halaman tidak ditemukan'], 404);
        }

        // Get total pages
        $totalPages = MaterialPage::where('material_id', $material->id)
            ->max('page_number') ?? 1;

        $lines = $materialPage->lines;
        $totalLines = count($lines);
        $currentLineIndex = ($lineIndex >= 0 && $lineIndex < $totalLines) ? $lineIndex : 0;
        $currentLineText = $lines[$currentLineIndex] ?? '';

        return response()->json([
            'success' => true,
            'data' => [
                'current_page' => $pageNumber,
                'current_line_index' => $currentLineIndex,
                'total_pages' => $totalPages,
                'total_lines' => $totalLines,
                'lines' => $lines,
                'material_title' => $material->judul,
                'material_description' => $material->deskripsi,
                'current_line_text' => $currentLineText,
                'has_previous' => $pageNumber > 1,
                'has_next' => $pageNumber < $totalPages
            ]
        ]);
    }
}