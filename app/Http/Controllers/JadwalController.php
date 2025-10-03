<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Device;
use App\Models\Material;
use App\Models\UserSavedMaterial;
use App\Services\MqttService;
use App\Services\MaterialSessionService;
use App\Services\DeviceButtonChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    protected $mqttService;
    protected $materialSessionService;

    public function __construct(MqttService $mqttService, MaterialSessionService $materialSessionService)
    {
        $this->mqttService = $mqttService;
        $this->materialSessionService = $materialSessionService;
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
        $savedMaterialIds = UserSavedMaterial::where('user_id', Auth::id())
            ->pluck('material_id');

        $savedMaterials = Material::published()
            ->accessibleBy(Auth::user())
            ->whereIn('id', $savedMaterialIds)
            ->orderBy('judul')
            ->get();

        return view('user.jadwal-belajar.create', compact('savedMaterials'));
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

        $savedMaterialIds = UserSavedMaterial::where('user_id', Auth::id())
            ->pluck('material_id');

        $savedMaterials = Material::published()
            ->accessibleBy(Auth::user())
            ->whereIn('id', $savedMaterialIds)
            ->orderBy('judul')
            ->get();

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
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            abort(403);
        }

        // Always update status to 'sedang_berlangsung' regardless of current status
        $jadwal->update([
            'status' => 'sedang_berlangsung'
        ]);

        // Get available devices for this user's lembaga
        $devices = Device::where('status', 'aktif')
            ->where(function($query) {
                $query->where('lembaga_id', Auth::user()->lembaga_id)
                      ->orWhere('user_id', Auth::id());
            })
            ->get();

        $material = $jadwal->material ?? Material::where('judul', $jadwal->materi)
            ->published()
            ->accessibleBy(Auth::user())
            ->first();

        return view('user.jadwal-belajar.select-device', [
            'sessionTitle' => $jadwal->judul,
            'sessionBackRoute' => route('user.jadwal-belajar'),
            'sessionBackLabel' => 'Kembali ke Jadwal',
            'sessionSubmitRoute' => route('user.jadwal-belajar.send', $jadwal),
            'sessionType' => 'jadwal',
            'jadwal' => $jadwal,
            'devices' => $devices,
            'material' => $material,
            'preselectedDevices' => $jadwal->devices()->pluck('devices.id')->all(),
        ]);
    }

    public function sendToDevices(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'devices' => 'required|array',
            'devices.*' => 'exists:devices,id'
        ]);

        $devices = Device::whereIn('id', $validated['devices'])->get();
        $deviceIds = $devices->pluck('id')->toArray();

        // Persist selected devices for subsequent learning session
        $jadwal->devices()->sync($deviceIds);
        $characterCapacity = $this->materialSessionService->resolveCharacterCapacity($deviceIds);

        // Prepare initial chunk data
        $currentChunkText = '';
        $currentChunkDecimalValues = [];
        $currentChunkDecimal = '';
        $pageNumber = 1;
        $lineNumber = 1;
        $chunkNumber = 1;

        $material = $jadwal->material ?? Material::where('judul', $jadwal->materi)
            ->published()
            ->accessibleBy(Auth::user())
            ->first();

        if ($material) {
            $state = $this->materialSessionService->getInitialState($material, $characterCapacity);
            $pageNumber = $state['pageNumber'];
            $lineNumber = max(1, $state['currentLineIndex'] + 1);
            $chunkNumber = max(1, $state['currentChunkIndex'] + 1);
            $currentChunkText = $state['currentChunkText'];
            $currentChunkDecimalValues = $state['currentChunkDecimalValues'];
            $currentChunkDecimal = $state['currentChunkDecimal'];
        }

        // Send material to selected devices via MQTT
        foreach ($devices as $device) {
            try {
                $this->mqttService->sendMaterial($device->serial_number, [
                    'jadwal_id' => $jadwal->id,
                    'judul' => $jadwal->judul,
                    'materi' => $jadwal->materi,
                    'user' => Auth::user()->nama_lengkap,
                    'character_capacity' => $characterCapacity,
                    'page_number' => $pageNumber,
                    'line_number' => $lineNumber,
                    'chunk_number' => $chunkNumber,
                    'current_chunk_text' => $currentChunkText,
                    'current_chunk_decimal_values' => $currentChunkDecimalValues,
                    'current_chunk_decimal' => $currentChunkDecimal,
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

    /**
     * Display the learning page with content formatted for the selected devices
     *
     * @param Jadwal $jadwal
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function learn(Jadwal $jadwal, Request $request)
    {
        // Check authorization
        if ($jadwal->user_id !== Auth::id()) {
            abort(403);
        }

        // Get material based on schedule
        $material = $jadwal->material ?? Material::where('judul', $jadwal->materi)
            ->published()
            ->accessibleBy(Auth::user())
            ->first();

        if (!$material) {
            return redirect()->route('user.jadwal-belajar')
                ->with('error', 'Materi tidak ditemukan atau tidak dapat diakses.');
        }

        $jadwal->load('devices');

        $pageParam = (int) $request->get('page', 1);
        $lineParam = $request->get('line', 1);
        $chunkParam = $request->get('chunk', 1);

        $deviceIds = $jadwal->devices->pluck('id')->toArray();
        $deviceSerials = $jadwal->devices->pluck('serial_number')->toArray();
        $characterCapacity = $this->materialSessionService->resolveCharacterCapacity($deviceIds);

        $buttonTopic = null;
        if (count($deviceSerials) === 1) {
            $buttonTopic = DeviceButtonChannel::topicForSerial($deviceSerials[0]);
        }

        $state = $this->materialSessionService->composeState(
            $material,
            $pageParam,
            $lineParam,
            $chunkParam,
            $characterCapacity
        );

        $viewData = [
            'jadwal' => $jadwal,
            'material' => $material,
            'pageNumber' => $state['pageNumber'],
            'currentPage' => $state['pageNumber'],
            'totalPages' => $state['totalPages'],
            'currentLine' => $state['totalLines'] > 0 ? $state['currentLineIndex'] + 1 : 0,
            'currentLineIndex' => $state['currentLineIndex'],
            'currentChunk' => $state['totalChunks'] > 0 ? $state['currentChunkIndex'] + 1 : 0,
            'totalLines' => $state['totalLines'],
            'totalChunks' => $state['totalChunks'],
            'currentLineText' => $state['currentLineText'],
            'currentChunkText' => $state['currentChunkText'],
            'currentChunkDecimal' => $state['currentChunkDecimal'],
            'currentChunkDecimalValues' => $state['currentChunkDecimalValues'],
            'characterCapacity' => $state['characterCapacity'],
            'braillePatterns' => $state['braillePatterns'],
            'brailleBinaryPatterns' => $state['brailleBinaryPatterns'],
            'brailleDecimalPatterns' => $state['brailleDecimalPatterns'],
            'hasNextChunk' => $state['hasNextChunk'],
            'hasNextLine' => $state['hasNextLine'],
            'hasPrevious' => $state['hasPrevious'],
            'deviceCount' => count($deviceIds),
            'lines' => $state['originalLines'],
            'originalLines' => $state['originalLines'],
            'selectedDeviceIds' => $deviceIds,
            'selectedDeviceSerials' => $deviceSerials,
            'learnRouteName' => 'user.jadwal-belajar.learn',
            'learnRouteParams' => ['jadwal' => $jadwal->id],
            'navigateRouteName' => 'user.jadwal-belajar.navigate',
            'navigateRouteParams' => ['jadwal' => $jadwal->id],
            'materialPageRouteName' => 'user.jadwal-belajar.material-page',
            'materialPageParams' => ['jadwal' => $jadwal->id],
            'sessionTitle' => $jadwal->judul,
            'sessionBackRoute' => route('user.jadwal-belajar'),
            'sessionBackLabel' => 'Kembali ke Jadwal',
            'sessionCompleteRoute' => route('user.jadwal-belajar.complete', $jadwal),
            'sessionCompleteMethod' => 'post',
            'sessionStatusLabel' => 'Sedang Berlangsung',
            'sessionStatusClass' => 'bg-green-100 text-green-800',
            'sessionType' => 'jadwal',
            'buttonTopic' => $buttonTopic,
            'buttonNavigationEnabled' => $buttonTopic !== null,
        ];

        return view('user.jadwal-belajar.learn', $viewData);
    }

    /**
     * Method helper untuk mendapatkan materi yang dapat diakses user
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
        if ($jadwal->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $pageParam = (int) $request->get('page', 1);
        $lineParam = $request->get('line', 1);
        $chunkParam = $request->get('chunk', 1);

        $material = $jadwal->material ?? Material::where('judul', $jadwal->materi)
            ->published()
            ->accessibleBy(Auth::user())
            ->first();

        if (!$material) {
            return response()->json(['error' => 'Materi tidak ditemukan'], 404);
        }

        $deviceIds = $jadwal->devices->pluck('id')->toArray();
        $characterCapacity = $this->materialSessionService->resolveCharacterCapacity($deviceIds);

        $state = $this->materialSessionService->composeState(
            $material,
            $pageParam,
            $lineParam,
            $chunkParam,
            $characterCapacity
        );

        return response()->json([
            'success' => true,
            'data' => [
                'current_page' => $state['pageNumber'],
                'currentPage' => $state['pageNumber'],
                'current_line_index' => $state['currentLineIndex'],
                'currentLineIndex' => $state['currentLineIndex'],
                'currentLine' => $state['totalLines'] > 0 ? $state['currentLineIndex'] + 1 : 0,
                'total_pages' => $state['totalPages'],
                'totalPages' => $state['totalPages'],
                'total_lines' => $state['totalLines'],
                'totalLines' => $state['totalLines'],
                'lines' => $state['originalLines'],
                'current_line_text' => $state['currentLineText'],
                'currentLineText' => $state['currentLineText'],
                'current_chunk_text' => $state['currentChunkText'],
                'currentChunkText' => $state['currentChunkText'],
                'current_chunk_decimal_values' => $state['currentChunkDecimalValues'],
                'currentChunkDecimalValues' => $state['currentChunkDecimalValues'],
                'current_chunk_decimal' => $state['currentChunkDecimal'],
                'currentChunkDecimal' => $state['currentChunkDecimal'],
                'current_chunk_index' => $state['currentChunkIndex'],
                'currentChunkIndex' => $state['currentChunkIndex'],
                'total_chunks' => $state['totalChunks'],
                'totalChunks' => $state['totalChunks'],
                'braillePatterns' => $state['braillePatterns'],
                'brailleBinaryPatterns' => $state['brailleBinaryPatterns'],
                'brailleDecimalPatterns' => $state['brailleDecimalPatterns'],
                'characterCapacity' => $state['characterCapacity'],
                'hasNextChunk' => $state['hasNextChunk'],
                'hasNextLine' => $state['hasNextLine'],
                'hasPrevious' => $state['hasPrevious'],
                'deviceCount' => count($deviceIds)
            ]
        ]);
    }

    /**
     * Mark a learning session as completed
     *
     * @param  \App\Models\Jadwal  $jadwal
     * @return \Illuminate\Http\Response
     */
    public function completeSession(Jadwal $jadwal)
    {
        // Check if the user is authorized to update this session
        if ($jadwal->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Update the status to 'selesai' and set completion time
        $jadwal->update([
            'status' => 'selesai',
            'waktu_selesai' => now()
        ]);

        // If this is an AJAX request
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('user.jadwal-belajar')
            ]);
        }

        // For regular form submission
        return redirect()->route('user.jadwal-belajar')
            ->with('success', 'Sesi belajar telah selesai.');
    }
}