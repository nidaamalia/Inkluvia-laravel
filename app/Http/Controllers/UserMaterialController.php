<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\UserSavedMaterial;
use App\Models\BraillePattern;
use App\Services\PdfToJsonService;
use App\Services\GeminiPdfProcessorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserMaterialController extends Controller
{
    protected $pdfToJsonService;
    protected $geminiService;

    public function __construct()
    {
        $this->pdfToJsonService = new PdfToJsonService();
        $this->geminiService = new GeminiPdfProcessorService();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Material::query()
            ->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereIn('id', function($subQuery) use ($user) {
                      $subQuery->select('material_id')
                          ->from('user_saved_materials')
                          ->where('user_id', $user->id);
                  });
            })
            ->with('creator');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $sortBy = $request->get('sort', 'updated_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $materials = $query->paginate(12)->withQueryString();

        $userSavedMaterials = UserSavedMaterial::where('user_id', $user->id)
            ->pluck('material_id')
            ->toArray();

        return view('user.materi-saya.index', compact('materials', 'userSavedMaterials'));
    }

    public function create()
    {
        return view('user.materi-saya.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string',
            'tingkat' => 'required|string',
            'kelas' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:1024', // 1MB max
            'akses' => 'required|in:private,public',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('materials/pdf', $fileName, 'private');

            $material = Material::create([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori ?: null,
                'tingkat' => $request->tingkat,
                'kelas' => $request->kelas,
                'file_path' => $filePath,
                'total_halaman' => 0,
                'status' => 'processing',
                'akses' => $request->akses ?? 'private',
                'created_by' => Auth::id(),
            ]);

            $this->processConversion($material);

            return redirect()->route('user.materi-saya')
                ->with('success', 'Materi berhasil diupload! Proses konversi dengan Gemini AI sedang berlangsung.');

        } catch (\Exception $e) {
            Log::error('Material upload failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengupload materi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Material $material)
    {
        if ($material->created_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit materi ini');
        }

        return view('user.materi-saya.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        if ($material->created_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit materi ini');
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string',
            'tingkat' => 'required|string',
            'kelas' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:1024',
            'akses' => 'required|in:private,public',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori ?: null,
                'tingkat' => $request->tingkat,
                'kelas' => $request->kelas,
                'akses' => $request->akses ?? 'private',
            ];

            if ($request->hasFile('file')) {
                if ($material->file_path) {
                    Storage::disk('private')->delete($material->file_path);
                }
                
                $this->pdfToJsonService->deleteMaterialJsonData($material);

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('materials/pdf', $fileName, 'private');
                
                $updateData['file_path'] = $filePath;
                $updateData['status'] = 'processing';
                $updateData['total_halaman'] = 0;
            }

            $material->update($updateData);

            if ($request->hasFile('file')) {
                $this->processConversion($material);
            }

            return redirect()->route('user.materi-saya')
                ->with('success', 'Materi berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Material update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui materi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show content editor for material
     */
    public function editContent(Material $material)
    {
        if ($material->created_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit materi ini');
        }

        try {
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);

            if (!$jsonData || !isset($jsonData['pages'])) {
                return redirect()->back()->with('error', 'Data materi tidak valid');
            }

            return view('user.materi-saya.edit-content', compact('material', 'jsonData'));

        } catch (\Exception $e) {
            Log::error('Content editor error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat editor konten');
        }
    }

    /**
     * Update material content (line by line editing)
     */
    public function updateContent(Request $request, Material $material)
    {
        if ($material->created_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit materi ini');
        }

        $validator = Validator::make($request->all(), [
            'pages' => 'required|array',
            'pages.*.page' => 'required|integer|min:1',
            'pages.*.lines' => 'required|array',
            'pages.*.lines.*.line' => 'required|integer|min:1',
            'pages.*.lines.*.text' => 'required|string',
            'pages.*.lines.*.source' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Load existing data
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);

            // Update pages with new content
            $jsonData['pages'] = $request->input('pages');
            
            // Add metadata about editing
            $jsonData['edited_at'] = now()->toISOString();
            $jsonData['edited_by'] = Auth::id();
            $jsonData['manual_edits'] = true;

            // Save updated JSON
            Storage::disk('private')->put(
                $material->file_path,
                json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            // Update material total pages
            $material->update([
                'total_halaman' => count($jsonData['pages']),
                'updated_at' => now()
            ]);

            Log::info("Content updated for material {$material->id}", [
                'pages' => count($jsonData['pages']),
                'total_lines' => array_sum(array_map(function($page) {
                    return count($page['lines'] ?? []);
                }, $jsonData['pages']))
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Konten berhasil diperbarui',
                'total_pages' => count($jsonData['pages'])
            ]);

        } catch (\Exception $e) {
            Log::error('Content update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui konten: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get page content for editing
     */
    public function getPageContent(Material $material, $pageNumber)
    {
        if ($material->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);

            $page = collect($jsonData['pages'])->firstWhere('page', (int)$pageNumber);

            if (!$page) {
                return response()->json(['error' => 'Page not found'], 404);
            }

            return response()->json([
                'success' => true,
                'page' => $page
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Material $material)
    {
        if ($material->created_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus materi ini');
        }

        try {
            if ($material->file_path) {
                Storage::disk('private')->delete($material->file_path);
            }

            $this->pdfToJsonService->deleteMaterialJsonData($material);
            
            UserSavedMaterial::where('material_id', $material->id)->delete();

            $material->delete();

            return redirect()->route('user.materi-saya')
                ->with('success', 'Materi berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Material deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }

    public function preview(Material $material)
{
    if ($material->created_by !== Auth::id() && 
        !UserSavedMaterial::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->exists()) {
        abort(403, 'Anda tidak memiliki akses ke materi ini');
    }

    try {
        // Load original JSON content
        $jsonContent = Storage::disk('private')->get($material->file_path);
        $jsonData = json_decode($jsonContent, true);

        // Generate Braille data in real-time using BrailleConverter
        $brailleConverter = new \App\Services\BrailleConverter();
        $brailleData = $this->generateBrailleDataFromJsonRealtime($jsonData, $brailleConverter);

        $isSaved = UserSavedMaterial::where('user_id', Auth::id())
            ->where('material_id', $material->id)
            ->exists();

        return view('user.materi-saya.preview', compact('material', 'jsonData', 'brailleData', 'isSaved'));

    } catch (\Exception $e) {
        Log::error('Preview error: ' . $e->getMessage());
        return view('user.materi-saya.preview', [
            'material' => $material,
            'jsonData' => ['pages' => []],
            'brailleData' => null,
            'isSaved' => false,
            'error' => 'Gagal memuat preview materi: ' . $e->getMessage()
        ]);
    }
}

    /**
     * Generate Braille data from JSON using BrailleConverter
     */
    private function generateBrailleDataFromJsonRealtime($jsonData, $brailleConverter)
    {
        if (!$jsonData || !isset($jsonData['pages'])) {
            return null;
        }

        $brailleData = [
            'judul' => isset($jsonData['judul']) ? $brailleConverter->toBraille($jsonData['judul']) : '',
            'pages' => []
        ];

        foreach ($jsonData['pages'] as $pageData) {
            $braillePage = [
                'page' => $pageData['page'] ?? 1,
                'lines' => []
            ];

            if (isset($pageData['lines']) && is_array($pageData['lines'])) {
                foreach ($pageData['lines'] as $line) {
                    if (isset($line['text']) && !empty(trim($line['text']))) {
                        $originalText = trim($line['text']);
                        
                        // Convert to Braille using BrailleConverter
                        $brailleText = $brailleConverter->toBraille($originalText);
                        
                        // Get decimal values
                        $decimalValues = $this->convertTextToDecimalValues($originalText);
                        
                        $braillePage['lines'][] = [
                            'line' => $line['line'] ?? count($braillePage['lines']) + 1,
                            'text' => $brailleText,
                            'original' => $originalText,
                            'decimal_values' => $decimalValues
                        ];
                    }
                }
            }

            $brailleData['pages'][] = $braillePage;
        }

        return $brailleData;
    }

    public function download(Material $material)
    {
        if ($material->created_by !== Auth::id() && 
            !UserSavedMaterial::where('user_id', Auth::id())
                ->where('material_id', $material->id)
                ->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini');
        }

        if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        $jsonContent = Storage::disk('private')->get($material->file_path);
        $filename = 'materi_' . $material->id . '_' . time() . '.json';

        return response($jsonContent)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Process PDF to JSON conversion using Gemini AI
     */
    private function processConversion(Material $material)
    {
        try {
            $pdfPath = Storage::disk('private')->path($material->file_path);

            $options = [
                'judul' => $material->judul,
                'caption_images' => true,
                'ocr_images' => true,
                'sanitize_content' => true,
                'convert_math' => true,
            ];

            Log::info("Starting Gemini AI PDF conversion for material {$material->id}");
            
            $jsonData = $this->geminiService->processPdfWithGemini($pdfPath, $options);

            if (!$jsonData) {
                Log::warning("Gemini AI processing failed, falling back to standard conversion");
                $jsonData = $this->pdfToJsonService->convertPdfToJson($pdfPath, $options);
            }

            if (!$jsonData) {
                throw new \Exception('All PDF conversion methods failed');
            }

            $jsonPath = 'materials/json/' . $material->id . '.json';
            Storage::disk('private')->put(
                $jsonPath, 
                json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $originalPdfPath = $material->file_path;
            if (Storage::disk('private')->exists($originalPdfPath)) {
                Storage::disk('private')->delete($originalPdfPath);
            }

            $material->update([
                'file_path' => $jsonPath,
                'total_halaman' => count($jsonData['pages'] ?? []),
                'status' => 'published'
            ]);

            Log::info("PDF conversion completed for material {$material->id}");

        } catch (\Exception $e) {
            Log::error("Conversion failed for material {$material->id}: " . $e->getMessage());
            $material->update(['status' => 'draft']);
        }
    }

    private function convertTextToBrailleUnicode(string $text): string
    {
        if (empty($text)) return '';

        $result = '';
        $length = mb_strlen($text, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            
            if ($char === ' ') {
                $result .= '⠀';
                continue;
            }
            
            $pattern = BraillePattern::getByCharacter($char);
            if ($pattern) {
                $result .= $pattern->braille_unicode;
            } else {
                $result .= '⠀';
            }
        }

        return $result;
    }

    private function convertTextToDecimalValues(string $text): array
    {
        if (empty($text)) return [];

        $values = [];
        $length = mb_strlen($text, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            
            if ($char === ' ') {
                $values[] = '00';
                continue;
            }
            
            $pattern = BraillePattern::getByCharacter($char);
            if ($pattern) {
                $values[] = str_pad((string)$pattern->dots_decimal, 2, '0', STR_PAD_LEFT);
            } else {
                $values[] = '00';
            }
        }

        return $values;
    }

    private function generateBrailleDataFromJson($jsonData)
    {
        if (!$jsonData || !isset($jsonData['pages'])) {
            return null;
        }

        $brailleData = [
            'judul' => $this->convertTextToBrailleUnicode($jsonData['judul'] ?? ''),
            'pages' => []
        ];

        foreach ($jsonData['pages'] as $pageData) {
            $braillePage = [
                'page' => $pageData['page'] ?? 1,
                'lines' => []
            ];

            if (isset($pageData['lines']) && is_array($pageData['lines'])) {
                foreach ($pageData['lines'] as $line) {
                    if (isset($line['text']) && !empty(trim($line['text']))) {
                        $originalText = trim($line['text']);
                        
                        $braillePage['lines'][] = [
                            'line' => $line['line'] ?? count($braillePage['lines']) + 1,
                            'text' => $this->convertTextToBrailleUnicode($originalText),
                            'original' => $originalText,
                            'decimal_values' => $this->convertTextToDecimalValues($originalText)
                        ];
                    }
                }
            }

            $brailleData['pages'][] = $braillePage;
        }

        return $brailleData;
    }
}