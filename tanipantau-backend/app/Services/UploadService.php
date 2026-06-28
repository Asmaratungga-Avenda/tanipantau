<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    /**
     * Allowed image MIME types
     */
    protected array $allowedImageMimes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
        'image/gif'
    ];

    /**
     * Allowed image extensions
     */
    protected array $allowedImageExtensions = [
        'jpeg',
        'jpg',
        'png',
        'webp',
        'gif'
    ];

    /**
     * Upload file to storage public disk
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @return string path (relative to storage/app/public)
     */
    public function uploadFile(UploadedFile $file, string $directory = 'uploads'): string
    {
        $extension = strtolower($file->extension());
        
        $filename = Str::uuid() . '.' . $extension;
        $path = $file->storeAs($directory, $filename, 'public');
        return $path;
    }

    /**
     * Delete file from storage public disk
     * 
     * @param string|null $path
     * @return bool
     */
    public function deleteFile(?string $path): bool
    {
        if (!$path) {
            return true;
        }
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return true;
    }

    /**
     * Replace old file with new one
     * 
     * @param UploadedFile $newFile
     * @param string|null $oldPath
     * @param string $directory
     * @return string
     */
    public function replaceFile(UploadedFile $newFile, ?string $oldPath, string $directory = 'uploads'): string
    {
        $this->deleteFile($oldPath);
        return $this->uploadFile($newFile, $directory);
    }
}
