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
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8').
                    mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8'
            );
        }

        preg_match_all('#([A-Z]+)#', $name, $capitals);

        if (count($capitals[1]) >= 2) {
            return mb_substr(implode('', $capitals[1]), 0, 2, 'UTF-8');
        }

        return mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'), 'UTF-8');
    }
}

if (! function_exists('normalize_currency')) {
    function normalize_currency(string $value): string
    {
        return str_replace(',', '.', str_replace('.', '', $value));
    }
}
