<?php

namespace Tests\Feature\Storefront\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Unmatched URLs render the branded 404 with the shared props the storefront shell needs. */
class UnknownUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_unknown_storefront_url_renders_404_with_shared_props(): void
    {
        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertInertia(fn (Assert $page) => $page
                ->component('error')
                ->where('status', 404)
                ->has('auth')
                ->where('auth.user', null)
                ->has('cart')
                ->where('app.area', 'storefront'));
    }

    public function test_unknown_url_keeps_the_signed_in_user(): void
    {
        $this->actingAs($this->userWith())
            ->get('/orders-typo')
            ->assertNotFound()
            ->assertInertia(fn (Assert $page) => $page->component('error')->whereNot('auth.user', null));
    }
}
