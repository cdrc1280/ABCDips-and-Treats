<?php

namespace Tests\Feature\Setting\StoreSettings;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Setting\Enums\SettingPermission;
use App\Domains\Setting\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StoreSettingsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function requiredValues(): array
    {
        return [
            'store_name' => 'ABCDips & Treats', 'home_hero_badge' => 'Fresh', 'home_hero_title' => 'Pastries', 'home_hero_subtitle' => 'Baked daily',
            'home_hero_description' => 'Banana bread and more', 'home_spotlight_tagline' => 'This week', 'home_spotlight_title' => 'Ube loaf',
            'home_spotlight_description' => 'Limited', 'home_spotlight_btn_text' => 'Order', 'home_spotlight_btn_url' => '/shop',
            'about_hero_tagline' => 'our story', 'about_hero_title' => 'Baked with heart', 'about_hero_subtitle' => 'Since 2020',
            'bdo_account_name' => 'ABCDips', 'bdo_account_number' => '0012',
        ];
    }

    public function test_view_only_users_see_settings_but_cannot_save(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView, SettingPermission::SettingsView));

        $this->get('/admin/settings')->assertOk()->assertInertia(fn (Assert $page) => $page->component('settings/index')->has('sections', 8));
        $this->put('/admin/settings', ['values' => $this->requiredValues()])->assertForbidden();
    }

    public function test_secrets_are_never_sent_to_the_browser_and_empty_means_keep(): void
    {
        Setting::set('paymongo_secret_key', 'sk_live_supersecret', 'paymongo');
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...SettingPermission::cases()));

        $this->get('/admin/settings')->assertInertia(fn (Assert $page) => $page
            ->where('values.paymongo_secret_key', '')
            ->where('secrets_set.paymongo_secret_key', true));
        $this->assertStringNotContainsString('sk_live_supersecret', $this->get('/admin/settings')->getContent());

        $this->put('/admin/settings', ['values' => $this->requiredValues() + ['paymongo_secret_key' => '']])->assertSessionHasNoErrors();
        $this->assertSame('sk_live_supersecret', Setting::get('paymongo_secret_key'));

        $this->put('/admin/settings', ['values' => $this->requiredValues() + ['paymongo_secret_key' => 'sk_live_rotated']])->assertSessionHasNoErrors();
        $this->assertSame('sk_live_rotated', Setting::get('paymongo_secret_key'));
    }

    public function test_saves_text_bools_lists_and_images(): void
    {
        Storage::fake('public');
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...SettingPermission::cases()));

        $this->put('/admin/settings', ['values' => $this->requiredValues() + [
            'store_phone' => '09171234567',
            'lalamove_sandbox' => '0',
            'lalamove_service_type' => 'MPV',
            'about_values' => [['emoji' => '🧈', 'title' => 'Real butter', 'desc' => 'Always'], ['emoji' => '', 'title' => '', 'desc' => '']],
            'home_spotlight_image' => UploadedFile::fake()->image('spot.jpg'),
        ]])->assertSessionHasNoErrors()->assertRedirect('/admin/settings')->assertInertiaFlash('toast.type', 'success');

        $this->assertSame('09171234567', Setting::get('store_phone'));
        $this->assertFalse(filter_var(Setting::get('lalamove_sandbox'), FILTER_VALIDATE_BOOLEAN));
        $this->assertSame([['emoji' => '🧈', 'title' => 'Real butter', 'desc' => 'Always']], Setting::getJson('about_values'));
        Storage::disk('public')->assertExists((string) Setting::get('home_spotlight_image'));
    }

    public function test_validation_uses_the_schema(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...SettingPermission::cases()));

        $this->put('/admin/settings', ['values' => ['store_name' => '', 'store_email' => 'not-an-email', 'lalamove_service_type' => 'ROCKET']])
            ->assertSessionHasErrors(['values.store_name', 'values.store_email', 'values.lalamove_service_type', 'values.bdo_account_number']);
    }
}
