<?php

namespace App\Services;

use App\Models\Documentation;
use App\Models\Program;
use App\Models\ProgramKerja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PhotoUploadService
{
    /**
     * Store, compress, and log photo documentation.
     */
    public static function storeAndCompress(
        $photoInput, 
        $programId = null, 
        $subProgramId = null, 
        $activityId = null, 
        $lat = null, 
        $lng = null, 
        $deviceInfo = null
    ) {
        if (empty($photoInput)) {
            return null;
        }

        $user = Auth::user();

        // Resolve names for metadata
        $programName = null;
        if ($programId) {
            $program = Program::find($programId);
            $programName = $program ? $program->name : null;
        }

        $subProgramName = null;
        if ($subProgramId) {
            $subProgram = ProgramKerja::find($subProgramId);
            $subProgramName = $subProgram ? $subProgram->name : null;
        }

        // Set folder path hierarchy: Program -> Sub Program -> Kegiatan -> Dokumentasi
        $folderProgram = $programId ?? 'general';
        $folderSubProgram = $subProgramId ?? 'general';
        $folderActivity = $activityId ?? 'general';
        $directoryPath = "programs/{$folderProgram}/sub-programs/{$folderSubProgram}/activities/{$folderActivity}/documentations";

        $fileName = 'doc_' . time() . '_' . uniqid() . '.jpg';
        $fullPath = $directoryPath . '/' . $fileName;

        // Compress and save photo locally first
        $tempLocalFile = tempnam(sys_get_temp_dir(), 'yota_upload_');
        
        try {
            if (is_object($photoInput) && method_exists($photoInput, 'getRealPath')) {
                // If standard uploaded file
                $tempPath = $photoInput->getRealPath();
                self::compressImage($tempPath, $tempLocalFile, 75);
            } else {
                // If base64 encoded photo
                $base64Data = $photoInput;
                if (str_contains($base64Data, ',')) {
                    $base64Data = explode(',', $base64Data)[1];
                }
                $imageData = base64_decode($base64Data);
                file_put_contents($tempLocalFile, $imageData);
                self::compressImage($tempLocalFile, $tempLocalFile, 75);
            }

            // Store compressed file to storage disk
            Storage::disk('public')->putFileAs($directoryPath, new \Illuminate\Http\File($tempLocalFile), $fileName);
            $publicUrl = '/storage/' . $fullPath;

            // Save record to unified documentations table
            Documentation::create([
                'user_id' => $user ? $user->id : null,
                'program_id' => $programId,
                'program_kerja_id' => $subProgramId,
                'activity_id' => $activityId,
                'file_path' => $publicUrl,
                'user_name' => $user ? $user->name : 'Public / Guest',
                'program_name' => $programName,
                'sub_program_name' => $subProgramName,
                'timestamp' => Carbon::now(),
                'latitude' => $lat,
                'longitude' => $lng,
                'device_info' => $deviceInfo ?? request()->header('User-Agent'),
            ]);

            return $publicUrl;

        } catch (\Exception $e) {
            \Log::error('Failed uploading/compressing photo documentation: ' . $e->getMessage());
            try {
                if (is_object($photoInput) && method_exists($photoInput, 'store')) {
                    $path = $photoInput->store($directoryPath, 'public');
                    return '/storage/' . $path;
                } else {
                    $base64Data = $photoInput;
                    if (str_contains($base64Data, ',')) {
                        $base64Data = explode(',', $base64Data)[1];
                    }
                    $imageData = base64_decode($base64Data);
                    Storage::disk('public')->put($fullPath, $imageData);
                    return '/storage/' . $fullPath;
                }
            } catch (\Exception $fallbackEx) {
                throw new \Exception("Gagal menyimpan dokumen foto (Kompresi gagal: {$e->getMessage()}. Penyimpanan mentah gagal: {$fallbackEx->getMessage()})");
            }
        } finally {
            @unlink($tempLocalFile);
        }
    }

    /**
     * Store and compress photo only (without creating documentation metadata records).
     */
    public static function compressAndStoreOnly($photoInput, $directory = 'selfies', $prefix = 'photo')
    {
        if (empty($photoInput)) {
            return null;
        }

        $fileName = $prefix . '_' . time() . '_' . uniqid() . '.jpg';
        $fullPath = $directory . '/' . $fileName;
        $tempLocalFile = tempnam(sys_get_temp_dir(), 'yota_compress_');

        try {
            if (is_object($photoInput) && method_exists($photoInput, 'getRealPath')) {
                $tempPath = $photoInput->getRealPath();
                self::compressImage($tempPath, $tempLocalFile, 70);
            } else {
                $base64Data = $photoInput;
                if (str_contains($base64Data, ',')) {
                    $base64Data = explode(',', $base64Data)[1];
                }
                $imageData = base64_decode($base64Data);
                file_put_contents($tempLocalFile, $imageData);
                self::compressImage($tempLocalFile, $tempLocalFile, 70);
            }

            Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempLocalFile), $fileName);
            return '/storage/' . $fullPath;

        } catch (\Exception $e) {
            \Log::error('Failed storing only compressed photo: ' . $e->getMessage());
            try {
                if (is_object($photoInput) && method_exists($photoInput, 'store')) {
                    $path = $photoInput->store($directory, 'public');
                    return '/storage/' . $path;
                } else {
                    $base64Data = $photoInput;
                    if (str_contains($base64Data, ',')) {
                        $base64Data = explode(',', $base64Data)[1];
                    }
                    $imageData = base64_decode($base64Data);
                    Storage::disk('public')->put($fullPath, $imageData);
                    return '/storage/' . $fullPath;
                }
            } catch (\Exception $fallbackEx) {
                throw new \Exception("Gagal menyimpan foto (Kompresi gagal: {$e->getMessage()}. Penyimpanan mentah gagal: {$fallbackEx->getMessage()})");
            }
        } finally {
            @unlink($tempLocalFile);
        }
    }

    /**
     * Image compression helper.
     */
    private static function compressImage($sourcePath, $targetPath, $quality = 75)
    {
        $info = getimagesize($sourcePath);
        if ($info === false) {
            copy($sourcePath, $targetPath);
            return;
        }

        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($sourcePath);
                if ($image) {
                    imagejpeg($image, $targetPath, $quality);
                    imagedestroy($image);
                } else {
                    copy($sourcePath, $targetPath);
                }
                break;
            case 'image/png':
                $image = @imagecreatefrompng($sourcePath);
                if ($image) {
                    $pngQuality = 9 - round(($quality * 9) / 100);
                    imagepng($image, $targetPath, $pngQuality);
                    imagedestroy($image);
                } else {
                    copy($sourcePath, $targetPath);
                }
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($sourcePath);
                if ($image) {
                    imagegif($image, $targetPath);
                    imagedestroy($image);
                } else {
                    copy($sourcePath, $targetPath);
                }
                break;
            default:
                copy($sourcePath, $targetPath);
                break;
        }
    }
}
