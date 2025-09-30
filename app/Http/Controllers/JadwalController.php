<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Device;
use App\Models\Material;
use App\Models\BraillePattern;
use App\Models\UserSavedMaterial;
use App\Services\MqttService;
use App\Services\MaterialContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    protected $mqttService;
    protected $materialContentService;

    public function __construct(MqttService $mqttService, MaterialContentService $materialContentService)
    {
        $this->mqttService = $mqttService;
        $this->materialContentService = $materialContentService;
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

        return view('user.jadwal-belajar.select-device', compact('jadwal', 'devices'));
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
        $characterCapacity = $this->resolveCharacterCapacity($deviceIds);

        // Prepare initial chunk data
        $currentChunkText = '';
        $currentChunkDecimalValues = [];
        $currentChunkDecimal = '';
        $pageNumber = 1;
        $lineNumber = 1;

        $material = $jadwal->material ?? Material::where('judul', $jadwal->materi)
            ->published()
            ->accessibleBy(Auth::user())
            ->first();

        if ($material) {
            $originalPages = $this->materialContentService->getOriginalPages($material);

            if (!empty($originalPages)) {
                $pageNumber = (int) array_key_first($originalPages);
                $originalLines = $originalPages[$pageNumber] ?? [];
                $firstLine = $originalLines[0] ?? '';

                if ($firstLine !== '') {
                    $lineChunks = $this->chunkText($firstLine, $characterCapacity);
                    $currentChunkText = $lineChunks[0] ?? '';

                    if ($currentChunkText !== '') {
                        $currentChunkDecimalValues = $this->convertTextToDecimalValues($currentChunkText);

                        $brailleLines = $this->materialContentService->getBraillePageLines($material, $pageNumber);
                        $brailleLine = $brailleLines[0] ?? null;

                        if ($brailleLine && !empty($brailleLine['decimal_values'])) {
                            $chunkLength = strlen($currentChunkText);
                            $brailleDecimals = array_slice($brailleLine['decimal_values'], 0, $chunkLength);

                            if (!empty($brailleDecimals)) {
                                $currentChunkDecimalValues = array_map(
                                    fn($value) => str_pad((string) $value, 2, '0', STR_PAD_LEFT),
                                    $brailleDecimals
                                );
                            }
                        }

                        $currentChunkDecimal = implode(' ', $currentChunkDecimalValues);
                    }
                }
            }
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
                    'chunk_number' => 1,
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
        $characterCapacity = $this->resolveCharacterCapacity($deviceIds);

        $state = $this->composeMaterialState(
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
            'selectedDeviceSerials' => $deviceSerials
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
        $characterCapacity = $this->resolveCharacterCapacity($deviceIds);

        $state = $this->composeMaterialState(
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

    protected function resolveCharacterCapacity(array $deviceIds): int
    {
        if (empty($deviceIds)) {
            return 5;
        }

        $minCapacity = Device::whereIn('id', $deviceIds)->min('character_capacity');

        return $minCapacity && $minCapacity > 0 ? (int) $minCapacity : 5;
    }

    protected function composeMaterialState(
        Material $material,
        int $pageParam,
        $lineParam,
        $chunkParam,
        int $characterCapacity
    ): array {
        $pageNumber = max(1, $pageParam);
        $originalPages = $this->materialContentService->getOriginalPages($material);
        $braillePages = $this->materialContentService->getBraillePages($material);

        if (empty($originalPages)) {
            return $this->emptyMaterialState($pageNumber, $characterCapacity);
        }

        if (!array_key_exists($pageNumber, $originalPages)) {
            $pageNumber = (int) array_key_first($originalPages);
        }

        $originalLines = $originalPages[$pageNumber] ?? [];
        $totalPages = count($originalPages);
        $totalLines = count($originalLines);

        $currentLineIndex = 0;
        if ($lineParam === 'last' && $totalLines > 0) {
            $currentLineIndex = $totalLines - 1;
        } elseif (is_numeric($lineParam)) {
            $requestedIndex = max(0, ((int) $lineParam) - 1);
            $currentLineIndex = min($requestedIndex, max($totalLines - 1, 0));
        }

        $currentLineText = $originalLines[$currentLineIndex] ?? '';
        $lineChunks = $this->chunkText($currentLineText, $characterCapacity);
        $totalChunks = count($lineChunks);

        $currentChunkIndex = 0;
        if ($chunkParam === 'last' && $totalChunks > 0) {
            $currentChunkIndex = $totalChunks - 1;
        } elseif (is_numeric($chunkParam)) {
            $requestedChunk = max(0, ((int) $chunkParam) - 1);
            $currentChunkIndex = min($requestedChunk, max($totalChunks - 1, 0));
        }

        $currentChunkText = $lineChunks[$currentChunkIndex] ?? '';
        $currentChunkDecimalValues = $this->convertTextToDecimalValues($currentChunkText);
        $currentChunkDecimal = implode(' ', $currentChunkDecimalValues);

        if (!empty($braillePages[$pageNumber] ?? [])) {
            $brailleLine = $braillePages[$pageNumber][$currentLineIndex] ?? null;
            if ($brailleLine && !empty($brailleLine['decimal_values'])) {
                $chunkLength = strlen($currentChunkText);
                $offset = $currentChunkIndex * $characterCapacity;
                $brailleSlice = array_slice($brailleLine['decimal_values'], $offset, $chunkLength);
                $brailleSlice = array_map(fn($value) => str_pad((string) $value, 2, '0', STR_PAD_LEFT), $brailleSlice);

                if (!empty($brailleSlice)) {
                    $currentChunkDecimalValues = $brailleSlice;
                    $currentChunkDecimal = implode(' ', $brailleSlice);
                }
            }
        }

        $braillePatterns = [];
        $brailleBinaryPatterns = [];
        $brailleDecimalPatterns = [];

        if ($currentLineText !== '') {
            foreach (str_split($currentLineText) as $char) {
                if (!array_key_exists($char, $braillePatterns)) {
                    if ($char === ' ') {
                        $braillePatterns[$char] = '⠀';
                        $brailleBinaryPatterns[$char] = '000000';
                        $brailleDecimalPatterns[$char] = 0;
                    } else {
                        $pattern = BraillePattern::getByCharacter($char);
                        $braillePatterns[$char] = $pattern ? $pattern->braille_unicode : '⠀';
                        $brailleBinaryPatterns[$char] = $pattern ? $pattern->dots_binary : '000000';
                        $brailleDecimalPatterns[$char] = $pattern ? $pattern->dots_decimal : 0;
                    }
                }
            }
        }

        $hasNextChunk = $currentChunkIndex < max($totalChunks - 1, 0);
        $hasNextLine = $currentLineIndex < max($totalLines - 1, 0);
        $hasPrevious = $currentChunkIndex > 0 || $currentLineIndex > 0;

        return [
            'pageNumber' => $pageNumber,
            'totalPages' => max(1, $totalPages),
            'originalLines' => $originalLines,
            'totalLines' => $totalLines,
            'currentLineIndex' => $currentLineIndex,
            'currentLineText' => $currentLineText,
            'characterCapacity' => $characterCapacity,
            'lineChunks' => $lineChunks,
            'totalChunks' => $totalChunks,
            'currentChunkIndex' => $currentChunkIndex,
            'currentChunkText' => $currentChunkText,
            'currentChunkDecimalValues' => $currentChunkDecimalValues,
            'currentChunkDecimal' => $currentChunkDecimal,
            'braillePatterns' => $braillePatterns,
            'brailleBinaryPatterns' => $brailleBinaryPatterns,
            'brailleDecimalPatterns' => $brailleDecimalPatterns,
            'hasNextChunk' => $hasNextChunk,
            'hasNextLine' => $hasNextLine,
            'hasPrevious' => $hasPrevious
        ];
    }

    protected function emptyMaterialState(int $pageNumber, int $characterCapacity): array
    {
        return [
            'pageNumber' => $pageNumber,
            'totalPages' => 1,
            'originalLines' => [],
            'totalLines' => 0,
            'currentLineIndex' => 0,
            'currentLineText' => '',
            'characterCapacity' => $characterCapacity,
            'lineChunks' => [],
            'totalChunks' => 0,
            'currentChunkIndex' => 0,
            'currentChunkText' => '',
            'currentChunkDecimalValues' => [],
            'currentChunkDecimal' => '',
            'braillePatterns' => [],
            'brailleBinaryPatterns' => [],
            'brailleDecimalPatterns' => [],
            'hasNextChunk' => false,
            'hasNextLine' => false,
            'hasPrevious' => false
        ];
    }

    protected function chunkText(string $text, int $characterCapacity): array
    {
        $safeCapacity = max(1, $characterCapacity);

        if ($text === '') {
            return [];
        }

        return str_split($text, $safeCapacity);
    }

    protected function convertTextToDecimalValues(string $text): array
    {
        if ($text === '') {
            return [];
        }

        $values = [];

        foreach (str_split($text) as $char) {
            if ($char === ' ') {
                $values[] = '00';
                continue;
            }

            $pattern = BraillePattern::getByCharacter($char);
            $decimalValue = $pattern ? $pattern->dots_decimal : 0;
            $values[] = str_pad((string) $decimalValue, 2, '0', STR_PAD_LEFT);
        }

        return $values;
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