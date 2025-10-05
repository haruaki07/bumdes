<?php

if (! function_exists('human_filesize')) {
    function human_filesize($bytes, $decimals = 2)
    {
        $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).' '.$size[$factor];
    }
}

if (! function_exists('get_initials')) {
    /**
     * Get the first and last initials from a full name.
     *
     * @param  string  $name  Full name.
     * @return string Uppercase initials.
     */
    function get_initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));

        if (empty($words)) {
            return '';
        }

        $firstInitial = strtoupper($words[0][0]);
        $lastInitial = strtoupper($words[count($words) - 1][0]);

        return $firstInitial.$lastInitial;
    }
}

if (! function_exists('normalize_currency')) {
    function normalize_currency(string $value): string
    {
        return str_replace(',', '.', str_replace('.', '', $value));
    }
}
