@extends('layouts.user')

@section('title', $sessionTitle ?? 'Kirim Materi Braille')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ $sessionBackRoute ?? route('user.jadwal-belajar') }}" 
           class="inline-flex items-center text-primary hover:text-primary-dark font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded px-2 py-1"
           aria-label="{{ $sessionBackLabel ?? 'Kembali ke daftar jadwal' }}">
            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>
            {{ $sessionBackLabel ?? 'Kembali' }}
        </a>
    </div>

    <!-- Session Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $sessionTitle ?? ($material->judul ?? 'Materi EduBraille') }}</h1>
                @if(!empty($sessionSubtitle))
                    <p class="text-sm text-gray-600">{{ $sessionSubtitle }}</p>
                @elseif(!empty($jadwal))
                    <p class="text-sm text-gray-600">{{ $jadwal->materi ?? 'Materi Pembelajaran' }}</p>
                @elseif(!empty($material) && !empty($material->deskripsi))
                    <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($material->deskripsi, 120) }}</p>
                @endif
            </div>
            @if(!empty($sessionStatusLabel))
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $sessionStatusClass ?? 'bg-green-100 text-green-800' }}">
                <i class="fas fa-circle text-xs mr-2 {{ ($sessionStatusClass ?? '') ? '' : 'animate-pulse' }}" aria-hidden="true"></i>
                {{ $sessionStatusLabel }}
            </span>
            @endif
        </div>
    </div>

    <!-- Braille Display -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-6">
        <div class="text-center mb-8">
            <h2 class="text-lg font-semibold text-primary mb-4">Teks Asli (Baris {{ $currentLine }} dari {{ $totalLines }})</h2>
            
            <!-- Original Text Display -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-lg">{{ $currentLineText }}</p>
            </div>
            
            <h3 class="text-md font-semibold text-gray-700 mb-3">Tampilan Braille ({{ $characterCapacity }} karakter per baris)</h3>
            
            <!-- Braille Display -->
            <div id="braille-display" class="mb-6 p-4 bg-gray-100 rounded-lg" aria-label="Tampilan pola braille">
                <div class="text-4xl mb-2">
                    @foreach(str_split($currentChunkText) as $char)
                        <span class="braille-char" style="margin: 0 2px;">
                            {{ $braillePatterns[$char] ?? '⠀' }}
                        </span>
                    @endforeach
                </div>
                <div class="text-sm text-gray-500">
                    @foreach(str_split($currentChunkText) as $char)
                        <span style="display: inline-block; width: 24px; text-align: center;">
                            {{ $char === ' ' ? ' ' : $char }}
                        </span>
                    @endforeach
                </div>
                <!-- @if(!empty($currentChunkDecimalValues))
                <div class="text-sm text-gray-500 mt-2">
                    @foreach($currentChunkDecimalValues as $decimal)
                        <span style="display: inline-block; width: 24px; text-align: center;">
                            {{ $decimal }}
                        </span>
                    @endforeach
                </div>
                @endif -->
                <div class="text-xs text-gray-400 mt-2">
                    Chunk {{ $currentChunk }} dari {{ $totalChunks }}
                </div>
                <div class="mt-4 flex flex-col items-center gap-2 text-sm text-gray-700">
                    <button id="btn-read" type="button"
                            class="px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            aria-label="Bacakan teks saat ini (tombol spasi)">
                        <i class="fas fa-volume-up mr-2" aria-hidden="true"></i>
                        Bacakan Teks (Spasi)
                    </button>
                    <span id="speech-status" class="text-xs text-gray-500" role="status" aria-live="polite"></span>
                </div>
            </div>

            <!-- Navigation Info -->
            <div id="navigation-info" class="text-gray-600 text-sm mb-4">
                <div>Halaman {{ $pageNumber }} dari {{ $totalPages }}</div>
                <div>Baris {{ $currentLine }} dari {{ $totalLines }}</div>
            </div>
            
            <!-- Braille Unicode Pattern (hidden, used for MQTT) -->
            <div id="braille-unicode-pattern" class="sr-only" aria-hidden="true"></div>

        <!-- Navigation Controls -->
        <div class="space-y-3">
            <!-- Row 1: Chunk Navigation -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Previous Chunk -->
                <div>
                    @if($currentChunk > 1 || $currentLine > 1 || $pageNumber > 1)
                        <a id="link-chunk-prev" href="{{ route($learnRouteName ?? 'user.jadwal-belajar.learn', array_merge($learnRouteParams ?? ['jadwal' => $jadwal->id ?? null], [
                            'page' => $currentChunk > 1 ? $pageNumber : ($currentLine > 1 ? $pageNumber : $pageNumber - 1),
                            'line' => $currentChunk > 1 ? $currentLine : ($currentLine > 1 ? $currentLine - 1 : 'last'),
                            'chunk' => $currentChunk > 1 ? $currentChunk - 1 : 'last'
                        ])) }}"
                           class="w-full flex items-center justify-center px-3 py-3 bg-white border-2 border-primary text-primary font-medium rounded-lg hover:bg-primary hover:text-white transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2"
                           aria-label="Chunk sebelumnya">
                            <i class="fas fa-chevron-left mr-2" aria-hidden="true"></i>
                            <span class="whitespace-nowrap">Chunk Sebelumnya</span>
                        </a>
                    @else
                        <button disabled class="w-full flex items-center justify-center px-3 py-3 bg-gray-100 text-gray-400 font-medium rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-left mr-2" aria-hidden="true"></i>
                            <span class="whitespace-nowrap">Chunk Sebelumnya</span>
                        </button>
                    @endif
                </div>

                <!-- Next Chunk -->
                <div>
                    @if($hasNextChunk || $hasNextLine || $pageNumber < $totalPages)
                        <a id="link-chunk-next" href="{{ route($learnRouteName ?? 'user.jadwal-belajar.learn', array_merge($learnRouteParams ?? ['jadwal' => $jadwal->id ?? null], [
                            'page' => $hasNextChunk ? $pageNumber : ($hasNextLine ? $pageNumber : $pageNumber + 1),
                            'line' => $hasNextChunk ? $currentLine : ($hasNextLine ? $currentLine + 1 : 1),
                            'chunk' => $hasNextChunk ? $currentChunk + 1 : 1
                        ])) }}" 
                           class="w-full flex items-center justify-center px-3 py-3 bg-white border-2 border-primary text-primary font-medium rounded-lg hover:bg-primary hover:text-white transition-colors focus:ring-2 focus:ring-primary focus:ring-offset-2"
                           aria-label="Chunk selanjutnya">
                            <span class="whitespace-nowrap">Chunk Berikutnya</span>
                            <i class="fas fa-chevron-right ml-2" aria-hidden="true"></i>
                        </a>
                    @else
                        <button disabled class="w-full flex items-center justify-center px-3 py-3 bg-gray-100 text-gray-400 font-medium rounded-lg cursor-not-allowed">
                            <span class="whitespace-nowrap">Chunk Berikutnya</span>
                            <i class="fas fa-chevron-right ml-2" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Row 2: Line Navigation -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Previous Line -->
                <div>
                    @if($currentLine > 1 || $pageNumber > 1)
                        <a id="link-line-prev" href="{{ route($learnRouteName ?? 'user.jadwal-belajar.learn', array_merge($learnRouteParams ?? ['jadwal' => $jadwal->id ?? null], [
                            'page' => $currentLine > 1 ? $pageNumber : $pageNumber - 1,
                            'line' => $currentLine > 1 ? $currentLine - 1 : 'last',
                            'chunk' => 'last'
                        ])) }}"
                           class="w-full flex items-center justify-center px-3 py-3 bg-blue-50 border-2 border-blue-200 text-blue-700 font-medium rounded-lg hover:bg-blue-100 transition-colors focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
                           aria-label="Baris sebelumnya">
                            <i class="fas fa-chevron-up mr-2" aria-hidden="true"></i>
                            <span class="whitespace-nowrap">Baris Sebelumnya</span>
                        </a>
                    @else
                        <button disabled class="w-full flex items-center justify-center px-3 py-3 bg-gray-100 text-gray-400 font-medium rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-up mr-2" aria-hidden="true"></i>
                            <span class="whitespace-nowrap">Baris Sebelumnya</span>
                        </button>
                    @endif
                </div>

                <!-- Next Line -->
                <div>
                    @if($hasNextLine || $pageNumber < $totalPages)
                        <a id="link-line-next" href="{{ route($learnRouteName ?? 'user.jadwal-belajar.learn', array_merge($learnRouteParams ?? ['jadwal' => $jadwal->id ?? null], [
                            'page' => $hasNextLine ? $pageNumber : $pageNumber + 1,
                            'line' => $hasNextLine ? $currentLine + 1 : 1,
                            'chunk' => 1
                        ])) }}" 
                           class="w-full flex items-center justify-center px-3 py-3 bg-blue-50 border-2 border-blue-200 text-blue-700 font-medium rounded-lg hover:bg-blue-100 transition-colors focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
                           aria-label="Baris selanjutnya">
                            <span class="whitespace-nowrap">Baris Berikutnya</span>
                            <i class="fas fa-chevron-down ml-2" aria-hidden="true"></i>
                        </a>
                    @else
                        <button disabled class="w-full flex items-center justify-center px-3 py-3 bg-gray-100 text-gray-400 font-medium rounded-lg cursor-not-allowed">
                            <span class="whitespace-nowrap">Baris Berikutnya</span>
                            <i class="fas fa-chevron-down ml-2" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Row 3: Page Navigation -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Previous Page -->
                <div>
                    @if($pageNumber > 1)
                        <a id="link-page-prev" href="{{ route($learnRouteName ?? 'user.jadwal-belajar.learn', array_merge($learnRouteParams ?? ['jadwal' => $jadwal->id ?? null], ['page' => $pageNumber - 1, 'line' => 1, 'chunk' => 1])) }}"
                           class="w-full flex items-center justify-center px-3 py-3 bg-white border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                           aria-label="Halaman sebelumnya">
                            <i class="fas fa-chevron-double-left mr-2" aria-hidden="true"></i>
                            <span class="whitespace-nowrap">Halaman Sebelumnya</span>
                        </a>
                    @else
                        <button disabled class="w-full flex items-center justify-center px-3 py-3 bg-gray-100 text-gray-400 font-medium rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-double-left mr-2" aria-hidden="true"></i>
                            <span class="whitespace-nowrap">Halaman Sebelumnya</span>
                        </button>
                    @endif
                </div>

                <!-- Next Page / Complete -->
                <div>
                    @if($pageNumber < $totalPages)
                        <a id="link-page-next" href="{{ route($learnRouteName ?? 'user.jadwal-belajar.learn', array_merge($learnRouteParams ?? ['jadwal' => $jadwal->id ?? null], ['page' => $pageNumber + 1, 'line' => 1, 'chunk' => 1])) }}" 
                           class="w-full flex items-center justify-center px-3 py-3 bg-white border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                           aria-label="Halaman selanjutnya">
                            <span class="whitespace-nowrap">Halaman Berikutnya</span>
                            <i class="fas fa-chevron-double-right ml-2" aria-hidden="true"></i>
                        </a>
                    @else
                        @if(!empty($sessionCompleteRoute))
                        <a href="{{ $sessionCompleteRoute }}" 
                           class="w-full flex items-center justify-center px-3 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                           aria-label="Selesai belajar">
                            <span class="whitespace-nowrap">Selesai Belajar</span>
                            <i class="fas fa-check-circle ml-2" aria-hidden="true"></i>
                        </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- MQTT Status -->
        <div id="mqtt-status" 
             class="mt-6 p-3 rounded-lg text-center text-sm font-medium"
             role="status"
             aria-live="polite">
            <i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>
            Menghubungkan ke MQTT...
        </div>
    </div>

    <!-- Full Text Preview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pratinjau Teks Lengkap</h3>
        <div class="space-y-2">
            @foreach($originalLines as $index => $line)
                <div class="p-2 rounded {{ $index + 1 == $currentLine ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                    @if($index + 1 == $currentLine)
                        <strong>Baris {{ $index + 1 }}:</strong> {{ $line }}
                    @else
                        <span class="text-gray-600">Baris {{ $index + 1 }}:</span> {{ $line }}
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Keyboard Shortcuts Info -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Pintasan Keyboard:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm text-blue-800">
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">←</kbd> Chunk sebelumnya</div>
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">→</kbd> Chunk berikutnya</div>
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">↑</kbd> Baris sebelumnya</div>
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">↓</kbd> Baris berikutnya</div>
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">Page Up</kbd> Halaman berikutnya</div>
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">Page Down</kbd> Halaman sebelumnya</div>
            <div><kbd class="px-2 py-1 bg-white rounded border border-blue-300">Space</kbd> Baca karakter saat ini</div>
        </div>
    </div>

    <!-- Tombol Selesai Belajar -->
    @if(!empty($sessionCompleteRoute))
    <div class="mt-6 text-center">
        <form id="complete-session-form" action="{{ $sessionCompleteRoute }}" method="POST" class="inline-block">
            @csrf
            @if(isset($sessionCompleteMethod) && strtolower($sessionCompleteMethod) !== 'post')
                @method($sessionCompleteMethod)
            @endif
            <button type="submit" 
                    class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    aria-label="Selesaikan sesi belajar">
                <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>
                Selesai Belajar
            </button>
        </form>
    </div>
    @endif
</div>

<!-- Live Region for Screen Reader -->
<div aria-live="assertive" aria-atomic="true" class="sr-only" id="announcements"></div>

@push('styles')
<style>
.braille-dot-row {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}

.braille-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #1F2937;
}

.braille-dot-empty {
    width: 16px;
    height: 16px;
}

.active-char {
    color: #10B981;
    font-weight: bold;
    background: #D1FAE5;
    border-radius: 4px;
    padding: 2px 8px;
}
</style>
@endpush

@push('scripts')
<!-- MQTT Library -->
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

<script>
// Inject braille data from controller
const brailleData = {
    current_page: {{ $pageNumber }},
    current_line_index: {{ $currentLineIndex }},
    total_pages: {{ $totalPages }},
    total_lines: {{ $totalLines }},
    lines: {!! json_encode($lines) !!},
    material_title: {!! json_encode($material->judul) !!},
    material_description: {!! json_encode($material->deskripsi ?? '') !!},
    current_line_text: {!! json_encode($currentLineText) !!},
    braille_patterns: {!! json_encode($braillePatterns ?? {}) !!},
    braille_binary_patterns: {!! json_encode($brailleBinaryPatterns ?? {}) !!},
    braille_decimal_patterns: {!! json_encode($brailleDecimalPatterns ?? {}) !!},
    current_chunk_text: {!! json_encode($currentChunkText) !!},
    current_chunk_decimal_values: {!! json_encode($currentChunkDecimalValues ?? []) !!}
};

const navigateUrl = {!! json_encode(isset($navigateRouteName) ? route($navigateRouteName, $navigateRouteParams ?? []) : route('user.jadwal-belajar.navigate', ['jadwal' => $jadwal->id ?? null])) !!};
const materialPageUrl = {!! json_encode(isset($materialPageRouteName) ? route($materialPageRouteName, $materialPageParams ?? []) : route('user.jadwal-belajar.material-page', ['jadwal' => $jadwal->id ?? null])) !!};
const selectedDeviceIds = {!! json_encode($selectedDeviceIds ?? ($jadwal->devices->pluck('id')->all() ?? [])) !!};
const selectedDeviceSerials = {!! json_encode($selectedDeviceSerials ?? ($jadwal->devices->pluck('serial_number')->all() ?? [])) !!};
const buttonNavigationEnabled = {!! json_encode($buttonNavigationEnabled ?? false) !!};
const buttonTopic = {!! json_encode($buttonTopic ?? null) !!};

// MQTT Configuration
const mqttUrl = '{{ config('mqtt.ws_url') }}';
const mqttTopic = '{{ config('mqtt.topics.device_button') }}';
const mqttPublishTopic = '{{ config('mqtt.topics.device_control') }}';
const mqttClient = mqtt.connect(mqttUrl, {
    @if(config('mqtt.username'))
    username: '{{ config('mqtt.username') }}',
    @endif
    @if(config('mqtt.password'))
    password: '{{ config('mqtt.password') }}',
    @endif
});

let currentIndex = 0;
let currentPage = brailleData.current_page || 1;
let currentLineIndex = brailleData.current_line_index || 0;
let currentLineText = brailleData.current_line_text || '';
let totalLines = brailleData.total_lines || 1;

// Debug: Log data to console
console.log('Braille Data:', brailleData);
console.log('Space unicode from DB:', brailleData.braille_patterns ? brailleData.braille_patterns[' '] : 'Not found');

// MQTT Connection Handlers
mqttClient.on('connect', function() {
    updateMqttStatus('Terhubung ke MQTT Broker', false);
    if (mqttTopic) {
        mqttClient.subscribe(mqttTopic);
    }

    if (buttonNavigationEnabled && buttonTopic) {
        mqttClient.subscribe(buttonTopic);
        console.log('Subscribed to device button topic:', buttonTopic);
    }
});

mqttClient.on('error', function(err) {
    updateMqttStatus('Kesalahan MQTT: ' + err.message, true);
});

mqttClient.on('offline', function() {
    updateMqttStatus('Koneksi MQTT terputus', true);
});

mqttClient.on('message', function(topic, message) {
    const payload = message ? message.toString().trim() : '';
    console.log('MQTT message received', { topic, payload });

    if (!payload) {
        return;
    }

    if (!buttonNavigationEnabled || !buttonTopic) {
        updateMqttStatus('Payload tombol diterima (navigasi non-aktif): ' + payload, false);
        return;
    }

    if (topic !== buttonTopic) {
        console.log('Payload tidak untuk topik tombol, diabaikan');
        return;
    }

    try {
        updateMqttStatus('Payload tombol diterima: ' + payload, false);

        const actionMap = {
            '1': 'link-page-prev',
            '4': 'link-page-next',
            '2': 'link-line-prev',
            '5': 'link-line-next',
            '3': 'link-chunk-prev',
            '6': 'link-chunk-next'
        };

        const targetLink = actionMap[payload] ?? null;

        if (targetLink) {
            const handled = triggerNavigation(targetLink);
            if (!handled) {
                console.log('Navigation action ignored for payload:', payload);
            }
        } else {
            console.log('Unknown button payload received:', payload);
        }
    } catch (error) {
        console.error('Failed to handle button payload:', error);
    }
});

function updateMqttStatus(message, isError = false) {
    const statusEl = document.getElementById('mqtt-status');
    statusEl.innerHTML = isError 
        ? '<i class="fas fa-exclamation-circle mr-2"></i>' + message
        : '<i class="fas fa-check-circle mr-2"></i>' + message;
    statusEl.className = 'mt-6 p-3 rounded-lg text-center text-sm font-medium ' + 
        (isError ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800');
    
    // Announce to screen reader
    document.getElementById('announcements').textContent = message;
}

// Convert braille binary to decimal for MQTT
function brailleToDecimal(binary6) {
    const reversed = binary6.split('').reverse().join('');
    const first3 = reversed.substring(0, 3);
    const last3 = reversed.substring(3, 6);
    const dec1 = parseInt(first3, 2);
    const dec2 = parseInt(last3, 2);
    return dec1.toString().padStart(1, '0') + dec2.toString().padStart(1, '0');
}

// Update braille unicode pattern
function updateBrailleUnicodePattern(character) {
    const brailleElement = document.getElementById('braille-unicode-pattern');
    if (!brailleElement) {
        console.warn('Element with ID "braille-unicode-pattern" not found');
        return;
    }
    
    const unicodePattern = getBrailleUnicodeForChar(character);
    // For space, show empty cell instead of space unicode
    if (character === ' ') {
        brailleElement.textContent = '⠀'; // Braille blank
        brailleElement.style.background = '';
        brailleElement.style.border = '';
    } else {
        brailleElement.textContent = unicodePattern;
        brailleElement.style.background = '';
        brailleElement.style.border = '';
    }
}

// Get braille unicode for character from database
function getBrailleUnicodeForChar(character) {
    if (character === ' ') {
        return '\u2800'; // Braille blank for space
    }

    if (brailleData.braille_patterns && brailleData.braille_patterns[character]) {
        return brailleData.braille_patterns[character];
    }
    return '\u2800'; // Default to space
}

function getBrailleBinaryForChar(character) {
    if (character === ' ') {
        return '000000';
    }

    if (brailleData.braille_binary_patterns && brailleData.braille_binary_patterns[character]) {
        return brailleData.braille_binary_patterns[character];
    }

    return '000000';
}

function getBrailleDecimalForChar(character) {
    if (character === ' ') {
        return 0;
    }

    if (brailleData.braille_decimal_patterns && typeof brailleData.braille_decimal_patterns[character] !== 'undefined') {
        return brailleData.braille_decimal_patterns[character];
    }

    return 0;
}

function fetchPageData(pageNumber, lineNumber = 1) {
    const safePage = Math.max(1, pageNumber);
    const payload = {
        page: safePage,
        line: Math.max(1, lineNumber)
    };

    return fetch(materialPageUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to fetch page data');
        }
        return response.json();
    })
    .then(result => {
        if (!result.success || !result.data) {
            throw new Error(result.error || 'Invalid response');
        }

        currentPage = result.data.current_page;
        currentLineIndex = result.data.current_line_index;
        totalLines = result.data.total_lines;
        brailleData.lines = result.data.lines;
        brailleData.total_pages = result.data.total_pages;
        currentLineText = result.data.current_line_text || '';

        if (result.data.braille_patterns) {
            brailleData.braille_patterns = result.data.braille_patterns;
        }
        if (result.data.braille_binary_patterns) {
            brailleData.braille_binary_patterns = result.data.braille_binary_patterns;
        }
        if (result.data.braille_decimal_patterns) {
            brailleData.braille_decimal_patterns = result.data.braille_decimal_patterns;
        }

        currentIndex = 0;
        updateView();
    })
    .catch(error => {
        console.error('Error fetching page data:', error);
        updateMqttStatus('Gagal memuat halaman: ' + error.message, true);
    });
}

// Update display
function updateView() {
    console.log('updateView called - currentLineText:', currentLineText);
    
    if (currentLineText && currentLineText.length > 0) {
        const characters = currentLineText.split('');
        currentIndex = Math.max(0, Math.min(currentIndex, characters.length - 1));
        
        const currentChar = characters[currentIndex];
        console.log('Current character:', currentChar);
        console.log('Character code:', currentChar.charCodeAt(0));
        
        const brailleUnicode = getBrailleUnicodeForChar(currentChar);
        console.log('Braille unicode for "' + currentChar + '":', brailleUnicode);
        const brailleDotsElement = document.getElementById('braille-dots');
        if (brailleDotsElement) {
            brailleDotsElement.innerHTML = brailleUnicode;
        }

        const brailleBinary = getBrailleBinaryForChar(currentChar);
        const brailleDecimal = getBrailleDecimalForChar(currentChar);
        console.log('Braille binary for "' + currentChar + '":', brailleBinary);
        console.log('Braille decimal for "' + currentChar + '":', brailleDecimal);
        
        const brailleCharElement = document.getElementById('braille-character');
        if (brailleCharElement) {
            brailleCharElement.textContent = currentChar;
        }
        
        const pageInfoElement = document.getElementById('page-info');
        if (pageInfoElement) {
            pageInfoElement.textContent = 
                `Halaman ${currentPage} • Baris ${currentLineIndex + 1} dari ${totalLines} • Karakter ${currentIndex + 1} dari ${characters.length}`;
        }
        
        const originalTextElement = document.getElementById('original-text');
        if (originalTextElement) {
            originalTextElement.innerHTML = characters.map((char, i) => {
                return i === currentIndex 
                    ? `<span class="active-char">${char}</span>`
                    : char;
            }).join('');
        }
        
        updateBrailleUnicodePattern(currentChar);
        
        if (window.mqttClient && mqttClient.connected) {
            const targetTopic = mqttPublishTopic || mqttTopic;
            if (!targetTopic) {
                updateMqttStatus('Topik publish MQTT tidak tersedia', true);
                return;
            }
            try {
                const decimalValue = typeof brailleDecimal !== 'undefined' && brailleDecimal !== null
                    ? String(brailleDecimal)
                    : brailleToDecimal(brailleBinary);
                mqttClient.publish(targetTopic, decimalValue, { qos: 1 }, function(err) {
                    if (err) {
                        updateMqttStatus('Gagal mengirim: ' + err.message, true);
                    } else {
                        updateMqttStatus(`Terkirim: ${currentChar}`, false);
                    }
                });
            } catch (e) {
                updateMqttStatus('Kesalahan konversi: ' + e.message, true);
            }
        }
    } else {
        console.log('No data available');
        
        const brailleDotsElement = document.getElementById('braille-dots');
        if (brailleDotsElement) {
            brailleDotsElement.innerHTML = '';
        }
        
        const brailleCharElement = document.getElementById('braille-character');
        if (brailleCharElement) {
            brailleCharElement.textContent = '';
        }
        
        const pageInfoElement = document.getElementById('page-info');
        if (pageInfoElement) {
            pageInfoElement.textContent = 'Tidak ada data tersedia';
        }
        
        const originalTextElement = document.getElementById('original-text');
        if (originalTextElement) {
            originalTextElement.innerHTML = '';
        }
        
        updateBrailleUnicodePattern(' ');
    }
}

// Text to speech
const speechSupported = 'speechSynthesis' in window && typeof SpeechSynthesisUtterance !== 'undefined';
let speechStatusElement = null;
let activeUtterance = null;

function updateSpeechStatus(message) {
    if (speechStatusElement) {
        speechStatusElement.textContent = message;
    }
    const announceEl = document.getElementById('announcements');
    if (announceEl) {
        announceEl.textContent = message;
    }
}

function toggleSpeechPlayback() {
    if (!speechSupported) {
        updateSpeechStatus('Text to speech tidak didukung di browser ini.');
        return;
    }

    const synth = window.speechSynthesis;

    if (synth.speaking || synth.pending || synth.paused) {
        synth.cancel();
        updateSpeechStatus('Pengucapan dihentikan.');
        return;
    }

    let textToSpeak = (currentLineText || '').trim();
    if (!textToSpeak) {
        textToSpeak = (brailleData.current_chunk_text || '').trim();
    }

    if (!textToSpeak) {
        updateSpeechStatus('Tidak ada teks untuk dibacakan.');
        return;
    }

    activeUtterance = new SpeechSynthesisUtterance(textToSpeak);
    activeUtterance.lang = 'id-ID';
    activeUtterance.rate = 0.95;

    const voices = synth.getVoices();
    const indoVoice = voices.find(v => v.lang && v.lang.startsWith('id'));
    if (indoVoice) {
        activeUtterance.voice = indoVoice;
    }

    activeUtterance.onstart = () => updateSpeechStatus('Membacakan teks...');
    activeUtterance.onend = () => updateSpeechStatus('Pengucapan selesai.');
    activeUtterance.onerror = () => updateSpeechStatus('Gagal membacakan teks.');

    synth.cancel();
    synth.speak(activeUtterance);
}

function triggerNavigation(linkId) {
    const element = document.getElementById(linkId);

    if (!element || element.tagName.toLowerCase() !== 'a') {
        return false;
    }

    const href = element.getAttribute('href');
    if (!href || element.classList.contains('disabled')) {
        return false;
    }

    element.click();
    return true;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    speechStatusElement = document.getElementById('speech-status');
    if (!speechSupported && speechStatusElement) {
        speechStatusElement.textContent = 'Browser Anda tidak mendukung text to speech.';
    }

    // Button controls
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnRead = document.getElementById('btn-read');
    const btnLinePrev = document.getElementById('btn-line-prev');
    const btnLineNext = document.getElementById('btn-line-next');
    const btnPagePrev = document.getElementById('btn-page-prev');

    if (btnPrev) {
        btnPrev.onclick = function() {
            if (currentLineText) {
                const characters = currentLineText.split('');
                currentIndex = Math.max(0, currentIndex - 1);
            }
            updateView();
        };
    }
    
    if (btnNext) {
        btnNext.onclick = function() {
            if (currentLineText) {
                const characters = currentLineText.split('');
                currentIndex = Math.min(characters.length - 1, currentIndex + 1);
            }
            updateView();
        };
    }

    if (btnRead) {
        btnRead.onclick = toggleSpeechPlayback;
    }
    
    // Line navigation
    if (btnLinePrev) {
        btnLinePrev.onclick = function() {
            if (brailleData.lines && currentLineIndex > 0) {
                currentLineIndex--;
                currentLineText = brailleData.lines[currentLineIndex] || '';
                currentIndex = 0; // Reset character index when changing line
                updateView();
            }
        };
    }
    
    if (btnLineNext) {
        btnLineNext.onclick = function() {
            if (brailleData.lines && currentLineIndex < totalLines - 1) {
                currentLineIndex++;
                currentLineText = brailleData.lines[currentLineIndex] || '';
                currentIndex = 0; // Reset character index when changing line
                updateView();
            }
        };
    }
    
    if (btnPagePrev) {
        btnPagePrev.onclick = function() {
            if (currentPage > 1) {
                fetchPageData(currentPage - 1);
            }
        };
    }

    const btnPageNext = document.getElementById('btn-page-next');
    if (btnPageNext) {
        btnPageNext.onclick = function() {
            if (currentPage < (brailleData.total_pages || 1)) {
                fetchPageData(currentPage + 1);
            }
        };
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (['INPUT', 'TEXTAREA', 'SELECT', 'BUTTON'].includes(e.target.tagName) || e.target.isContentEditable) {
            return;
        }

        let handled = false;

        switch(e.key) {
            case 'ArrowLeft':
                handled = triggerNavigation('link-chunk-prev');
                break;
            case 'ArrowRight':
                handled = triggerNavigation('link-chunk-next');
                break;
            case 'ArrowUp':
                handled = triggerNavigation('link-line-prev');
                break;
            case 'ArrowDown':
                handled = triggerNavigation('link-line-next');
                break;
            case 'PageUp':
                handled = triggerNavigation('link-page-next');
                break;
            case 'PageDown':
                handled = triggerNavigation('link-page-prev');
                break;
            case ' ':
                toggleSpeechPlayback();
                handled = true;
                break;
        }

        if (handled) {
            e.preventDefault();
        }
    });
    
    // Load voices
    window.speechSynthesis.onvoiceschanged = function() {};
    
    // Initial render
    updateView();
    
    // Initialize braille unicode pattern
    if (currentLineText) {
        const firstChar = currentLineText.charAt(0);
        updateBrailleUnicodePattern(firstChar);
    }
    
    // Announce page load
    setTimeout(() => {
        document.getElementById('announcements').textContent = 
            'Halaman pembelajaran siap. Gunakan tombol atau keyboard untuk navigasi.';
    }, 1000);
});

// Handle complete session form submission
const completeSessionForm = document.getElementById('complete-session-form');
if (completeSessionForm) {
    completeSessionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        const formData = new FormData(this);
        
        // Disable button and show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyelesaikan...';
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            alert('Terjadi kesalahan saat menyelesaikan sesi. Silakan coba lagi.');
        });
    });
}

// Cleanup on page leave
window.addEventListener('beforeunload', function() {
    if (mqttClient && mqttClient.connected) {
        mqttClient.end();
    }
});

const initialChunkText = brailleData.current_chunk_text || '';
const initialChunkDecimals = Array.isArray(brailleData.current_chunk_decimal_values)
    ? brailleData.current_chunk_decimal_values
    : [];

let lastSentSignature = '';
let isSending = false;

// Function to send chunk decimal values to devices
// Function to send chunk decimal values to devices
async function sendToDevices(chunkText, decimalValues) {
    const decimals = Array.isArray(decimalValues) ? decimalValues : [];
    
    // PERBAIKAN: Pastikan format decimal values konsisten
    // Setiap nilai harus 2 digit dengan padding '0' di depan
    const paddedDecimals = decimals.map(val => {
        const numStr = String(val);
        return numStr.padStart(2, '0'); // Pastikan selalu 2 digit: 1 -> '01', 12 -> '12'
    });
    
    // PENTING: Cek format yang diharapkan perangkat
    // Opsi 1: Array tetap array
    const decimalArray = paddedDecimals;
    
    // Opsi 2: Concatenated string tanpa spasi (misalnya: "011205")
    const decimalString = paddedDecimals.join('');
    
    // Opsi 3: String dengan spasi (misalnya: "01 12 05")
    const decimalStringSpaced = paddedDecimals.join(' ');
    
    const signature = JSON.stringify({ chunkText, decimals: paddedDecimals });

    if ((typeof chunkText !== 'string' || chunkText.length === 0) && paddedDecimals.length === 0) {
        return;
    }

    if (signature === lastSentSignature || isSending) {
        return;
    }

    const targetTopic = mqttPublishTopic || mqttTopic;

    if (!targetTopic) {
        console.warn('MQTT publish topic is not configured.');
        return;
    }

    if (!mqttClient || typeof mqttClient.publish !== 'function') {
        console.warn('MQTT client is not available.');
        return;
    }

    if (!mqttClient.connected) {
        updateMqttStatus('Belum terhubung ke MQTT', true);
        return;
    }

    isSending = true;
    lastSentSignature = signature;
    
    const payload = decimalString;

    try {
        await new Promise((resolve, reject) => {
            mqttClient.publish(targetTopic, payload, { qos: 1 }, function(err) {
                if (err) {
                    reject(err);
                } else {
                    resolve();
                }
            });
            // mqttClient.publish('abatago/01/control', payload, { qos: 1 }, function(err) {
            //     if (err) {
            //         reject(err);
            //     } else {
            //         resolve();
            //     }
            // });
        });

        console.log('Decimal string sent via MQTT:', payload);

        updateMqttStatus('Teks dikirim ke perangkat', false);

        const mqttStatus = document.getElementById('mqtt-status');
        if (mqttStatus) {
            setTimeout(() => {
                mqttStatus.textContent = 'Siap mengirim teks ke perangkat';
                mqttStatus.className = 'mt-6 p-3 rounded-lg text-center text-sm font-medium bg-gray-100 text-gray-700';
            }, 3000);
        }
    } catch (error) {
        console.error('Failed to send text via MQTT:', error);
        updateMqttStatus('Gagal mengirim teks ke perangkat', true);
    } finally {
        isSending = false;
    }
}

// Initial send of current chunk decimal values
setTimeout(() => {
    sendToDevices(initialChunkText, initialChunkDecimals);
}, 1000);
</script>
@endpush
@endsection