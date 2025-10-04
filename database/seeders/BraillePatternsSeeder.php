<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BraillePatternsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patterns = [
            // Huruf Latin Uppercase A-Z
            ['category' => 'latin', 'character' => 'A', 'braille_unicode' => '⠁', 'dots_binary' => '100000', 'dots_decimal' => 32, 'description' => 'Huruf A (Latin besar)'],
            ['category' => 'latin', 'character' => 'B', 'braille_unicode' => '⠃', 'dots_binary' => '110000', 'dots_decimal' => 48, 'description' => 'Huruf B (Latin besar)'],
            ['category' => 'latin', 'character' => 'C', 'braille_unicode' => '⠉', 'dots_binary' => '100100', 'dots_decimal' => 36, 'description' => 'Huruf C (Latin besar)'],
            ['category' => 'latin', 'character' => 'D', 'braille_unicode' => '⠙', 'dots_binary' => '110100', 'dots_decimal' => 52, 'description' => 'Huruf D (Latin besar)'],
            ['category' => 'latin', 'character' => 'E', 'braille_unicode' => '⠑', 'dots_binary' => '100010', 'dots_decimal' => 34, 'description' => 'Huruf E (Latin besar)'],
            ['category' => 'latin', 'character' => 'F', 'braille_unicode' => '⠋', 'dots_binary' => '110010', 'dots_decimal' => 50, 'description' => 'Huruf F (Latin besar)'],
            ['category' => 'latin', 'character' => 'G', 'braille_unicode' => '⠛', 'dots_binary' => '110110', 'dots_decimal' => 54, 'description' => 'Huruf G (Latin besar)'],
            ['category' => 'latin', 'character' => 'H', 'braille_unicode' => '⠓', 'dots_binary' => '100110', 'dots_decimal' => 38, 'description' => 'Huruf H (Latin besar)'],
            ['category' => 'latin', 'character' => 'I', 'braille_unicode' => '⠊', 'dots_binary' => '010100', 'dots_decimal' => 20, 'description' => 'Huruf I (Latin besar)'],
            ['category' => 'latin', 'character' => 'J', 'braille_unicode' => '⠚', 'dots_binary' => '010110', 'dots_decimal' => 22, 'description' => 'Huruf J (Latin besar)'],
            ['category' => 'latin', 'character' => 'K', 'braille_unicode' => '⠅', 'dots_binary' => '101000', 'dots_decimal' => 40, 'description' => 'Huruf K (Latin besar)'],
            ['category' => 'latin', 'character' => 'L', 'braille_unicode' => '⠇', 'dots_binary' => '111000', 'dots_decimal' => 56, 'description' => 'Huruf L (Latin besar)'],
            ['category' => 'latin', 'character' => 'M', 'braille_unicode' => '⠍', 'dots_binary' => '101100', 'dots_decimal' => 44, 'description' => 'Huruf M (Latin besar)'],
            ['category' => 'latin', 'character' => 'N', 'braille_unicode' => '⠝', 'dots_binary' => '111100', 'dots_decimal' => 60, 'description' => 'Huruf N (Latin besar)'],
            ['category' => 'latin', 'character' => 'O', 'braille_unicode' => '⠕', 'dots_binary' => '101010', 'dots_decimal' => 42, 'description' => 'Huruf O (Latin besar)'],
            ['category' => 'latin', 'character' => 'P', 'braille_unicode' => '⠏', 'dots_binary' => '111010', 'dots_decimal' => 58, 'description' => 'Huruf P (Latin besar)'],
            ['category' => 'latin', 'character' => 'Q', 'braille_unicode' => '⠟', 'dots_binary' => '111110', 'dots_decimal' => 62, 'description' => 'Huruf Q (Latin besar)'],
            ['category' => 'latin', 'character' => 'R', 'braille_unicode' => '⠗', 'dots_binary' => '101110', 'dots_decimal' => 46, 'description' => 'Huruf R (Latin besar)'],
            ['category' => 'latin', 'character' => 'S', 'braille_unicode' => '⠎', 'dots_binary' => '011100', 'dots_decimal' => 28, 'description' => 'Huruf S (Latin besar)'],
            ['category' => 'latin', 'character' => 'T', 'braille_unicode' => '⠞', 'dots_binary' => '011110', 'dots_decimal' => 30, 'description' => 'Huruf T (Latin besar)'],
            ['category' => 'latin', 'character' => 'U', 'braille_unicode' => '⠥', 'dots_binary' => '101001', 'dots_decimal' => 41, 'description' => 'Huruf U (Latin besar)'],
            ['category' => 'latin', 'character' => 'V', 'braille_unicode' => '⠧', 'dots_binary' => '111001', 'dots_decimal' => 57, 'description' => 'Huruf V (Latin besar)'],
            ['category' => 'latin', 'character' => 'W', 'braille_unicode' => '⠺', 'dots_binary' => '010111', 'dots_decimal' => 23, 'description' => 'Huruf W (Latin besar)'],
            ['category' => 'latin', 'character' => 'X', 'braille_unicode' => '⠭', 'dots_binary' => '101101', 'dots_decimal' => 45, 'description' => 'Huruf X (Latin besar)'],
            ['category' => 'latin', 'character' => 'Y', 'braille_unicode' => '⠽', 'dots_binary' => '111101', 'dots_decimal' => 61, 'description' => 'Huruf Y (Latin besar)'],
            ['category' => 'latin', 'character' => 'Z', 'braille_unicode' => '⠵', 'dots_binary' => '101011', 'dots_decimal' => 43, 'description' => 'Huruf Z (Latin besar)'],

            // Huruf Latin Lowercase a-z
            ['category' => 'latin', 'character' => 'a', 'braille_unicode' => '⠁', 'dots_binary' => '100000', 'dots_decimal' => 32, 'description' => 'Huruf a (Latin kecil)'],
            ['category' => 'latin', 'character' => 'b', 'braille_unicode' => '⠃', 'dots_binary' => '110000', 'dots_decimal' => 48, 'description' => 'Huruf b (Latin kecil)'],
            ['category' => 'latin', 'character' => 'c', 'braille_unicode' => '⠉', 'dots_binary' => '100100', 'dots_decimal' => 36, 'description' => 'Huruf c (Latin kecil)'],
            ['category' => 'latin', 'character' => 'd', 'braille_unicode' => '⠙', 'dots_binary' => '100110', 'dots_decimal' => 52, 'description' => 'Huruf d (Latin kecil)'],
            ['category' => 'latin', 'character' => 'e', 'braille_unicode' => '⠑', 'dots_binary' => '100010', 'dots_decimal' => 34, 'description' => 'Huruf e (Latin kecil)'],
            ['category' => 'latin', 'character' => 'f', 'braille_unicode' => '⠋', 'dots_binary' => '110010', 'dots_decimal' => 50, 'description' => 'Huruf f (Latin kecil)'],
            ['category' => 'latin', 'character' => 'g', 'braille_unicode' => '⠛', 'dots_binary' => '110110', 'dots_decimal' => 54, 'description' => 'Huruf g (Latin kecil)'],
            ['category' => 'latin', 'character' => 'h', 'braille_unicode' => '⠓', 'dots_binary' => '100110', 'dots_decimal' => 38, 'description' => 'Huruf h (Latin kecil)'],
            ['category' => 'latin', 'character' => 'i', 'braille_unicode' => '⠊', 'dots_binary' => '010100', 'dots_decimal' => 20, 'description' => 'Huruf i (Latin kecil)'],
            ['category' => 'latin', 'character' => 'j', 'braille_unicode' => '⠚', 'dots_binary' => '010110', 'dots_decimal' => 22, 'description' => 'Huruf j (Latin kecil)'],
            ['category' => 'latin', 'character' => 'k', 'braille_unicode' => '⠅', 'dots_binary' => '101000', 'dots_decimal' => 40, 'description' => 'Huruf k (Latin kecil)'],
            ['category' => 'latin', 'character' => 'l', 'braille_unicode' => '⠇', 'dots_binary' => '111000', 'dots_decimal' => 56, 'description' => 'Huruf l (Latin kecil)'],
            ['category' => 'latin', 'character' => 'm', 'braille_unicode' => '⠍', 'dots_binary' => '101100', 'dots_decimal' => 44, 'description' => 'Huruf m (Latin kecil)'],
            ['category' => 'latin', 'character' => 'n', 'braille_unicode' => '⠝', 'dots_binary' => '111100', 'dots_decimal' => 60, 'description' => 'Huruf n (Latin kecil)'],
            ['category' => 'latin', 'character' => 'o', 'braille_unicode' => '⠕', 'dots_binary' => '101010', 'dots_decimal' => 42, 'description' => 'Huruf o (Latin kecil)'],
            ['category' => 'latin', 'character' => 'p', 'braille_unicode' => '⠏', 'dots_binary' => '111010', 'dots_decimal' => 58, 'description' => 'Huruf p (Latin kecil)'],
            ['category' => 'latin', 'character' => 'q', 'braille_unicode' => '⠟', 'dots_binary' => '111110', 'dots_decimal' => 62, 'description' => 'Huruf q (Latin kecil)'],
            ['category' => 'latin', 'character' => 'r', 'braille_unicode' => '⠗', 'dots_binary' => '101110', 'dots_decimal' => 46, 'description' => 'Huruf r (Latin kecil)'],
            ['category' => 'latin', 'character' => 's', 'braille_unicode' => '⠎', 'dots_binary' => '011100', 'dots_decimal' => 28, 'description' => 'Huruf s (Latin kecil)'],
            ['category' => 'latin', 'character' => 't', 'braille_unicode' => '⠞', 'dots_binary' => '011110', 'dots_decimal' => 30, 'description' => 'Huruf t (Latin kecil)'],
            ['category' => 'latin', 'character' => 'u', 'braille_unicode' => '⠥', 'dots_binary' => '101001', 'dots_decimal' => 41, 'description' => 'Huruf u (Latin kecil)'],
            ['category' => 'latin', 'character' => 'v', 'braille_unicode' => '⠧', 'dots_binary' => '111001', 'dots_decimal' => 57, 'description' => 'Huruf v (Latin kecil)'],
            ['category' => 'latin', 'character' => 'w', 'braille_unicode' => '⠺', 'dots_binary' => '010111', 'dots_decimal' => 23, 'description' => 'Huruf w (Latin kecil)'],
            ['category' => 'latin', 'character' => 'x', 'braille_unicode' => '⠭', 'dots_binary' => '101101', 'dots_decimal' => 45, 'description' => 'Huruf x (Latin kecil)'],
            ['category' => 'latin', 'character' => 'y', 'braille_unicode' => '⠽', 'dots_binary' => '111101', 'dots_decimal' => 61, 'description' => 'Huruf y (Latin kecil)'],
            ['category' => 'latin', 'character' => 'z', 'braille_unicode' => '⠵', 'dots_binary' => '101011', 'dots_decimal' => 43, 'description' => 'Huruf z (Latin kecil)'],

            // Angka 0-9
            ['category' => 'angka', 'character' => '0', 'braille_unicode' => '⠼⠚', 'dots_binary' => '010110', 'dots_decimal' => 22, 'description' => 'Angka 0'],
            ['category' => 'angka', 'character' => '1', 'braille_unicode' => '⠼⠁', 'dots_binary' => '100000', 'dots_decimal' => 32, 'description' => 'Angka 1'],
            ['category' => 'angka', 'character' => '2', 'braille_unicode' => '⠼⠃', 'dots_binary' => '110000', 'dots_decimal' => 48, 'description' => 'Angka 2'],
            ['category' => 'angka', 'character' => '3', 'braille_unicode' => '⠼⠉', 'dots_binary' => '100100', 'dots_decimal' => 36, 'description' => 'Angka 3'],
            ['category' => 'angka', 'character' => '4', 'braille_unicode' => '⠼⠙', 'dots_binary' => '110100', 'dots_decimal' => 52, 'description' => 'Angka 4'],
            ['category' => 'angka', 'character' => '5', 'braille_unicode' => '⠼⠑', 'dots_binary' => '100010', 'dots_decimal' => 34, 'description' => 'Angka 5'],
            ['category' => 'angka', 'character' => '6', 'braille_unicode' => '⠼⠋', 'dots_binary' => '110010', 'dots_decimal' => 50, 'description' => 'Angka 6'],
            ['category' => 'angka', 'character' => '7', 'braille_unicode' => '⠼⠛', 'dots_binary' => '110110', 'dots_decimal' => 54, 'description' => 'Angka 7'],
            ['category' => 'angka', 'character' => '8', 'braille_unicode' => '⠼⠓', 'dots_binary' => '100110', 'dots_decimal' => 38, 'description' => 'Angka 8'],
            ['category' => 'angka', 'character' => '9', 'braille_unicode' => '⠼⠊', 'dots_binary' => '010100', 'dots_decimal' => 20, 'description' => 'Angka 9'],

            // Huruf Hijaiyah (ا sampai ي)
            ['category' => 'hijaiyah', 'character' => 'ا', 'braille_unicode' => '⠁', 'dots_binary' => '100000', 'dots_decimal' => 32, 'description' => 'Alif'],
            ['category' => 'hijaiyah', 'character' => 'ب', 'braille_unicode' => '⠃', 'dots_binary' => '110000', 'dots_decimal' => 48, 'description' => 'Ba'],
            ['category' => 'hijaiyah', 'character' => 'ت', 'braille_unicode' => '⠞', 'dots_binary' => '011110', 'dots_decimal' => 30, 'description' => 'Ta'],
            ['category' => 'hijaiyah', 'character' => 'ث', 'braille_unicode' => '⠹', 'dots_binary' => '100111', 'dots_decimal' => 39, 'description' => 'Tsa'],
            ['category' => 'hijaiyah', 'character' => 'ج', 'braille_unicode' => '⠚', 'dots_binary' => '010110', 'dots_decimal' => 22, 'description' => 'Jim'],
            ['category' => 'hijaiyah', 'character' => 'ح', 'braille_unicode' => '⠓', 'dots_binary' => '100110', 'dots_decimal' => 38, 'description' => 'Ha'],
            ['category' => 'hijaiyah', 'character' => 'خ', 'braille_unicode' => '⠡', 'dots_binary' => '100001', 'dots_decimal' => 33, 'description' => 'Kha'],
            ['category' => 'hijaiyah', 'character' => 'د', 'braille_unicode' => '⠙', 'dots_binary' => '110100', 'dots_decimal' => 52, 'description' => 'Dal'],
            ['category' => 'hijaiyah', 'character' => 'ذ', 'braille_unicode' => '⠮', 'dots_binary' => '011101', 'dots_decimal' => 29, 'description' => 'Dzal'],
            ['category' => 'hijaiyah', 'character' => 'ر', 'braille_unicode' => '⠗', 'dots_binary' => '101110', 'dots_decimal' => 46, 'description' => 'Ra'],
            ['category' => 'hijaiyah', 'character' => 'ز', 'braille_unicode' => '⠵', 'dots_binary' => '101011', 'dots_decimal' => 43, 'description' => 'Zay'],
            ['category' => 'hijaiyah', 'character' => 'س', 'braille_unicode' => '⠎', 'dots_binary' => '011100', 'dots_decimal' => 28, 'description' => 'Sin'],
            ['category' => 'hijaiyah', 'character' => 'ش', 'braille_unicode' => '⠱', 'dots_binary' => '110001', 'dots_decimal' => 49, 'description' => 'Syin'],
            ['category' => 'hijaiyah', 'character' => 'ص', 'braille_unicode' => '⠹', 'dots_binary' => '100111', 'dots_decimal' => 39, 'description' => 'Shad'],
            ['category' => 'hijaiyah', 'character' => 'ض', 'braille_unicode' => '⠱', 'dots_binary' => '110001', 'dots_decimal' => 49, 'description' => 'Dhad'],
            ['category' => 'hijaiyah', 'character' => 'ط', 'braille_unicode' => '⠞', 'dots_binary' => '011110', 'dots_decimal' => 30, 'description' => 'Tha'],
            ['category' => 'hijaiyah', 'character' => 'ظ', 'braille_unicode' => '⠹', 'dots_binary' => '100111', 'dots_decimal' => 39, 'description' => 'Zha'],
            ['category' => 'hijaiyah', 'character' => 'ع', 'braille_unicode' => '⠪', 'dots_binary' => '010101', 'dots_decimal' => 21, 'description' => 'Ain'],
            ['category' => 'hijaiyah', 'character' => 'غ', 'braille_unicode' => '⠻', 'dots_binary' => '110011', 'dots_decimal' => 51, 'description' => 'Ghayn'],
            ['category' => 'hijaiyah', 'character' => 'ف', 'braille_unicode' => '⠋', 'dots_binary' => '110010', 'dots_decimal' => 50, 'description' => 'Fa'],
            ['category' => 'hijaiyah', 'character' => 'ق', 'braille_unicode' => '⠟', 'dots_binary' => '111110', 'dots_decimal' => 62, 'description' => 'Qaf'],
            ['category' => 'hijaiyah', 'character' => 'ك', 'braille_unicode' => '⠅', 'dots_binary' => '101000', 'dots_decimal' => 40, 'description' => 'Kaf'],
            ['category' => 'hijaiyah', 'character' => 'ل', 'braille_unicode' => '⠇', 'dots_binary' => '111000', 'dots_decimal' => 56, 'description' => 'Lam'],
            ['category' => 'hijaiyah', 'character' => 'م', 'braille_unicode' => '⠍', 'dots_binary' => '101100', 'dots_decimal' => 44, 'description' => 'Mim'],
            ['category' => 'hijaiyah', 'character' => 'ن', 'braille_unicode' => '⠝', 'dots_binary' => '111100', 'dots_decimal' => 60, 'description' => 'Nun'],
            ['category' => 'hijaiyah', 'character' => 'ه', 'braille_unicode' => '⠓', 'dots_binary' => '100110', 'dots_decimal' => 38, 'description' => 'Ha'],
            ['category' => 'hijaiyah', 'character' => 'و', 'braille_unicode' => '⠺', 'dots_binary' => '010111', 'dots_decimal' => 23, 'description' => 'Waw'],
            ['category' => 'hijaiyah', 'character' => 'ي', 'braille_unicode' => '⠊', 'dots_binary' => '010100', 'dots_decimal' => 20, 'description' => 'Ya'],

            // Harakat dasar
            ['category' => 'harakat', 'character' => 'َ', 'braille_unicode' => '⠘', 'dots_binary' => '010000', 'dots_decimal' => 16, 'description' => 'Fathah'],
            ['category' => 'harakat', 'character' => 'ِ', 'braille_unicode' => '⠌', 'dots_binary' => '001100', 'dots_decimal' => 12, 'description' => 'Kasrah'],
            ['category' => 'harakat', 'character' => 'ُ', 'braille_unicode' => '⠬', 'dots_binary' => '001010', 'dots_decimal' => 10, 'description' => 'Dhammah'],
            ['category' => 'harakat', 'character' => 'ْ', 'braille_unicode' => '⠄', 'dots_binary' => '000100', 'dots_decimal' => 4, 'description' => 'Sukun'],
            ['category' => 'harakat', 'character' => 'ّ', 'braille_unicode' => '⠠', 'dots_binary' => '000010', 'dots_decimal' => 2, 'description' => 'Tasydid'],
            ['category' => 'harakat', 'character' => 'ً', 'braille_unicode' => '⠈', 'dots_binary' => '000001', 'dots_decimal' => 1, 'description' => 'Tanwin Fathah'],
            ['category' => 'harakat', 'character' => 'ٍ', 'braille_unicode' => '⠘', 'dots_binary' => '010000', 'dots_decimal' => 16, 'description' => 'Tanwin Kasrah'],
            ['category' => 'harakat', 'character' => 'ٌ', 'braille_unicode' => '⠌', 'dots_binary' => '001100', 'dots_decimal' => 12, 'description' => 'Tanwin Dhammah'],

            // Simbol umum
            ['category' => 'simbol', 'character' => '.', 'braille_unicode' => '⠲', 'dots_binary' => '010011', 'dots_decimal' => 19, 'description' => 'Titik'],
            ['category' => 'simbol', 'character' => ',', 'braille_unicode' => '⠂', 'dots_binary' => '000010', 'dots_decimal' => 2, 'description' => 'Koma'],
            ['category' => 'simbol', 'character' => '!', 'braille_unicode' => '⠖', 'dots_binary' => '011001', 'dots_decimal' => 25, 'description' => 'Tanda seru'],
            ['category' => 'simbol', 'character' => '?', 'braille_unicode' => '⠦', 'dots_binary' => '001001', 'dots_decimal' => 9, 'description' => 'Tanda tanya'],
            ['category' => 'simbol', 'character' => '"', 'braille_unicode' => '⠶', 'dots_binary' => '011011', 'dots_decimal' => 27, 'description' => 'Tanda kutip'],
            ['category' => 'simbol', 'character' => '(', 'braille_unicode' => '⠦', 'dots_binary' => '001001', 'dots_decimal' => 9, 'description' => 'Tanda kurung buka'],
            ['category' => 'simbol', 'character' => ')', 'braille_unicode' => '⠴', 'dots_binary' => '001011', 'dots_decimal' => 11, 'description' => 'Tanda kurung tutup'],
            ['category' => 'simbol', 'character' => ';', 'braille_unicode' => '⠆', 'dots_binary' => '000110', 'dots_decimal' => 6, 'description' => 'Titik koma'],
            ['category' => 'simbol', 'character' => ':', 'braille_unicode' => '⠒', 'dots_binary' => '000101', 'dots_decimal' => 5, 'description' => 'Titik dua'],
            ['category' => 'simbol', 'character' => '-', 'braille_unicode' => '⠤', 'dots_binary' => '001001', 'dots_decimal' => 9, 'description' => 'Tanda hubung'],
            ['category' => 'simbol', 'character' => ' ', 'braille_unicode' => '⠀', 'dots_binary' => '000000', 'dots_decimal' => 0, 'description' => 'Spasi'],
        ];

        DB::table('braille_patterns')->insert($patterns);
    }
}
