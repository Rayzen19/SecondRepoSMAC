<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Serve profile pictures stored under storage/app/public/profile_pictures
     */
    public function profilePicture($filename)
    {
        $path = storage_path('app/public/profile_pictures/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=604800', // cache for 7 days
        ]);
    }
}
