<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Extensions -> type MIME, en dur pour ne PAS dépendre de l'extension PHP
     * "fileinfo" (souvent désactivée par défaut sur XAMPP/Windows), qui fait
     * planter Storage::response()/File::getMimeType() avec une erreur 500.
     */
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'bmp' => 'image/bmp',
        'pdf' => 'application/pdf',
    ];

    public function show(Request $request, string $path): Response
    {
        $normalizedPath = str_replace('\\', '/', $path);
        abort_if(str_contains($normalizedPath, '..'), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($normalizedPath), 404);

        $extension = strtolower(pathinfo($normalizedPath, PATHINFO_EXTENSION));
        $contentType = self::MIME_TYPES[$extension] ?? 'application/octet-stream';

        return response($disk->get($normalizedPath), 200, [
            'Content-Type' => $contentType,
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
