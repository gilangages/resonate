<?php

namespace App\Contracts;

interface MusicProvider
{
    public function searchTrack(string $query): array;
}
