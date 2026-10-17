<?php

namespace App\Http\Controllers;

use App\Services\PhotoUploadService;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /**
     * Store documentation photo with metadata and auto-compression.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required', // file or base64 string
            'program_id' => 'nullable|uuid',
            'sub_program_id' => 'nullable|uuid',
            'activity_id' => 'nullable|uuid',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_info' => 'nullable|string',
        ]);

        $filePath = PhotoUploadService::storeAndCompress(
            $request->photo,
            $request->program_id,
            $request->sub_program_id,
            $request->activity_id,
            $request->latitude,
            $request->longitude,
            $request->device_info
        );

        if ($filePath) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumentasi berhasil diunggah dan disimpan!',
                'file_path' => $filePath
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengunggah foto.'
        ], 500);
    }
}
