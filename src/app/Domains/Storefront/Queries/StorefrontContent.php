<?php

namespace App\Domains\Storefront\Queries;

use App\Domains\Setting\Models\Setting;

/** Editable marketing copy (Store settings) with the original defaults. */
final class StorefrontContent
{
    /**
     * @return array<string, string|null>
     */
    public function home(): array
    {
        return [
            'hero_badge' => Setting::get('home_hero_badge', 'Baked in small batches'),
            'hero_title' => Setting::get('home_hero_title', 'Real banana bread,'),
            'hero_subtitle' => Setting::get('home_hero_subtitle', 'baked in Cavite.'),
            'hero_description' => Setting::get('home_hero_description', 'Classic banana bread, fudgy brownies, cheesecakes and cinnamon rolls from our home kitchen. Order online and we deliver around Cavite.'),
            'hero_btn_primary_text' => Setting::get('home_hero_btn_primary_text', 'See the menu'),
            'hero_btn_primary_url' => Setting::get('home_hero_btn_primary_url', '/shop'),
            'hero_btn_secondary_text' => Setting::get('home_hero_btn_secondary_text', 'Best sellers'),
            'hero_btn_secondary_url' => Setting::get('home_hero_btn_secondary_url', '/best-sellers'),
            'hero_bullet_1' => Setting::get('home_hero_bullet_1', 'Same-day or scheduled delivery'),
            'hero_bullet_2' => Setting::get('home_hero_bullet_2', 'Made with real butter'),
            'hero_card_badge' => Setting::get('home_hero_card_badge', 'Our bestseller'),
            'hero_card_title' => Setting::get('home_hero_card_title', 'Classic Banana Bread'),
            'hero_card_subtitle' => Setting::get('home_hero_card_subtitle', 'From ₱280'),
            'hero_card_image' => self::image(Setting::get('home_hero_card_image')),
            'spotlight_tagline' => Setting::get('home_spotlight_tagline', 'This week'),
            'spotlight_title' => Setting::get('home_spotlight_title', 'Signature Ube Cheesecake'),
            'spotlight_description' => Setting::get('home_spotlight_description', 'Ube halaya folded into baked cream cheese on a coconut graham crust. Small batches only, so order early.'),
            'spotlight_btn_text' => Setting::get('home_spotlight_btn_text', 'Order one · ₱680'),
            'spotlight_btn_url' => Setting::get('home_spotlight_btn_url', '/products/signature-ube-cheesecake-6-inch'),
            'spotlight_image' => self::image(Setting::get('home_spotlight_image')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function about(): array
    {
        $timeline = [
            ['year' => '2020', 'icon' => 'home', 'title' => 'A home kitchen', 'desc' => 'ABCDips & Treats started in a small home kitchen, baking banana bread and cookies for friends and family.'],
            ['year' => '2021', 'icon' => 'heart', 'title' => 'First online orders', 'desc' => 'Word spread, and we started taking orders on Facebook. Most weekends sold out.'],
            ['year' => '2023', 'icon' => 'sparkles', 'title' => 'A full menu and delivery', 'desc' => 'We added custom cakes, cheesecakes and cinnamon rolls, and started delivering.'],
        ];
        $values = [
            ['icon' => 'coffee', 'title' => 'Real ingredients', 'desc' => 'Real butter, Belgian chocolate and fresh eggs. Nothing we would not eat ourselves.'],
            ['icon' => 'heart', 'title' => 'Small batches', 'desc' => 'We bake by hand, a few trays at a time, so nothing sits on a shelf.'],
            ['icon' => 'star', 'title' => 'Local first', 'desc' => 'We buy from local suppliers where we can, and many of our customers are neighbours.'],
        ];

        return [
            'hero_tagline' => Setting::get('about_hero_tagline', 'our story'),
            'hero_title' => Setting::get('about_hero_title', "From a home kitchen\nin Cavite"),
            'hero_subtitle' => Setting::get('about_hero_subtitle', 'ABCDips & Treats started in 2020, baking banana bread for friends and family. We still bake everything ourselves, a few batches at a time.'),
            'timeline_tagline' => Setting::get('about_timeline_tagline', 'the journey'),
            'timeline_title' => Setting::get('about_timeline_title', 'How we got here'),
            'timeline' => Setting::getJson('about_timeline', $timeline),
            'values_tagline' => Setting::get('about_values_tagline', 'what drives us'),
            'values_title' => Setting::get('about_values_title', 'What we care about'),
            'values' => Setting::getJson('about_values', $values),
            'cta_tagline' => Setting::get('about_cta_tagline', 'order now'),
            'cta_title' => Setting::get('about_cta_title', 'Ready to order?'),
            'cta_subtitle' => Setting::get('about_cta_subtitle', 'Same-day delivery around Cavite.'),
            'cta_button_text' => Setting::get('about_cta_button_text', 'See the menu'),
            'cta_button_url' => Setting::get('about_cta_button_url', '/shop'),
        ];
    }

    private static function image(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return str_starts_with($path, '/') ? asset($path) : asset('storage/'.ltrim($path, '/'));
    }
}
