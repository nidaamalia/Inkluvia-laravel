<?php

if (!function_exists('getInitials')) {
    function getInitials($name) {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'd M Y') {
        return \Carbon\Carbon::parse($date)->format($format);
    }
}