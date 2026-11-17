<?php

namespace Tests\Feature\Storefront\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The storefront shell honours the `appearance` cookie before React boots, like the back office. */
class StorefrontThemeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_dark_cookie_puts_the_dark_class_on_html(): void
    {
        $html = $this->withUnencryptedCookie('appearance', 'dark')->get('/about')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<html[^>]*class="[^"]*\bdark\b/', (string) $html);
    }

    public function test_light_cookie_leaves_html_light(): void
    {
        $html = $this->withUnencryptedCookie('appearance', 'light')->get('/about')->assertOk()->getContent();

        $this->assertDoesNotMatchRegularExpression('/<html[^>]*class="[^"]*\bdark\b/', (string) $html);
    }

    public function test_auth_pages_share_the_storefront_theme_handling(): void
    {
        $html = $this->withUnencryptedCookie('appearance', 'dark')->get('/login')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<html[^>]*class="[^"]*\bdark\b/', (string) $html);
    }
}
