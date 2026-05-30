<?php

if (! function_exists('avatarTones')) {
    function avatarTones(string $name): array
    {
        $tones = [
            ['#F4EFE6', '#5C5346'],
            ['#E8ECFF', '#2A3FB8'],
            ['#FAF1DD', '#8A6520'],
            ['#F8E7DD', '#8E4A2E'],
            ['#DEEFEC', '#2D6B61'],
            ['#ECE4D6', '#3E342A'],
        ];
        $hash = 0;
        foreach (str_split($name) as $char) {
            $hash = (($hash * 31) + ord($char)) & 0x7FFFFFFF;
        }
        return $tones[$hash % count($tones)];
    }
}

if (! function_exists('avatarInitials')) {
    function avatarInitials(string $name): string
    {
        return collect(explode(' ', $name))
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
            ->take(2)
            ->implode('');
    }
}
