<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    /**
     * Store an author photo under a standard directory, optionally removing the old one.
     * Returns the stored path.
     */
    public function storeAuthorPhoto(UploadedFile $file, ?string $oldPath = null, string $disk = 'public'): string
    {
        if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
            Storage::disk($disk)->delete($oldPath);
        }
        return $this->storeImage($file, $disk, 'authors/photos');
    }

    /**
     * Delete a file from the given disk if it exists.
     */
    public function deleteIfExists(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Store a PDF for a given entity using standard directories and return [path,size].
     * Entities: 'book' => books/pdfs, 'borrow' => borrows/pdfs, 'user' => users/pdfs
    */
    public function storePdfFor(string $entity, UploadedFile $file, ?string $oldPath = null, string $disk = 'local'): array
    {
        $directories = [
            'book'   => 'books/pdfs',
        ];

        $directory = $directories[$entity] ?? 'pdfs';

        if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
            Storage::disk($disk)->delete($oldPath);
        }

        return $this->storePdf($file, $disk, $directory);
    }

    /**
     * Store a photo for a given entity with a standard directory convention.
     * Entities: 'author' => authors/photos, 'user' => users/photos, 'book' => books/covers
     */
    public function storePhotoFor(string $entity, UploadedFile $file, ?string $oldPath = null, string $disk = 'public'): string
    {
        $directories = [
            'author' => 'authors/photos',
            'user'   => 'users/photos',
            'book'   => 'books/covers',
        ];

        $directory = $directories[$entity] ?? 'photos';

        if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
            Storage::disk($disk)->delete($oldPath);
        }

        return $this->storeImage($file, $disk, $directory);
    }

    /**
     * Stream a PDF from storage with proper headers.
     */
    public function streamPdf(string $path, string $disk = 'local', ?string $filename = null, bool $inline = true): BinaryFileResponse
    {
        if (empty($path) || !Storage::disk($disk)->exists($path)) {
            throw new NotFoundHttpException('File not found');
        }

        $fullPath = Storage::disk($disk)->path($path);
        $name = $filename ?: basename($fullPath);
        $disposition = $inline ? 'inline' : 'attachment';

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $name . '"',
        ]);
    }
}
