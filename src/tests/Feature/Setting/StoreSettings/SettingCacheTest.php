<?php

namespace Tests\Feature\Setting\StoreSettings;

use App\Domains\Setting\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Setting::get caches the stored value only, never a caller's default. */
class SettingCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_is_not_cached_for_a_missing_setting(): void
    {
        $this->assertSame('old default', Setting::get('home_hero_title', 'old default'));
        $this->assertSame('new default', Setting::get('home_hero_title', 'new default'));
        $this->assertNull(Setting::get('home_hero_title'));
    }

    public function test_stored_value_wins_and_set_refreshes_the_cache(): void
    {
        Setting::set('home_hero_title', 'Banana bread');
        $this->assertSame('Banana bread', Setting::get('home_hero_title', 'fallback'));

        Setting::set('home_hero_title', 'Brownies');
        $this->assertSame('Brownies', Setting::get('home_hero_title', 'fallback'));
    }
}
