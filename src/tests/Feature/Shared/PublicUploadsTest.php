<?php

namespace Tests\Feature\Shared;

use App\Shared\Services\PublicUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Admin uploads go to `filesystems.uploads_disk` (local `public`, or R2 on Vercel). */
class PublicUploadsTest extends TestCase
{
    public function test_urls_for_each_kind_of_stored_value(): void
    {
        $this->assertNull(PublicUploads::url(null));
        $this->assertNull(PublicUploads::url(''));
        $this->assertSame('https://cdn.example.com/a.jpg', PublicUploads::url('https://cdn.example.com/a.jpg'));
        $this->assertSame(asset('images/logo.png'), PublicUploads::url('images/logo.png'));
        $this->assertSame(asset('images/hero.jpg'), PublicUploads::url('/images/hero.jpg'));
        $this->assertSame(asset('storage/products/a.jpg'), PublicUploads::url('products/a.jpg'));
    }

    public function test_uploads_and_urls_follow_the_configured_disk(): void
    {
        config(['filesystems.uploads_disk' => 'r2', 'filesystems.disks.r2.url' => 'https://pub-test.r2.dev']);
        Storage::fake('r2', ['url' => 'https://pub-test.r2.dev']);
        Storage::fake('public');

        $uploads = new PublicUploads;
        $path = $uploads->store(UploadedFile::fake()->image('loaf.jpg'), 'products');

        Storage::disk('r2')->assertExists($path);
        Storage::disk('public')->assertMissing($path);
        $this->assertSame("https://pub-test.r2.dev/{$path}", PublicUploads::url($path));

        $uploads->delete($path);
        Storage::disk('r2')->assertMissing($path);
    }
}
