<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Device;
use App\Models\UserSavedMaterial;
use App\Services\MaterialSessionService;
use App\Services\MqttService;
use App\Services\DeviceButtonChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PerpustakaanController extends Controller
{
    protected MaterialSessionService $materialSessionService;
    protected MqttService $mqttService;

    public function __construct(MaterialSessionService $materialSessionService, MqttService $mqttService)
    {
        $this->materialSessionService = $materialSessionService;
        $this->mqttService = $mqttService;
    }

    /**
     * Display listing of materials available to user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $query = Material::query()
            ->where('status', 'published')
            ->with('creator');

        // Filter berdasarkan hak akses - sesuai dengan aturan admin
        $query->where(function($q) use ($userLembagaId) {
            // Materi publik - dapat diakses semua orang
            $q->where('akses', 'public');
            
            // Materi khusus lembaga - hanya dapat diakses user dari lembaga tersebut
            if ($userLembagaId) {
                $q->orWhere('akses', $userLembagaId);
            }
        });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter tingkat
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        // Sorting
        $sortBy = $request->get('sort', 'judul');
        $sortOrder = $request->get('order', 'asc');
        
        if (in_array($sortBy, ['judul', 'tahun_terbit', 'published_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $materials = $query->paginate(12)->withQueryString();

        // Get user's saved materials
        $userSavedMaterials = UserSavedMaterial::where('user_id', Auth::id())
            ->pluck('material_id')
            ->toArray();

        // Get user's devices
        $userDevices = Device::where('user_id', Auth::id())
            ->where('status', 'aktif')
            ->get();

        return view('user.perpustakaan', compact('materials', 'userSavedMaterials', 'userDevices'));
    }

    /**
     * Show saved materials page
     */
    public function savedMaterials(Request $request)
    {
        $user = Auth::user();
        
        $query = Material::query()
            ->whereIn('id', function($subQuery) use ($user) {
                $subQuery->select('material_id')
                    ->from('user_saved_materials')
                    ->where('user_id', $user->id);
            })
            ->where('status', 'published')
            ->with('creator');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter tingkat
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        $materials = $query->paginate(12)->withQueryString();

        // Get user's saved materials
        $userSavedMaterials = UserSavedMaterial::where('user_id', Auth::id())
            ->pluck('material_id')
            ->toArray();

        return view('user.materi-tersimpan', compact('materials', 'userSavedMaterials'));
    }

    /**
     * Toggle saved status
     */
    public function toggleSaved(Material $material)
    {
        // Check access rights
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;
        
        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke materi ini'
            ], 403);
        }

        $userId = Auth::id();
        $saved = UserSavedMaterial::where('user_id', $userId)
            ->where('material_id', $material->id)
            ->first();

        if ($saved) {
            $saved->delete();
            $isSaved = false;
            $message = 'Materi berhasil dihapus dari daftar tersimpan';
        } else {
            UserSavedMaterial::create([
                'user_id' => $userId,
                'material_id' => $material->id,
                'saved_at' => now()
            ]);
            $isSaved = true;
            $message = 'Materi berhasil disimpan';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_saved' => $isSaved,
                'message' => $message
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Preview material content for screen readers
     */
    public function preview(Material $material)
    {
        // Check access rights
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;
        
        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke materi ini'
            ], 403);
        }

        try {
            // Get JSON content
            if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konten materi tidak tersedia'
                ], 404);
            }

            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);

            // Format content for screen reader
            $previewData = [
                'id' => $material->id,
                'judul' => $material->judul,
                'penerbit' => $material->penerbit ?? 'Tidak ada',
                'tahun' => $material->tahun_terbit ?? 'Tidak ada',
                'kategori' => \App\Models\Material::getKategoriOptions()[$material->kategori] ?? $material->kategori,
                'tingkat' => \App\Models\Material::getTingkatOptions()[$material->tingkat] ?? $material->tingkat,
                'total_halaman' => count($jsonData['pages'] ?? []),
                'deskripsi' => $material->deskripsi ?? '',
                'preview_text' => $this->extractPreviewText($jsonData),
                'full_content' => $this->formatFullContent($jsonData)
            ];

            return response()->json([
                'success' => true,
                'data' => $previewData
            ]);

        } catch (\Exception $e) {
            Log::error('Preview error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat preview materi'
            ], 500);
        }
    }

    /**
     * Show detailed preview page
     */
    public function showPreview(Material $material)
    {
        // Check access rights
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke materi ini');
        }

        try {
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);
            
            $isSaved = UserSavedMaterial::where('user_id', Auth::id())
                ->where('material_id', $material->id)
                ->exists();

            return view('user.preview-materi', compact('material', 'jsonData', 'isSaved'));
            
        } catch (\Exception $e) {
            Log::error('Preview page error: ' . $e->getMessage());
            return view('user.preview-materi', [
                'material' => $material,
                'error' => 'Gagal memuat konten materi'
            ]);
        }
    }

    /**
     * Send material to EduBraille device
     */
    public function sendToDevice(Request $request, Material $material)
    {
        // Check access rights
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;
        
        if (!$hasAccess) {
            return redirect()->route('user.perpustakaan')
                ->with('error', 'Anda tidak memiliki akses ke materi ini');
        }

        return redirect()->route('user.perpustakaan.start', $material);
    }

    public function startMaterial(Material $material)
    {
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;

        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke materi ini');
        }

        $devices = Device::where('status', 'aktif')
            ->where(function ($query) {
                $query->where('lembaga_id', Auth::user()->lembaga_id)
                      ->orWhere('user_id', Auth::id());
            })
            ->get();

        $preselectedDevices = session('perpustakaan_selected_devices', []);

        return view('user.jadwal-belajar.select-device', [
            'sessionTitle' => $material->judul,
            'sessionSubtitle' => Str::limit($material->deskripsi ?? '', 120),
            'sessionBackRoute' => route('user.perpustakaan'),
            'sessionBackLabel' => 'Kembali ke Perpustakaan',
            'sessionSubmitRoute' => route('user.perpustakaan.send-material', $material),
            'sessionType' => 'perpustakaan',
            'jadwal' => null,
            'devices' => $devices,
            'material' => $material,
            'preselectedDevices' => $preselectedDevices,
        ]);
    }

    public function sendMaterialToDevices(Request $request, Material $material)
    {
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;

        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke materi ini');
        }

        $validated = $request->validate([
            'devices' => 'required|array',
            'devices.*' => 'exists:devices,id'
        ]);

        $devices = Device::whereIn('id', $validated['devices'])->get();
        $deviceIds = $devices->pluck('id')->toArray();
        $deviceSerials = $devices->pluck('serial_number')->toArray();
        $characterCapacity = $this->materialSessionService->resolveCharacterCapacity($deviceIds);

        $state = $this->materialSessionService->getInitialState($material, $characterCapacity);

        foreach ($devices as $device) {
            try {
                $this->mqttService->sendMaterial($device->serial_number, [
                    'material_id' => $material->id,
                    'judul' => $material->judul,
                    'user' => $user->nama_lengkap,
                    'character_capacity' => $characterCapacity,
                    'page_number' => $state['pageNumber'],
                    'line_number' => max(1, $state['currentLineIndex'] + 1),
                    'chunk_number' => max(1, $state['currentChunkIndex'] + 1),
                    'current_chunk_text' => $state['currentChunkText'],
                    'current_chunk_decimal_values' => $state['currentChunkDecimalValues'],
                    'current_chunk_decimal' => $state['currentChunkDecimal'],
                    'timestamp' => now()->toISOString(),
                    'context' => 'perpustakaan',
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send material to device: ' . $e->getMessage());
            }
        }

        session()->put('perpustakaan_selected_devices', $deviceIds);
        session()->put('perpustakaan_selected_device_serials', $deviceSerials);

        return redirect()->route('user.perpustakaan.learn', [
            'material' => $material->id,
            'page' => 1,
            'line' => 1,
        ])->with('success', 'Materi berhasil dikirim ke perangkat!');
    }

    public function learnMaterial(Request $request, Material $material)
    {
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;

        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke materi ini');
        }

        $pageParam = (int) $request->get('page', 1);
        $lineParam = $request->get('line', 1);
        $chunkParam = $request->get('chunk', 1);

        $selectedDeviceIds = session('perpustakaan_selected_devices', []);
        $selectedDeviceSerials = session('perpustakaan_selected_device_serials', []);

        if (empty($selectedDeviceIds)) {
            $selectedDeviceIds = Device::where('status', 'aktif')
                ->where(function ($query) {
                    $query->where('lembaga_id', Auth::user()->lembaga_id)
                          ->orWhere('user_id', Auth::id());
                })
                ->pluck('id')
                ->toArray();
        }

        $characterCapacity = $this->materialSessionService->resolveCharacterCapacity($selectedDeviceIds);

        $state = $this->materialSessionService->composeState(
            $material,
            $pageParam,
            $lineParam,
            $chunkParam,
            $characterCapacity
        );

        $buttonTopic = null;
        if (count($selectedDeviceSerials) === 1) {
            $buttonTopic = DeviceButtonChannel::topicForSerial($selectedDeviceSerials[0]);
        }

        return view('user.jadwal-belajar.learn', [
            'sessionTitle' => $material->judul,
            'sessionBackRoute' => route('user.perpustakaan'),
            'sessionBackLabel' => 'Kembali ke Perpustakaan',
            'sessionCompleteRoute' => null,
            'sessionType' => 'perpustakaan',
            'sessionStatusLabel' => 'Mode Perpustakaan',
            'sessionStatusClass' => 'bg-blue-100 text-blue-800',
            'jadwal' => null,
            'material' => $material,
            'sessionSubtitle' => Str::limit($material->deskripsi ?? '', 120),
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
            'deviceCount' => count($selectedDeviceIds),
            'lines' => $state['originalLines'],
            'originalLines' => $state['originalLines'],
            'selectedDeviceIds' => $selectedDeviceIds,
            'selectedDeviceSerials' => $selectedDeviceSerials,
            'learnRouteName' => 'user.perpustakaan.learn',
            'learnRouteParams' => ['material' => $material->id],
            'navigateRouteName' => 'user.perpustakaan.learn',
            'navigateRouteParams' => ['material' => $material->id],
            'materialPageRouteName' => 'user.perpustakaan.material-page',
            'materialPageParams' => ['material' => $material->id],
            'buttonTopic' => $buttonTopic,
            'buttonNavigationEnabled' => $buttonTopic !== null,
        ]);
    }

    public function materialPage(Material $material, Request $request)
    {
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;

        $hasAccess = $material->akses === 'public' || $material->akses == $userLembagaId;

        if (!$hasAccess) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke materi ini'], 403);
        }

        $pageParam = (int) $request->get('page', 1);
        $lineParam = $request->get('line', 1);
        $chunkParam = $request->get('chunk', 1);

        $selectedDeviceIds = session('perpustakaan_selected_devices', []);
        if (empty($selectedDeviceIds)) {
            $selectedDeviceIds = Device::where('status', 'aktif')
                ->where(function ($query) {
                    $query->where('lembaga_id', Auth::user()->lembaga_id)
                          ->orWhere('user_id', Auth::id());
                })
                ->pluck('id')
                ->toArray();
        }

        $characterCapacity = $this->materialSessionService->resolveCharacterCapacity($selectedDeviceIds);

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
                'deviceCount' => count($selectedDeviceIds)
            ]
        ]);
    }

    /**
     * Extract preview text from JSON data
     */
    private function extractPreviewText($jsonData, $maxLines = 10)
    {
        $previewText = [];
        $lineCount = 0;

        if (isset($jsonData['pages']) && is_array($jsonData['pages'])) {
            foreach ($jsonData['pages'] as $page) {
                if ($lineCount >= $maxLines) break;
                
                if (isset($page['lines']) && is_array($page['lines'])) {
                    foreach ($page['lines'] as $line) {
                        if ($lineCount >= $maxLines) break;
                        if (isset($line['text']) && !empty(trim($line['text']))) {
                            $previewText[] = trim($line['text']);
                            $lineCount++;
                        }
                    }
                }
            }
        }

        return implode(' ', $previewText);
    }

    /**
     * Format full content for detailed view
     */
    private function formatFullContent($jsonData)
    {
        $content = [];

        if (isset($jsonData['pages']) && is_array($jsonData['pages'])) {
            foreach ($jsonData['pages'] as $page) {
                $pageContent = [
                    'page_number' => $page['page'] ?? 1,
                    'lines' => []
                ];
                
                if (isset($page['lines']) && is_array($page['lines'])) {
                    foreach ($page['lines'] as $line) {
                        if (isset($line['text']) && !empty(trim($line['text']))) {
                            $pageContent['lines'][] = trim($line['text']);
                        }
                    }
                }
                
                if (!empty($pageContent['lines'])) {
                    $content[] = $pageContent;
                }
            }
        }

        return $content;
    }
}