<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BraillePattern extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'character',
        'braille_unicode',
        'dots_binary',
        'dots_decimal',
        'description'
    ];

    /**
     * Get braille pattern by character
     */
    public static function getByCharacter($character)
    {
        return static::where('character', $character)->first();
    }

    /**
     * Get braille unicode by character
     */
    public static function getUnicodeByCharacter($character)
    {
        $pattern = static::getByCharacter($character);
        return $pattern ? $pattern->braille_unicode : '⠀'; // Default to space
    }

    /**
     * Get dots binary by character
     */
    public static function getDotsBinaryByCharacter($character)
    {
        $pattern = static::getByCharacter($character);
        return $pattern ? $pattern->dots_binary : '000000'; // Default to empty pattern
    }

    /**
     * Get dots decimal by character
     */
    public static function getDotsDecimalByCharacter($character)
    {
        $pattern = static::getByCharacter($character);
        return $pattern ? $pattern->dots_decimal : 0; // Default to 0
    }
}
