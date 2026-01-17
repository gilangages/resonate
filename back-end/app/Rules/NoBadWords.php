<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoBadWords implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Daftar kata terlarang (bisa dipindah ke config/database nanti)
        $badWords = ['bodoh', 'anying', 'cacat', 'kntl', 'bangke', 'toket', 'coli', 'ngewe', 'bokep', 'mastrubasi', 'sange', 'sanhge', 'ngentot', 'tolol', 'perek', 'jancok', 'pelacur', 'lonte', 'asu', 'bangsat', 'jingan', 'anjing', 'goblok', 'asu', 'kontol', 'tai', 'eek', 'bajingan', 'pentil', 'memek', 'pepek', 'itil', '']; // Contoh saja

        foreach ($badWords as $word) {
            // Cek jika teks mengandung kata kasar (case insensitive)
            if (stripos($value, $word) !== false) {
                $fail("Pesan Anda mengandung kata yang tidak pantas: '$word'. Harap gunakan bahasa yang sopan.");
                return;
            }
        }
    }
}
