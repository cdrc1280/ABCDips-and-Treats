<?php

namespace App\Shared\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Admin uploads (product/category/blog/setting images). Paths are stored relative, matching how
 * the image_path columns work. The disk is `filesystems.uploads_disk`: `public` (local /storage,
 * Docker and dev) or `r2` (Cloudflare R2; required on Vercel, whose disk is temporary).
 */
final class PublicUploads
{
    public static function disk(): string
    {
        return (string) config('filesystems.uploads_disk', 'public');
    }

    /**
     * Public URL for a stored value: full URLs as-is, files bundled in public/ (`/…` or
     * `images/…`) from the app, anything else from the uploads disk.
     */
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, '/') || str_starts_with($path, 'images/')) {
            return asset(ltrim($path, '/'));
        }

        return self::disk() === 'public'
            ? asset('storage/'.$path)
            : Storage::disk(self::disk())->url($path);
    }

    public function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, self::disk()) ?: throw new \RuntimeException("Could not store the upload in {$directory}.");
    }

    /**
     * Replace/remove an existing path. External URLs (http…) are never deleted.
     */
    public function replace(?string $current, ?UploadedFile $new, string $directory, bool $remove = false): ?string
    {
        if ($new) {
            $this->delete($current);

            return $this->store($new, $directory);
        }

        if ($remove) {
            $this->delete($current);

            return null;
        }

        return $current;
    }

    /**
     * Gallery-style list: keep the paths the user kept (in their order), delete the ones
     * they removed, then append newly uploaded files.
     *
     * @param  array<int, string>  $current
     * @param  array<int, string>  $keep
     * @param  array<int, UploadedFile>  $new
     * @return list<string>
     */
    public function mergeList(array $current, array $keep, array $new, string $directory): array
    {
        $kept = array_values(array_filter($keep, fn ($path) => in_array($path, $current, true)));

        foreach (array_diff($current, $kept) as $removed) {
            $this->delete($removed);
        }

        foreach ($new as $file) {
            $kept[] = $this->store($file, $directory);
        }

        return $kept;
    }

    public function delete(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk(self::disk())->delete($path);
        }
    }
}
