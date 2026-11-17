<?php

namespace App\Domains\Setting\Support;

/**
 * Every store setting the back office edits: section, key, input type, validation flag and
 * default. Single source for the settings page, its validation and its save action.
 * Types: text email textarea bool image select secret json_list.
 */
final class StoreSettingsSchema
{
    /**
     * @return list<array{title: string, fields: list<array<string, mixed>>}>
     */
    public static function sections(): array
    {
        $defaultTimeline = [
            ['year' => '2020', 'emoji' => '🏠', 'title' => 'A home kitchen', 'desc' => 'ABCDips & Treats started in a small home kitchen, baking banana bread and cookies for friends and family.'],
            ['year' => '2021', 'emoji' => '❤️', 'title' => 'First online orders', 'desc' => 'Word spread, and we started taking orders on Facebook. Most weekends sold out.'],
            ['year' => '2023', 'emoji' => '🥰', 'title' => 'A full menu and delivery', 'desc' => 'We added custom cakes, cheesecakes and cinnamon rolls, and started delivering.'],
        ];
        $defaultValues = [
            ['emoji' => '🫖', 'title' => 'Real ingredients', 'desc' => 'Real butter, Belgian chocolate and fresh eggs. Nothing we would not eat ourselves.'],
            ['emoji' => '❤️', 'title' => 'Small batches', 'desc' => 'We bake by hand, a few trays at a time, so nothing sits on a shelf.'],
            ['emoji' => '🌟', 'title' => 'Local first', 'desc' => 'We buy from local suppliers where we can, and many of our customers are neighbours.'],
        ];

        return [
            ['title' => 'Store information', 'fields' => [
                ['key' => 'store_name', 'label' => 'Name', 'type' => 'text', 'required' => true, 'default' => 'ABCDips & Treats'],
                ['key' => 'store_phone', 'label' => 'Phone', 'type' => 'tel', 'required' => false, 'default' => ''],
                ['key' => 'store_region', 'label' => 'Region', 'type' => 'text', 'required' => false, 'default' => 'Region IV-A (CALABARZON)'],
                ['key' => 'store_province', 'label' => 'Province', 'type' => 'text', 'required' => false, 'default' => 'Cavite'],
                ['key' => 'store_city', 'label' => 'City', 'type' => 'text', 'required' => false, 'default' => 'City of Bacoor'],
                ['key' => 'store_barangay', 'label' => 'Barangay', 'type' => 'text', 'required' => false, 'default' => 'Molino III'],
                ['key' => 'store_street_address', 'label' => 'Street address', 'type' => 'text', 'required' => false, 'default' => 'Molino Blvd'],
                ['key' => 'store_address', 'label' => 'Address', 'type' => 'textarea', 'required' => false, 'default' => 'Molino Blvd, Molino III, City of Bacoor, Cavite, Region IV-A (CALABARZON)'],
                ['key' => 'store_lat', 'label' => 'Lat', 'type' => 'number', 'signed' => true, 'required' => false, 'default' => '14.4597'],
                ['key' => 'store_lng', 'label' => 'Lng', 'type' => 'number', 'signed' => true, 'required' => false, 'default' => '120.9640'],
                ['key' => 'store_email', 'label' => 'Email', 'type' => 'email', 'required' => false, 'default' => ''],
            ]],
            ['title' => 'Homepage hero', 'fields' => [
                ['key' => 'home_hero_badge', 'label' => 'Badge', 'type' => 'text', 'required' => true, 'default' => 'Baked in small batches'],
                ['key' => 'home_hero_title', 'label' => 'Title', 'type' => 'text', 'required' => true, 'default' => 'Real banana bread,'],
                ['key' => 'home_hero_subtitle', 'label' => 'Subtitle', 'type' => 'text', 'required' => true, 'default' => 'baked in Cavite.'],
                ['key' => 'home_hero_description', 'label' => 'Description', 'type' => 'textarea', 'required' => true, 'default' => 'Classic banana bread, fudgy brownies, cheesecakes and cinnamon rolls from our home kitchen. Order online and we deliver around Cavite.'],
                ['key' => 'home_hero_btn_primary_text', 'label' => 'Btn primary text', 'type' => 'text', 'required' => false, 'default' => 'See the menu'],
                ['key' => 'home_hero_btn_primary_url', 'label' => 'Btn primary url', 'type' => 'text', 'required' => false, 'default' => '/shop'],
                ['key' => 'home_hero_btn_secondary_text', 'label' => 'Btn secondary text', 'type' => 'text', 'required' => false, 'default' => 'Best sellers'],
                ['key' => 'home_hero_btn_secondary_url', 'label' => 'Btn secondary url', 'type' => 'text', 'required' => false, 'default' => '/best-sellers'],
                ['key' => 'home_hero_bullet_1', 'label' => 'Bullet 1', 'type' => 'text', 'required' => false, 'default' => 'Same-day or scheduled delivery'],
                ['key' => 'home_hero_bullet_2', 'label' => 'Bullet 2', 'type' => 'text', 'required' => false, 'default' => 'Made with real butter'],
                ['key' => 'home_hero_card_badge', 'label' => 'Card badge', 'type' => 'text', 'required' => false, 'default' => 'Our bestseller'],
                ['key' => 'home_hero_card_title', 'label' => 'Card title', 'type' => 'text', 'required' => false, 'default' => 'Classic Banana Bread'],
                ['key' => 'home_hero_card_subtitle', 'label' => 'Card subtitle', 'type' => 'text', 'required' => false, 'default' => 'From ₱280'],
                ['key' => 'home_hero_card_image', 'label' => 'Card image', 'type' => 'image', 'required' => false, 'default' => ''],
            ]],
            ['title' => 'Homepage spotlight', 'fields' => [
                ['key' => 'home_spotlight_tagline', 'label' => 'Tagline', 'type' => 'text', 'required' => true, 'default' => 'This week'],
                ['key' => 'home_spotlight_title', 'label' => 'Title', 'type' => 'text', 'required' => true, 'default' => 'Signature Ube Cheesecake'],
                ['key' => 'home_spotlight_description', 'label' => 'Description', 'type' => 'textarea', 'required' => true, 'default' => 'Ube halaya folded into baked cream cheese on a coconut graham crust. Small batches only, so order early.'],
                ['key' => 'home_spotlight_btn_text', 'label' => 'Btn text', 'type' => 'text', 'required' => true, 'default' => 'Order one · ₱680'],
                ['key' => 'home_spotlight_btn_url', 'label' => 'Btn url', 'type' => 'text', 'required' => true, 'default' => '/products/signature-ube-cheesecake-6-inch'],
                ['key' => 'home_spotlight_image', 'label' => 'Image', 'type' => 'image', 'required' => false, 'default' => ''],
            ]],
            ['title' => 'About page', 'fields' => [
                ['key' => 'about_hero_tagline', 'label' => 'Hero tagline', 'type' => 'text', 'required' => true, 'default' => 'our story'],
                ['key' => 'about_hero_title', 'label' => 'Hero title', 'type' => 'text', 'required' => true, 'default' => "From a home kitchen\nin Cavite"],
                ['key' => 'about_hero_subtitle', 'label' => 'Hero subtitle', 'type' => 'textarea', 'required' => true, 'default' => 'ABCDips & Treats started in 2020, baking banana bread for friends and family. We still bake everything ourselves, a few batches at a time.'],
                ['key' => 'about_timeline_tagline', 'label' => 'Timeline tagline', 'type' => 'text', 'required' => false, 'default' => 'the journey'],
                ['key' => 'about_timeline_title', 'label' => 'Timeline title', 'type' => 'text', 'required' => false, 'default' => 'How we got here'],
                ['key' => 'about_timeline', 'label' => 'Timeline', 'type' => 'json_list', 'required' => false, 'default' => $defaultTimeline, 'columns' => ['year' => 'Year', 'emoji' => 'Emoji', 'title' => 'Title', 'desc' => 'Description']],
                ['key' => 'about_values_tagline', 'label' => 'Values tagline', 'type' => 'text', 'required' => false, 'default' => 'what drives us'],
                ['key' => 'about_values_title', 'label' => 'Values title', 'type' => 'text', 'required' => false, 'default' => 'What we care about'],
                ['key' => 'about_values', 'label' => 'Values', 'type' => 'json_list', 'required' => false, 'default' => $defaultValues, 'columns' => ['emoji' => 'Emoji', 'title' => 'Title', 'desc' => 'Description']],
                ['key' => 'about_cta_tagline', 'label' => 'Cta tagline', 'type' => 'text', 'required' => false, 'default' => 'order now'],
                ['key' => 'about_cta_title', 'label' => 'Cta title', 'type' => 'text', 'required' => false, 'default' => 'Ready to order?'],
                ['key' => 'about_cta_subtitle', 'label' => 'Cta subtitle', 'type' => 'textarea', 'required' => false, 'default' => 'Same-day delivery around Cavite.'],
                ['key' => 'about_cta_button_text', 'label' => 'Cta button text', 'type' => 'text', 'required' => false, 'default' => 'See the menu'],
                ['key' => 'about_cta_button_url', 'label' => 'Cta button url', 'type' => 'text', 'required' => false, 'default' => '/shop'],
            ]],
            ['title' => 'Lalamove delivery', 'fields' => [
                ['key' => 'lalamove_api_key', 'label' => 'Api key', 'type' => 'text', 'required' => false, 'default' => ''],
                ['key' => 'lalamove_api_secret', 'label' => 'Api secret', 'type' => 'secret', 'required' => false, 'default' => ''],
                ['key' => 'lalamove_service_type', 'label' => 'Service type', 'type' => 'select', 'required' => false, 'default' => 'MOTORCYCLE', 'options' => ['MOTORCYCLE' => 'Motorcycle', 'MPV' => 'MPV (car)', 'VAN' => 'Van', 'TRUCK175' => 'Light truck']],
                ['key' => 'lalamove_sandbox', 'label' => 'Sandbox', 'type' => 'bool', 'required' => false, 'default' => true],
            ]],
            ['title' => 'PayMongo (GCash, Maya, QR Ph)', 'fields' => [
                ['key' => 'paymongo_public_key', 'label' => 'Public key', 'type' => 'text', 'required' => false, 'default' => ''],
                ['key' => 'paymongo_secret_key', 'label' => 'Secret key', 'type' => 'secret', 'required' => false, 'default' => ''],
                ['key' => 'paymongo_sandbox', 'label' => 'Sandbox', 'type' => 'bool', 'required' => false, 'default' => true],
                ['key' => 'enable_qrph', 'label' => 'Enable qrph', 'type' => 'bool', 'required' => false, 'default' => false],
            ]],
            ['title' => 'BDO bank transfer', 'fields' => [
                ['key' => 'bdo_account_name', 'label' => 'Account name', 'type' => 'text', 'required' => true, 'default' => 'ABCDips & Treats'],
                ['key' => 'bdo_account_number', 'label' => 'Account number', 'type' => 'text', 'required' => true, 'default' => '0012-3456-7890'],
                ['key' => 'bdo_instructions', 'label' => 'Instructions', 'type' => 'textarea', 'required' => false, 'default' => ''],
            ]],
            // Statutory payroll rates (semi-monthly). Confirm against current SSS / PhilHealth / Pag-IBIG / BIR tables.
            ['title' => 'Payroll rates and pay policy (semi-monthly)', 'fields' => [
                ['key' => 'payroll_sss_rate', 'label' => 'SSS employee share (%)', 'type' => 'number', 'required' => false, 'default' => '5'],
                ['key' => 'payroll_sss_cap', 'label' => 'SSS maximum per cut-off (₱)', 'type' => 'number', 'required' => false, 'default' => '875'],
                ['key' => 'payroll_philhealth_rate', 'label' => 'PhilHealth employee share (%)', 'type' => 'number', 'required' => false, 'default' => '2.5'],
                ['key' => 'payroll_pagibig_amount', 'label' => 'Pag-IBIG per cut-off (₱)', 'type' => 'number', 'required' => false, 'default' => '100'],
                ['key' => 'payroll_tax_threshold', 'label' => 'Tax-free gross per cut-off (₱)', 'type' => 'number', 'required' => false, 'default' => '10416'],
                ['key' => 'payroll_tax_rate', 'label' => 'Tax rate above the threshold (%)', 'type' => 'number', 'required' => false, 'default' => '15'],
                // Pay policy (audit A16): how attendance changes pay. Defaults = Labor Code minimums.
                ['key' => 'payroll_work_days_per_year', 'label' => 'Working days per year (daily rate = monthly × 12 ÷ this; 313 = 6-day week, 261 = 5-day)', 'type' => 'number', 'required' => false, 'default' => '313'],
                ['key' => 'payroll_hours_per_day', 'label' => 'Hours per working day', 'type' => 'number', 'required' => false, 'default' => '8'],
                ['key' => 'payroll_overtime_premium', 'label' => 'Overtime premium (% on top of the hourly rate)', 'type' => 'number', 'required' => false, 'default' => '25'],
                ['key' => 'payroll_holiday_premium', 'label' => 'Holiday worked premium (% of the daily rate)', 'type' => 'number', 'required' => false, 'default' => '100'],
                ['key' => 'payroll_deduct_absences', 'label' => 'Deduct absences and half days (no work, no pay)', 'type' => 'bool', 'required' => false, 'default' => true],
                ['key' => 'payroll_deduct_late', 'label' => 'Deduct late minutes', 'type' => 'bool', 'required' => false, 'default' => true],
                ['key' => 'payroll_shift_start', 'label' => 'Shift start (HH:MM, lateness counts from here)', 'type' => 'text', 'required' => false, 'default' => '08:00'],
                ['key' => 'payroll_late_grace_minutes', 'label' => 'Late grace period (minutes)', 'type' => 'number', 'required' => false, 'default' => '0'],
            ]],
        ];
    }

    /**
     * @return array<string, array<string, mixed>> key => field definition
     */
    public static function fields(): array
    {
        $fields = [];
        foreach (self::sections() as $section) {
            foreach ($section['fields'] as $field) {
                $fields[$field['key']] = $field;
            }
        }

        return $fields;
    }

    public static function group(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'store_') => 'store',
            str_starts_with($key, 'lalamove_') => 'lalamove',
            str_starts_with($key, 'paymongo_'), $key === 'enable_qrph' => 'paymongo',
            str_starts_with($key, 'bdo_') => 'bdo',
            str_starts_with($key, 'about_') => 'about',
            str_starts_with($key, 'home_') => 'home',
            default => 'general',
        };
    }
}
