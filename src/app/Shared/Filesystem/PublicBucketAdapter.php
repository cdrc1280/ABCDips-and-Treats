<?php

namespace App\Shared\Filesystem;

use Illuminate\Filesystem\FilesystemAdapter;

/**
 * Disk for a public bucket behind its own address (Cloudflare R2 r2.dev / custom domain):
 * file URLs are `config('url')` + path. Laravel only builds URLs for its built-in drivers.
 */
class PublicBucketAdapter extends FilesystemAdapter
{
    /**
     * @param  string  $path
     */
    public function url($path): string
    {
        $base = rtrim((string) ($this->config['url'] ?? ''), '/');
        if ($base === '') {
            throw new \RuntimeException('Set R2_PUBLIC_URL: the bucket has no public address configured.');
        }

        $prefix = trim((string) ($this->config['prefix'] ?? ''), '/');

        return $base.'/'.ltrim(($prefix !== '' ? $prefix.'/' : '').$path, '/');
    }
}
