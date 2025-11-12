<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    /**
     * Store a PDF file and return its storage path and size.
     *
     * @return array{path:string,size:int}
     */
    public function storePdf(UploadedFile $file, string $disk = 'local', string $directory = 'books/pdfs'): array
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, $disk);

        return [
            'path' => $path,
            'size' => $file->getSize(),
        ];
    }

    /**
     * Store an image file and return its storage path.
     */
    public function storeImage(UploadedFile $file, string $disk = 'public', string $directory = 'books/covers'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($directory, $filename, $disk);
    }
}
