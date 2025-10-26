<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BraillePatternsSeeder extends Seeder
{
    public function run()
    {
        // Hapus semua data lama sebelum insert baru
        DB::table('braille_patterns')->truncate();

        $patterns = [
            // =====================
            // HURUF LATIN (A–Z)
            // =====================
            ['category' => 'Latin', 'character' => 'A', 'braille_unicode' => '⠁', 'dots_decimal' => '32', 'dots_binary' => '100000'],
            ['category' => 'Latin', 'character' => 'B', 'braille_unicode' => '⠃', 'dots_decimal' => '48', 'dots_binary' => '110000'],
            ['category' => 'Latin', 'character' => 'C', 'braille_unicode' => '⠉', 'dots_decimal' => '36', 'dots_binary' => '100100'],
            ['category' => 'Latin', 'character' => 'D', 'braille_unicode' => '⠙', 'dots_decimal' => '38', 'dots_binary' => '100110'],
            ['category' => 'Latin', 'character' => 'E', 'braille_unicode' => '⠑', 'dots_decimal' => '34', 'dots_binary' => '100010'],
            ['category' => 'Latin', 'character' => 'F', 'braille_unicode' => '⠋', 'dots_decimal' => '52', 'dots_binary' => '110100'],
            ['category' => 'Latin', 'character' => 'G', 'braille_unicode' => '⠛', 'dots_decimal' => '54', 'dots_binary' => '110110'],
            ['category' => 'Latin', 'character' => 'H', 'braille_unicode' => '⠓', 'dots_decimal' => '50', 'dots_binary' => '110010'],
            ['category' => 'Latin', 'character' => 'I', 'braille_unicode' => '⠊', 'dots_decimal' => '20', 'dots_binary' => '010100'],
            ['category' => 'Latin', 'character' => 'J', 'braille_unicode' => '⠚', 'dots_decimal' => '22', 'dots_binary' => '010110'],
            ['category' => 'Latin', 'character' => 'K', 'braille_unicode' => '⠅', 'dots_decimal' => '40', 'dots_binary' => '101000'],
            ['category' => 'Latin', 'character' => 'L', 'braille_unicode' => '⠇', 'dots_decimal' => '56', 'dots_binary' => '111000'],
            ['category' => 'Latin', 'character' => 'M', 'braille_unicode' => '⠍', 'dots_decimal' => '44', 'dots_binary' => '101100'],
            ['category' => 'Latin', 'character' => 'N', 'braille_unicode' => '⠝', 'dots_decimal' => '46', 'dots_binary' => '101110'],
            ['category' => 'Latin', 'character' => 'O', 'braille_unicode' => '⠕', 'dots_decimal' => '42', 'dots_binary' => '101010'],
            ['category' => 'Latin', 'character' => 'P', 'braille_unicode' => '⠏', 'dots_decimal' => '60', 'dots_binary' => '111100'],
            ['category' => 'Latin', 'character' => 'Q', 'braille_unicode' => '⠟', 'dots_decimal' => '62', 'dots_binary' => '111110'],
            ['category' => 'Latin', 'character' => 'R', 'braille_unicode' => '⠗', 'dots_decimal' => '58', 'dots_binary' => '111010'],
            ['category' => 'Latin', 'character' => 'S', 'braille_unicode' => '⠎', 'dots_decimal' => '28', 'dots_binary' => '011100'],
            ['category' => 'Latin', 'character' => 'T', 'braille_unicode' => '⠞', 'dots_decimal' => '30', 'dots_binary' => '011110'],
            ['category' => 'Latin', 'character' => 'U', 'braille_unicode' => '⠥', 'dots_decimal' => '41', 'dots_binary' => '101001'],
            ['category' => 'Latin', 'character' => 'V', 'braille_unicode' => '⠧', 'dots_decimal' => '57', 'dots_binary' => '111001'],
            ['category' => 'Latin', 'character' => 'W', 'braille_unicode' => '⠺', 'dots_decimal' => '23', 'dots_binary' => '010111'],
            ['category' => 'Latin', 'character' => 'X', 'braille_unicode' => '⠭', 'dots_decimal' => '45', 'dots_binary' => '110011'],
            ['category' => 'Latin', 'character' => 'Y', 'braille_unicode' => '⠽', 'dots_decimal' => '47', 'dots_binary' => '110111'],
            ['category' => 'Latin', 'character' => 'Z', 'braille_unicode' => '⠵', 'dots_decimal' => '43', 'dots_binary' => '100111'],
            ['category' => 'Latin', 'character' => 'a', 'braille_unicode' => '⠁', 'dots_decimal' => '32', 'dots_binary' => '100000'],
            ['category' => 'Latin', 'character' => 'b', 'braille_unicode' => '⠃', 'dots_decimal' => '48', 'dots_binary' => '110000'],
            ['category' => 'Latin', 'character' => 'c', 'braille_unicode' => '⠉', 'dots_decimal' => '36', 'dots_binary' => '100100'],
            ['category' => 'Latin', 'character' => 'd', 'braille_unicode' => '⠙', 'dots_decimal' => '38', 'dots_binary' => '100110'],
            ['category' => 'Latin', 'character' => 'e', 'braille_unicode' => '⠑', 'dots_decimal' => '34', 'dots_binary' => '100010'],
            ['category' => 'Latin', 'character' => 'f', 'braille_unicode' => '⠋', 'dots_decimal' => '52', 'dots_binary' => '110100'],
            ['category' => 'Latin', 'character' => 'g', 'braille_unicode' => '⠛', 'dots_decimal' => '54', 'dots_binary' => '110110'],
            ['category' => 'Latin', 'character' => 'h', 'braille_unicode' => '⠓', 'dots_decimal' => '50', 'dots_binary' => '110010'],
            ['category' => 'Latin', 'character' => 'i', 'braille_unicode' => '⠊', 'dots_decimal' => '20', 'dots_binary' => '010100'],
            ['category' => 'Latin', 'character' => 'j', 'braille_unicode' => '⠚', 'dots_decimal' => '22', 'dots_binary' => '010110'],
            ['category' => 'Latin', 'character' => 'k', 'braille_unicode' => '⠅', 'dots_decimal' => '40', 'dots_binary' => '101000'],
            ['category' => 'Latin', 'character' => 'l', 'braille_unicode' => '⠇', 'dots_decimal' => '56', 'dots_binary' => '111000'],
            ['category' => 'Latin', 'character' => 'm', 'braille_unicode' => '⠍', 'dots_decimal' => '44', 'dots_binary' => '101100'],
            ['category' => 'Latin', 'character' => 'n', 'braille_unicode' => '⠝', 'dots_decimal' => '46', 'dots_binary' => '101110'],
            ['category' => 'Latin', 'character' => 'o', 'braille_unicode' => '⠕', 'dots_decimal' => '42', 'dots_binary' => '101010'],
            ['category' => 'Latin', 'character' => 'p', 'braille_unicode' => '⠏', 'dots_decimal' => '60', 'dots_binary' => '111100'],
            ['category' => 'Latin', 'character' => 'q', 'braille_unicode' => '⠟', 'dots_decimal' => '62', 'dots_binary' => '111110'],
            ['category' => 'Latin', 'character' => 'r', 'braille_unicode' => '⠗', 'dots_decimal' => '58', 'dots_binary' => '111010'],
            ['category' => 'Latin', 'character' => 's', 'braille_unicode' => '⠎', 'dots_decimal' => '28', 'dots_binary' => '011100'],
            ['category' => 'Latin', 'character' => 't', 'braille_unicode' => '⠞', 'dots_decimal' => '30', 'dots_binary' => '011110'],
            ['category' => 'Latin', 'character' => 'u', 'braille_unicode' => '⠥', 'dots_decimal' => '41', 'dots_binary' => '101001'],
            ['category' => 'Latin', 'character' => 'v', 'braille_unicode' => '⠧', 'dots_decimal' => '57', 'dots_binary' => '111001'],
            ['category' => 'Latin', 'character' => 'w', 'braille_unicode' => '⠺', 'dots_decimal' => '23', 'dots_binary' => '010111'],
            ['category' => 'Latin', 'character' => 'x', 'braille_unicode' => '⠭', 'dots_decimal' => '45', 'dots_binary' => '110011'],
            ['category' => 'Latin', 'character' => 'y', 'braille_unicode' => '⠽', 'dots_decimal' => '47', 'dots_binary' => '110111'],
            ['category' => 'Latin', 'character' => 'z', 'braille_unicode' => '⠵', 'dots_decimal' => '43', 'dots_binary' => '100111'],
            // =====================
            // ANGKA (0–9)
            // =====================
            ['category' => 'Latin', 'character' => '1', 'braille_unicode' => '⠁', 'dots_decimal' => '32', 'dots_binary' => '100000'],
            ['category' => 'Latin', 'character' => '2', 'braille_unicode' => '⠃', 'dots_decimal' => '48', 'dots_binary' => '110000'],
            ['category' => 'Latin', 'character' => '3', 'braille_unicode' => '⠉', 'dots_decimal' => '36', 'dots_binary' => '100100'],
            ['category' => 'Latin', 'character' => '4', 'braille_unicode' => '⠙', 'dots_decimal' => '38', 'dots_binary' => '100110'],
            ['category' => 'Latin', 'character' => '5', 'braille_unicode' => '⠑', 'dots_decimal' => '34', 'dots_binary' => '100010'],
            ['category' => 'Latin', 'character' => '6', 'braille_unicode' => '⠋', 'dots_decimal' => '52', 'dots_binary' => '110100'],
            ['category' => 'Latin', 'character' => '7', 'braille_unicode' => '⠛', 'dots_decimal' => '54', 'dots_binary' => '110110'],
            ['category' => 'Latin', 'character' => '8', 'braille_unicode' => '⠓', 'dots_decimal' => '50', 'dots_binary' => '110010'],
            ['category' => 'Latin', 'character' => '9', 'braille_unicode' => '⠊', 'dots_decimal' => '20', 'dots_binary' => '010100'],
            ['category' => 'Latin', 'character' => '0', 'braille_unicode' => '⠚', 'dots_decimal' => '22', 'dots_binary' => '010110'],

            ['category' => 'Simbol', 'character' => '.', 'braille_unicode' => '⠲', 'dots_decimal' => '19', 'dots_binary' => '010011'],    // 0+16+0+0+2+1 = 19
            ['category' => 'Simbol', 'character' => ',', 'braille_unicode' => '⠂', 'dots_decimal' => '16', 'dots_binary' => '010000'],    // 16
            ['category' => 'Simbol', 'character' => ';', 'braille_unicode' => '⠆', 'dots_decimal' => '24', 'dots_binary' => '011000'],    // 16+8 = 24
            ['category' => 'Simbol', 'character' => ':', 'braille_unicode' => '⠒', 'dots_decimal' => '18', 'dots_binary' => '010010'],    // 16+2 = 18
            ['category' => 'Simbol', 'character' => '?', 'braille_unicode' => '⠦', 'dots_decimal' => '25', 'dots_binary' => '011001'],    // 16+8+1 = 25
            ['category' => 'Simbol', 'character' => '!', 'braille_unicode' => '⠖', 'dots_decimal' => '26', 'dots_binary' => '011010'],    // 16+8+2 = 26
            ['category' => 'Simbol', 'character' => '-', 'braille_unicode' => '⠤', 'dots_decimal' => '9',  'dots_binary' => '001001'],     // 8+1 = 9
            ['category' => 'Simbol', 'character' => '(', 'braille_unicode' => '⠣', 'dots_decimal' => '49', 'dots_binary' => '110001'],    // 32+16+1 = 49
            ['category' => 'Simbol', 'character' => ')', 'braille_unicode' => '⠜', 'dots_decimal' => '14', 'dots_binary' => '001110'],    // 8+4+2 = 14
            ['category' => 'Simbol', 'character' => '/', 'braille_unicode' => '⠌', 'dots_decimal' => '12', 'dots_binary' => '001100'],    // 8+4 = 12
            ['category' => 'Simbol', 'character' => '+', 'braille_unicode' => '⠬', 'dots_decimal' => '13', 'dots_binary' => '001101'],    // 8+4+1 = 13
            ['category' => 'Simbol', 'character' => '=', 'braille_unicode' => '⠶', 'dots_decimal' => '27', 'dots_binary' => '011011'],    // 16+8+2+1 = 27
            ['category' => 'Simbol', 'character' => '*', 'braille_unicode' => '⠔', 'dots_decimal' => '10', 'dots_binary' => '001010'],    // 8+2 = 10
            ['category' => 'Simbol', 'character' => '@', 'braille_unicode' => '⠈', 'dots_decimal' => '4',  'dots_binary' => '000100'],     // 4
            ['category' => 'Simbol', 'character' => '#', 'braille_unicode' => '⠼', 'dots_decimal' => '15', 'dots_binary' => '001111'],    // 8+4+2+1 = 15
            ['category' => 'Simbol', 'character' => '&', 'braille_unicode' => '⠯', 'dots_decimal' => '61', 'dots_binary' => '111101'],    // 32+16+8+4+0+1 = 61
            ['category' => 'Simbol', 'character' => '<', 'braille_unicode' => '⠣', 'dots_decimal' => '49', 'dots_binary' => '110001'],    // same as '(' -> 49
            ['category' => 'Simbol', 'character' => '>', 'braille_unicode' => '⠜', 'dots_decimal' => '14', 'dots_binary' => '001110'],    // same as ')' -> 14
            ['category' => 'Simbol', 'character' => '[', 'braille_unicode' => '⠷', 'dots_decimal' => '59', 'dots_binary' => '111011'],    // 32+16+8+0+2+1 = 59
            ['category' => 'Simbol', 'character' => ']', 'braille_unicode' => '⠾', 'dots_decimal' => '31', 'dots_binary' => '011111'],    // 16+8+4+2+1 = 31
            ['category' => 'Simbol', 'character' => '"', 'braille_unicode' => '⠦', 'dots_decimal' => '25', 'dots_binary' => '011001'],    // same as '?' -> 25
            // ['category' => 'Simbol', 'character' => \"'\", 'braille_unicode' => '⠄', 'dots_decimal' => '8',  'dots_binary' => '001000'],     // 8
            ['category' => 'Simbol', 'character' => '\\\\', 'braille_unicode' => '⠡', 'dots_decimal' => '33', 'dots_binary' => '100001'],   // 32+1 = 33
        ];

        // Masukkan semua data ke tabel
        DB::table('braille_patterns')->insert($patterns);
    }
}
