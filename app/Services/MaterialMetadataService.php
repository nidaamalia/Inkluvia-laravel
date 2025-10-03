<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenAI; 

class MaterialMetadataService
{
    /**
     * Generate structured metadata for a material file using OpenAI.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return array{judul:?string, deskripsi:?string, kategori:?string, tingkat:?string}
     */
    public function generateMetadata(UploadedFile $file): array
    {
        $content = $this->extractText($file);

        if (! $content) {
            throw new \RuntimeException('Tidak dapat membaca isi berkas untuk dianalisis AI.');
        }

        $apiKey = Config::get('services.openai.api_key');
        $model = Config::get('services.openai.model', 'gpt-4o-mini');

        if (! $apiKey) {
            throw new \RuntimeException('OpenAI API key belum dikonfigurasi.');
        }

        $client = OpenAI::client($apiKey);
        $prompt = $this->buildPrompt($content);

        $response = $client->responses()->create([
            'model' => $model,
            'input' => $prompt,
            'max_output_tokens' => 400,
            'temperature' => 0.4,
        ]);

        $text = $response->output[0]->content[0]->text ?? null;

        if (! $text) {
            Log::warning('OpenAI tidak mengembalikan teks untuk metadata materi.');
            throw new \RuntimeException('Tidak mendapatkan respons dari OpenAI.');
        }

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            Log::warning('OpenAI merespons format yang tidak valid.', ['response' => $text]);
            throw new \RuntimeException('OpenAI tidak memberikan format metadata yang valid.');
        }

        return [
            'judul' => $decoded['judul'] ?? null,
            'deskripsi' => $decoded['deskripsi'] ?? null,
            'kategori' => $decoded['kategori'] ?? null,
            'tingkat' => $decoded['tingkat'] ?? null,
        ];
    }

    protected function extractText(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        try {
            if (Str::contains($mime, ['text', 'markdown'])) {
                return trim((string) file_get_contents($file->getRealPath()));
            }

            if ($mime === 'application/pdf') {
                // Basic fallback: attempt to read text directly.
                // For better accuracy, consider integrating a dedicated PDF to text parser later.
                return trim((string) shell_exec(sprintf('pdftotext %s -', escapeshellarg($file->getRealPath()))));
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengekstrak teks dari materi.', [
                'mime' => $mime,
                'error' => $e->getMessage(),
            ]);
        }

        return '';
    }

    protected function buildPrompt(string $content): string
    {
        $trimmedContent = Str::limit($content, 4000, '...');

        $prompt = <<<'PROMPT'
Anda adalah asisten untuk platform pembelajaran braille. Berdasarkan konten materi berikut, buat metadata dalam format JSON dengan kunci: "judul", "deskripsi", "kategori", dan "tingkat".
- "kategori" harus salah satu dari: matematika, bahasa, ipa, ips, agama, umum.
- "tingkat" harus salah satu dari: paud, sd, smp, sma, perguruan_tinggi, umum.
- Jika ragu, pilih "umum".
- Batasi deskripsi maksimal 3 kalimat pendek.

Konten materi:
<<<MATERI>>>
PROMPT;

        return str_replace('<<<MATERI>>>', $trimmedContent, $prompt);
    }
}
