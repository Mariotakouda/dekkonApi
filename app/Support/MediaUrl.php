<?php

namespace App\Support;

class MediaUrl
{
    public static function to(string $relativePath): string
    {
        return url('/media/'.ltrim($relativePath, '/'));
    }

    public static function toRelativePath(string $url): string
    {
        return ltrim(str_replace(url('/media/'), '', $url), '/');
    }
}
