<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Serve profile pictures stored under storage/app/public/profile_pictures
     */
    public function profilePicture($path)
    {
        // Normalize and prevent directory traversal
        $normalized = str_replace('..', '', $path);
        $fullPath = storage_path('app/public/' . $normalized);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=604800', // cache for 7 days
        ]);
    }
}
