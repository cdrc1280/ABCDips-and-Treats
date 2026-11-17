<?php

namespace App\Shared\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Files stored on the `public` disk (served from /storage). Paths are stored relative,
 * matching how the existing image_path columns work.
 */
final class PublicUploads
{
    public function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public') ?: throw new \RuntimeException("Could not store the upload in {$directory}.");
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
            Storage::disk('public')->delete($path);
        }
    }
}
