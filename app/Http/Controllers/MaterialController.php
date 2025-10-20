<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\BraillePattern;
use App\Services\MaterialConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    public function __construct(
        private readonly MaterialConversionService $materialConversionService,
    ) {}

    public function index(Request $request)
    {
        $query = Material::with('creator');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $materials = $query->paginate(10)->withQueryString();
        
        return view('admin.manajemen-materi.index', compact('materials'));
    }
    
    public function create()
    {
        return view('admin.manajemen-materi.create');
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'penerbit' => 'nullable|string|max:255',
            'edisi' => 'nullable|string|max:100',
            'kategori' => 'nullable|string',
            'tingkat' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:51200',
            'akses' => 'required'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('materials/pdf', $fileName, 'private');
        
        $material = Material::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tahun_terbit' => $request->tahun_terbit,
            'penerbit' => $request->penerbit,
            'edisi' => $request->edisi,
            'kategori' => $request->kategori ?: null,
            'tingkat' => $request->tingkat,
            'file_path' => $filePath,
            'total_halaman' => 0,
            'status' => 'processing',
            'akses' => $request->akses,
            'created_by' => Auth::id()
        ]);
        
        $this->triggerPdfConversion($material);
        
        return redirect()->route('admin.manajemen-materi')
            ->with('success', 'Materi berhasil diupload!');
    }
    
    public function show(Material $material)
    {
        $material->load('creator');
        return view('admin.manajemen-materi.show', compact('material'));
    }
    
    public function edit(Material $material)
    {
        return view('admin.manajemen-materi.edit', compact('material'));
    }
    
    public function update(Request $request, Material $material)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'penerbit' => 'nullable|string|max:255',
            'edisi' => 'nullable|string|max:100',
            'kategori' => 'nullable|string',
            'tingkat' => 'required|string',
            'status' => 'required|in:draft,processing,review,published,archived,pending',
            'akses' => 'required',
            'file' => 'nullable|file|mimes:pdf|max:51200'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        if ($request->hasFile('file')) {
            if ($material->file_path && Storage::disk('private')->exists($material->file_path)) {
                Storage::disk('private')->delete($material->file_path);
            }
            
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('materials/pdf', $fileName, 'private');
            
            $pdfToJsonService = new PdfToJsonService();
            $pdfToJsonService->deleteMaterialJsonData($material);
        } else {
            $filePath = $material->file_path;
        }
        
        $material->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tahun_terbit' => $request->tahun_terbit,
            'penerbit' => $request->penerbit,
            'edisi' => $request->edisi,
            'kategori' => $request->kategori ?: null,
            'tingkat' => $request->tingkat,
            'status' => $request->status,
            'akses' => $request->akses,
            'file_path' => $filePath,
            'published_at' => $request->status === 'published' ? now() : null
        ]);
        
        if ($request->hasFile('file')) {
            $this->triggerPdfConversion($material);
        }
        
        return redirect()->route('admin.manajemen-materi')
            ->with('success', 'Materi berhasil diperbarui!');
    }
    
    public function updateStatus(Request $request, Material $material)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,processing,review,published,archived'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => 'Status tidak valid'], 400);
        }
        
        $material->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'status' => $material->status,
            'status_badge_color' => $material->status_badge_color
        ]);
    }

    public function destroy(Material $material)
    {
        if ($material->file_path) {
            Storage::disk('private')->delete($material->file_path);
        }
        
        $pdfToJsonService = new PdfToJsonService();
        $pdfToJsonService->deleteMaterialJsonData($material);
        
        $material->delete();
        
        return redirect()->route('admin.manajemen-materi')
            ->with('success', 'Materi berhasil dihapus!');
    }
    
    public function preview(Material $material)
    {
        try {
            if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
                return response()->json([
                    'error' => 'File JSON tidak ditemukan untuk materi ini'
                ], 404);
            }

            try {
                $jsonContent = Storage::disk('private')->get($material->file_path);
                $jsonData = json_decode($jsonContent, true);
                
                if ($jsonData && isset($jsonData['pages']) && !empty($jsonData['pages'])) {
                    return response()->json($jsonData);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to read JSON file: ' . $e->getMessage());
            }

            $jsonData = [
                'judul' => $material->judul,
                'penerbit' => $material->penerbit,
                'tahun' => $material->tahun_terbit,
                'edisi' => $material->edisi,
                'pages' => [
                    [
                        'page' => 1,
                        'lines' => [
                            ['line' => 1, 'text' => 'PDF conversion failed. Material info:'],
                            ['line' => 2, 'text' => 'Judul: ' . $material->judul],
                            ['line' => 3, 'text' => 'Kategori: ' . ($material->kategori ?? 'Tidak ada')],
                            ['line' => 4, 'text' => 'Tingkat: ' . ($material->tingkat ?? 'Tidak ada')],
                            ['line' => 5, 'text' => 'Status: ' . ($material->status ?? 'Tidak ada')]
                        ]
                    ]
                ]
            ];
            
            return response()->json($jsonData);
            
        } catch (\Exception $e) {
            \Log::error('Preview error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memuat preview: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function downloadJson(Material $material)
    {
        try {
            if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
                return redirect()->back()->with('error', 'File JSON tidak ditemukan');
            }
            
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);
            
            if (!$jsonData) {
                return redirect()->back()->with('error', 'Tidak dapat memuat data JSON');
            }
            
            $filename = 'materi_' . $material->id . '_' . time() . '.json';
            
            return response()->json($jsonData)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh JSON: ' . $e->getMessage());
        }
    }
    
    public function library(Request $request)
    {
        $user = Auth::user();
        $userLembagaId = $user->lembaga_id;
        
        $query = Material::published()->with('creator');
        
        $query->where(function($q) use ($userLembagaId) {
            $q->where('akses', 'public');
            
            if ($userLembagaId) {
                $q->orWhere('akses', $userLembagaId);
            }
        });
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        
        if ($request->has('tingkat') && $request->tingkat) {
            $query->where('tingkat', $request->tingkat);
        }
        
        $materials = $query->paginate(12)->withQueryString();
        
        return view('user.perpustakaan', compact('materials'));
    }
    
    public function requestMaterial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_materi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'nullable|string',
            'tingkat' => 'required|string',
            'prioritas' => 'required|in:rendah,sedang,tinggi'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        MaterialRequest::create([
            'judul_materi' => $request->judul_materi,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'tingkat' => $request->tingkat,
            'prioritas' => $request->prioritas,
            'status' => 'pending',
            'requested_by' => Auth::id()
        ]);
        
        return redirect()->route('user.request-materi')
            ->with('success', 'Request materi berhasil dikirim! Admin akan meninjau request Anda.');
    }
    
    public function myRequests()
    {
        $requests = MaterialRequest::where('requested_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('user.my-requests', compact('requests'));
    }

    /**
     * Trigger PDF to JSON conversion (NO Braille file generation)
     */
    private function triggerPdfConversion(Material $material)
    {
        try {
            $this->materialConversionService->convertMaterial($material, [], [
                'target_status' => 'review',
            ]);

        } catch (\Exception $e) {
            \Log::error("PDF conversion failed for material {$material->id}: " . $e->getMessage());
            $material->update(['status' => 'draft']);
        }
    }
    /**
     * Download material file
     */
    public function download(Material $material)
    {
        $this->checkMaterialAccess($material);
        
        if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
            abort(404, 'File not found');
        }
        
        return Storage::disk('private')->download($material->file_path, $material->judul . '.json');
    }

    /**
     * Get Braille content - converted on-the-fly from original text
     */
    public function getBrailleContent(Material $material)
    {
        try {
            $this->checkMaterialAccess($material);
            
            if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
                return response()->json([
                    'error' => 'File JSON tidak ditemukan untuk materi ini'
                ], 404);
            }
            
            $jsonContent = Storage::disk('private')->get($material->file_path);
            $jsonData = json_decode($jsonContent, true);
            
            if (!$jsonData || !isset($jsonData['pages'])) {
                return response()->json([
                    'error' => 'Data JSON tidak valid'
                ], 400);
            }
            
            // Convert metadata to Braille
            $brailleJson = [
                'judul' => $this->convertTextToBrailleUnicode($jsonData['judul'] ?? ''),
                'penerbit' => $this->convertTextToBrailleUnicode($jsonData['penerbit'] ?? ''),
                'tahun' => $this->convertTextToBrailleUnicode((string)($jsonData['tahun'] ?? '')),
                'edisi' => $this->convertTextToBrailleUnicode($jsonData['edisi'] ?? ''),
                'pages' => []
            ];
            
            // Convert each page
            foreach ($jsonData['pages'] as $pageData) {
                $braillePage = [
                    'page' => $pageData['page'] ?? 1,
                    'lines' => []
                ];
                
                if (isset($pageData['lines']) && is_array($pageData['lines'])) {
                    foreach ($pageData['lines'] as $line) {
                        if (isset($line['text']) && !empty(trim($line['text']))) {
                            $originalText = trim($line['text']);
                            $brailleText = $this->convertTextToBrailleUnicode($originalText);
                            $decimalValues = $this->convertTextToDecimalValues($originalText);
                            
                            $braillePage['lines'][] = [
                                'line' => $line['line'] ?? count($braillePage['lines']) + 1,
                                'text' => $brailleText,
                                'decimal_values' => $decimalValues,
                                'decimal' => implode('', $decimalValues)
                            ];
                        }
                    }
                }
                
                $brailleJson['pages'][] = $braillePage;
            }
            
            return response()->json($brailleJson);
            
        } catch (\Exception $e) {
            \Log::error('getBrailleContent error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memuat konten braille: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert text to Braille Unicode using BraillePattern model
     */
    private function convertTextToBrailleUnicode(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $result = '';
        $length = mb_strlen($text, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            
            if ($char === ' ') {
                $result .= 'â €'; // Braille blank
                continue;
            }
            
            $pattern = BraillePattern::getByCharacter($char);
            if ($pattern) {
                $result .= $pattern->braille_unicode;
            } else {
                $result .= 'â €'; // Default to blank if not found
            }
        }

        return $result;
    }

    /**
     * Convert text to decimal values using BraillePattern model
     */
    private function convertTextToDecimalValues(string $text): array
    {
        if (empty($text)) {
            return [];
        }

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
                $values[] = '00'; // Default to 00 if not found
            }
        }

        return $values;
    }

    /**
     * Check if user has access to material
     */
    private function checkMaterialAccess(Material $material)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        switch ($material->akses) {
            case 'public':
                return true;
                
            case 'premium':
                return true;
                
            case 'restricted':
                return $user->lembaga_id === $material->creator->lembaga_id;
                
            default:
                return false;
        }
    }

    public function userPreview(Material $material)
    {
        try {
            if (!$material->file_path || !Storage::disk('private')->exists($material->file_path)) {
                return view('user.material-preview', [
                    'material' => $material,
                    'error' => 'File JSON tidak ditemukan untuk materi ini'
                ]);
            }

            try {
                $jsonContent = Storage::disk('private')->get($material->file_path);
                $jsonData = json_decode($jsonContent, true);
                
                if ($jsonData && isset($jsonData['pages']) && !empty($jsonData['pages'])) {
                    return view('user.material-preview', [
                        'material' => $material,
                        'jsonData' => $jsonData
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to read JSON file: ' . $e->getMessage());
            }

            $jsonData = [
                'judul' => $material->judul,
                'penerbit' => $material->penerbit,
                'tahun' => $material->tahun_terbit,
                'edisi' => $material->edisi,
                'pages' => [
                    [
                        'page' => 1,
                        'lines' => [
                            ['line' => 1, 'text' => 'PDF conversion failed. Material info:'],
                            ['line' => 2, 'text' => 'Judul: ' . $material->judul],
                            ['line' => 3, 'text' => 'Kategori: ' . ($material->kategori ?? 'Tidak ada')],
                            ['line' => 4, 'text' => 'Tingkat: ' . ($material->tingkat ?? 'Tidak ada')],
                            ['line' => 5, 'text' => 'Status: ' . ($material->status ?? 'Tidak ada')]
                        ]
                    ]
                ]
            ];
            
            return view('user.material-preview', [
                'material' => $material,
                'jsonData' => $jsonData
            ]);
            
        } catch (\Exception $e) {
            \Log::error('User preview error: ' . $e->getMessage());
            return view('user.material-preview', [
                'material' => $material,
                'error' => 'Gagal memuat preview: ' . $e->getMessage()
            ]);
        }
    }
}