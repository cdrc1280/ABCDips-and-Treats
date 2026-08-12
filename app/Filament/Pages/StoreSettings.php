<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\PsgcService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class StoreSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';
    protected static ?string $navigationLabel = 'Store Settings';
    protected static ?int $navigationSort = 99;
    protected string $view = 'filament.pages.store-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $defaultTimeline = [
            ['year' => '2020', 'emoji' => '🏠', 'title' => 'Home Kitchen Beginnings', 'desc' => 'ABCDips & Treats started in a small home kitchen, baking banana bread and cookies for friends and family.'],
            ['year' => '2021', 'emoji' => '❤️', 'title' => 'First Online Orders', 'desc' => 'Word spread and we started taking online orders through social media, quickly selling out every weekend.'],
            ['year' => '2023', 'emoji' => '🥰', 'title' => 'Full Menu & Delivery', 'desc' => 'Expanded to our full pastry menu including custom cakes, cheesecakes, and cinnamon rolls with city-wide delivery.'],
        ];

        $defaultValues = [
            ['emoji' => '🫖', 'title' => 'Quality Ingredients', 'desc' => 'We use only real creamery butter, imported Belgian chocolate, and fresh farm eggs. No shortcuts, ever.'],
            ['emoji' => '❤️', 'title' => 'Made with Love', 'desc' => 'Every pastry is handcrafted in small batches by our dedicated bakers who pour passion into every bite.'],
            ['emoji' => '🌟', 'title' => 'Community First', 'desc' => 'We believe in building relationships, supporting local suppliers, and making people smile one pastry at a time.'],
        ];

        $this->form->fill([
            'store_name'           => Setting::get('store_name', 'ABCDips & Treats'),
            'store_phone'          => Setting::get('store_phone', ''),
            'store_region'         => Setting::get('store_region', 'Region IV-A (CALABARZON)'),
            'store_province'       => Setting::get('store_province', 'Cavite'),
            'store_city'           => Setting::get('store_city', 'City of Bacoor'),
            'store_barangay'       => Setting::get('store_barangay', 'Molino III'),
            'store_street_address' => Setting::get('store_street_address', 'Molino Blvd'),
            'store_address'        => Setting::get('store_address', 'Molino Blvd, Molino III, City of Bacoor, Cavite, Region IV-A (CALABARZON)'),
            'store_lat'            => Setting::get('store_lat', '14.4597'),
            'store_lng'            => Setting::get('store_lng', '120.9640'),
            'store_email'          => Setting::get('store_email', ''),

            'lalamove_api_key' => Setting::get('lalamove_api_key', ''),
            'lalamove_api_secret' => Setting::get('lalamove_api_secret', ''),
            'lalamove_service_type' => Setting::get('lalamove_service_type', 'MOTORCYCLE'),
            'lalamove_sandbox' => (bool) Setting::get('lalamove_sandbox', true),

            'paymongo_public_key' => Setting::get('paymongo_public_key', ''),
            'paymongo_secret_key' => Setting::get('paymongo_secret_key', ''),
            'paymongo_sandbox' => (bool) Setting::get('paymongo_sandbox', true),

            'bdo_account_name' => Setting::get('bdo_account_name', 'ABCDips & Treats'),
            'bdo_account_number' => Setting::get('bdo_account_number', '0012-3456-7890'),
            'bdo_instructions' => Setting::get('bdo_instructions', ''),

            'about_hero_tagline' => Setting::get('about_hero_tagline', 'our story'),
            'about_hero_title' => Setting::get('about_hero_title', "Baked with Heart,\nserved with love"),
            'about_hero_subtitle' => Setting::get('about_hero_subtitle', 'ABCDips & Treats began as a small home bakery with a simple dream: to share the joy of freshly baked, handcrafted pastries with every Filipino household.'),

            'about_timeline_tagline' => Setting::get('about_timeline_tagline', 'the journey'),
            'about_timeline_title' => Setting::get('about_timeline_title', 'The ABCDips Story'),
            'about_timeline' => Setting::getJson('about_timeline', $defaultTimeline),

            'about_values_tagline' => Setting::get('about_values_tagline', 'what drives us'),
            'about_values_title' => Setting::get('about_values_title', 'Our Core Values'),
            'about_values' => Setting::getJson('about_values', $defaultValues),

            'about_cta_tagline' => Setting::get('about_cta_tagline', 'ready to indulge?'),
            'about_cta_title' => Setting::get('about_cta_title', 'Order Your Favorites Today'),
            'about_cta_subtitle' => Setting::get('about_cta_subtitle', 'Same-day delivery available in Cavite. Fresh from our oven to your door.'),
            'about_cta_button_text' => Setting::get('about_cta_button_text', 'Browse Full Menu →'),
            'about_cta_button_url' => Setting::get('about_cta_button_url', '/shop'),

            'home_hero_badge' => Setting::get('home_hero_badge', 'OVEN FRESH TODAY IN CAVITE'),
            'home_hero_title' => Setting::get('home_hero_title', 'Handcrafted Pastries'),
            'home_hero_subtitle' => Setting::get('home_hero_subtitle', 'baked with love & real butter'),
            'home_hero_description' => Setting::get('home_hero_description', 'From our famous Classic Banana Bread Loaves and ultra-fudgy Belgian chocolate brownies to cheesecakes and fresh cinnamon rolls.'),
            'home_hero_btn_primary_text' => Setting::get('home_hero_btn_primary_text', 'Browse Full Menu'),
            'home_hero_btn_primary_url' => Setting::get('home_hero_btn_primary_url', '/shop'),
            'home_hero_btn_secondary_text' => Setting::get('home_hero_btn_secondary_text', 'Explore Best Sellers'),
            'home_hero_btn_secondary_url' => Setting::get('home_hero_btn_secondary_url', '/best-sellers'),
            'home_hero_bullet_1' => Setting::get('home_hero_bullet_1', 'Same-day & Scheduled Delivery'),
            'home_hero_bullet_2' => Setting::get('home_hero_bullet_2', '100% Real Creamery Butter'),
            'home_hero_card_badge' => Setting::get('home_hero_card_badge', 'Signature Treat'),
            'home_hero_card_title' => Setting::get('home_hero_card_title', 'Classic Banana Bread'),
            'home_hero_card_subtitle' => Setting::get('home_hero_card_subtitle', 'Starts at ₱280.00'),
            'home_hero_card_image' => Setting::get('home_hero_card_image'),

            'home_spotlight_tagline' => Setting::get('home_spotlight_tagline', 'weekly special spotlight'),
            'home_spotlight_title' => Setting::get('home_spotlight_title', 'Signature Ube Cheesecake'),
            'home_spotlight_description' => Setting::get('home_spotlight_description', 'Real Philippine Ube Halaya folded into silky baked cream cheese set over a coconut Graham crust. Baked fresh in limited batches.'),
            'home_spotlight_btn_text' => Setting::get('home_spotlight_btn_text', 'Order Spotlight Treat — ₱680.00'),
            'home_spotlight_btn_url' => Setting::get('home_spotlight_btn_url', '/products/signature-ube-cheesecake-6-inch'),
            'home_spotlight_image' => Setting::get('home_spotlight_image'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Store Information')
                    ->description('Your bakery\'s address and contact details. Coordinates are used for Lalamove pickup location.')
                    ->columns(2)
                    ->components([
                        TextInput::make('store_name')
                            ->label('Store Name')
                            ->required(),

                        TextInput::make('store_phone')
                            ->label('Store Phone')
                            ->placeholder('0917 123 4567'),

                        Select::make('store_region')
                            ->label('Store Region')
                            ->options(fn () => PsgcService::getRegionsOptions())
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('store_province', null);
                                $set('store_city', null);
                                $set('store_barangay', null);
                                static::updateCompiledAddress($set, $get);
                            }),

                        Select::make('store_province')
                            ->label('Store Province')
                            ->options(fn (callable $get) => PsgcService::getProvincesOptions($get('store_region')))
                            ->searchable()
                            ->live()
                            ->disabled(fn (callable $get) => ! $get('store_region') || $get('store_region') === '130000000')
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('store_city', null);
                                $set('store_barangay', null);
                                static::updateCompiledAddress($set, $get);
                            }),

                        Select::make('store_city')
                            ->label('Store City / Municipality')
                            ->options(fn (callable $get) => PsgcService::getCitiesOptions($get('store_region'), $get('store_province')))
                            ->searchable()
                            ->live()
                            ->disabled(fn (callable $get) => ! $get('store_region'))
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('store_barangay', null);
                                static::updateCompiledAddress($set, $get);
                            }),

                        Select::make('store_barangay')
                            ->label('Store Barangay')
                            ->options(fn (callable $get) => PsgcService::getBarangaysOptions($get('store_city')))
                            ->searchable()
                            ->live()
                            ->disabled(fn (callable $get) => ! $get('store_city'))
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::updateCompiledAddress($set, $get);
                            }),

                        TextInput::make('store_street_address')
                            ->label('Store Street Address / Building / House #')
                            ->placeholder('e.g. 123 Zapote Road, Phase 1')
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::updateCompiledAddress($set, $get);
                            }),

                        Textarea::make('store_address')
                            ->label('Compiled Store Pickup Address')
                            ->helperText('Automatically compiled from selected PSGC region, province, city, barangay, and street address.')
                            ->rows(2)
                            ->columnSpanFull()
                            ->readOnly(),

                        TextInput::make('store_lat')
                            ->label('Store Latitude (GPS)')
                            ->placeholder('14.4597')
                            ->helperText('Get from Google Maps: right-click store location → copy coordinates'),

                        TextInput::make('store_lng')
                            ->label('Store Longitude (GPS)')
                            ->placeholder('120.9640'),

                        TextInput::make('store_email')
                            ->label('Store Support Email')
                            ->email()
                            ->columnSpanFull(),
                    ]),

                Section::make('Homepage Hero Banner')
                    ->description('Customize the main hero banner headlines, trust bullets, CTA buttons, and showcase treat card.')
                    ->columns(2)
                    ->components([
                        TextInput::make('home_hero_badge')
                            ->label('Top Badge Text')
                            ->placeholder('OVEN FRESH TODAY IN CAVITE')
                            ->required(),

                        TextInput::make('home_hero_title')
                            ->label('Main Headline Title')
                            ->placeholder('Handcrafted Pastries')
                            ->required(),

                        TextInput::make('home_hero_subtitle')
                            ->label('Script Subheading Text')
                            ->placeholder('baked with love & real butter')
                            ->required(),

                        Textarea::make('home_hero_description')
                            ->label('Paragraph Description')
                            ->rows(2)
                            ->columnSpanFull()
                            ->required(),

                        TextInput::make('home_hero_btn_primary_text')
                            ->label('Primary Button Label')
                            ->placeholder('Browse Full Menu'),

                        TextInput::make('home_hero_btn_primary_url')
                            ->label('Primary Button Destination URL')
                            ->placeholder('/shop'),

                        TextInput::make('home_hero_btn_secondary_text')
                            ->label('Secondary Button Label')
                            ->placeholder('Explore Best Sellers'),

                        TextInput::make('home_hero_btn_secondary_url')
                            ->label('Secondary Button Destination URL')
                            ->placeholder('/best-sellers'),

                        TextInput::make('home_hero_bullet_1')
                            ->label('Trust Bullet Point 1')
                            ->placeholder('Same-day & Scheduled Delivery'),

                        TextInput::make('home_hero_bullet_2')
                            ->label('Trust Bullet Point 2')
                            ->placeholder('100% Real Creamery Butter'),

                        TextInput::make('home_hero_card_badge')
                            ->label('Showcase Card Badge Label')
                            ->placeholder('Signature Treat'),

                        TextInput::make('home_hero_card_title')
                            ->label('Showcase Card Item Title')
                            ->placeholder('Classic Banana Bread'),

                        TextInput::make('home_hero_card_subtitle')
                            ->label('Showcase Card Price / Subtitle')
                            ->placeholder('Starts at ₱280.00'),

                        FileUpload::make('home_hero_card_image')
                            ->label('Showcase Card Product Image')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('settings')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),

                Section::make('Homepage Weekly Spotlight Banner')
                    ->description('Customize the weekly featured pastry spotlight banner, title, description, pricing, and showcase image.')
                    ->columns(2)
                    ->components([
                        TextInput::make('home_spotlight_tagline')
                            ->label('Script Tagline')
                            ->placeholder('weekly special spotlight')
                            ->required(),

                        TextInput::make('home_spotlight_title')
                            ->label('Spotlight Product Title')
                            ->placeholder('Signature Ube Cheesecake')
                            ->required(),

                        Textarea::make('home_spotlight_description')
                            ->label('Spotlight Description')
                            ->rows(2)
                            ->columnSpanFull()
                            ->required(),

                        TextInput::make('home_spotlight_btn_text')
                            ->label('Button Label & Price')
                            ->placeholder('Order Spotlight Treat — ₱680.00')
                            ->required(),

                        TextInput::make('home_spotlight_btn_url')
                            ->label('Button Destination Link')
                            ->placeholder('/products/signature-ube-cheesecake-6-inch')
                            ->required(),

                        FileUpload::make('home_spotlight_image')
                            ->label('Spotlight Product Image')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('settings')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),

                Section::make('About Us Page Content')
                    ->description('Modify all titles, subtitles, timeline milestones, and core values shown on the public /about page.')
                    ->columns(2)
                    ->components([
                        TextInput::make('about_hero_tagline')
                            ->label('Hero Script Tagline')
                            ->placeholder('our story')
                            ->required(),

                        TextInput::make('about_hero_title')
                            ->label('Hero Heading')
                            ->placeholder('Baked with Heart, served with love')
                            ->required(),

                        Textarea::make('about_hero_subtitle')
                            ->label('Hero Description')
                            ->rows(2)
                            ->columnSpanFull()
                            ->required(),

                        TextInput::make('about_timeline_tagline')
                            ->label('Timeline Script Tagline')
                            ->placeholder('the journey'),

                        TextInput::make('about_timeline_title')
                            ->label('Timeline Heading')
                            ->placeholder('The ABCDips Story'),

                        Repeater::make('about_timeline')
                            ->label('Bakery Milestones Timeline')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('year')->label('Year')->required(),
                                TextInput::make('emoji')->label('Emoji Icon')->placeholder('🏠'),
                                TextInput::make('title')->label('Milestone Title')->required(),
                                Textarea::make('desc')->label('Milestone Description')->rows(2)->required(),
                            ])
                            ->columns(3),

                        TextInput::make('about_values_tagline')
                            ->label('Values Script Tagline')
                            ->placeholder('what drives us'),

                        TextInput::make('about_values_title')
                            ->label('Values Heading')
                            ->placeholder('Our Core Values'),

                        Repeater::make('about_values')
                            ->label('Bakery Core Values')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('emoji')->label('Emoji Icon')->placeholder('🫖'),
                                TextInput::make('title')->label('Value Title')->required(),
                                Textarea::make('desc')->label('Value Description')->rows(2)->required(),
                            ])
                            ->columns(3),

                        TextInput::make('about_cta_tagline')
                            ->label('CTA Script Tagline')
                            ->placeholder('ready to indulge?'),

                        TextInput::make('about_cta_title')
                            ->label('CTA Heading')
                            ->placeholder('Order Your Favorites Today'),

                        Textarea::make('about_cta_subtitle')
                            ->label('CTA Subtitle')
                            ->rows(2)
                            ->columnSpanFull(),

                        TextInput::make('about_cta_button_text')
                            ->label('CTA Button Label')
                            ->placeholder('Browse Full Menu →'),

                        TextInput::make('about_cta_button_url')
                            ->label('CTA Button Destination URL')
                            ->placeholder('/shop'),
                    ]),

                Section::make('Lalamove Delivery API (Free Distance-Based Quoting)')
                    ->description('Free real-time distance & delivery fee calculation using Lalamove v3 Sandbox (/v3/quotations). Get your free Sandbox API Key & Secret from partner.lalamove.com.')
                    ->columns(2)
                    ->components([
                        TextInput::make('lalamove_api_key')
                            ->label('Sandbox / API Key')
                            ->placeholder('Your Lalamove API Key')
                            ->helperText('Get free API Key at partner.lalamove.com → Developers'),

                        TextInput::make('lalamove_api_secret')
                            ->label('Sandbox / API Secret')
                            ->password()
                            ->placeholder('Your Lalamove API Secret')
                            ->helperText('Get free API Secret at partner.lalamove.com → Developers'),

                        Select::make('lalamove_service_type')
                            ->label('Vehicle / Service Type')
                            ->options([
                                'MOTORCYCLE' => 'Motorcycle (Standard Pastries)',
                                'SEDAN'      => 'Sedan (Fragile Custom Cakes)',
                                'MPV'        => 'MPV / Van (Bulk Orders)',
                            ])
                            ->default('MOTORCYCLE'),

                        Toggle::make('lalamove_sandbox')
                            ->label('Sandbox Mode (100% Free - Zero Payment Required)')
                            ->helperText('Uses https://rest.sandbox.lalamove.com/v3/quotations for 100% free price/distance calculation without charging any wallet.')
                            ->default(true),
                    ]),

                Section::make('PayMongo Payment Gateway (GCash, Maya & QR Ph)')
                    ->description('Powers GCash, Maya & QR Ph checkout. Get test keys at dashboard.paymongo.com → API Keys.')
                    ->columns(2)
                    ->components([
                        TextInput::make('paymongo_public_key')
                            ->label('Public Key')
                            ->placeholder('pk_test_...'),

                        TextInput::make('paymongo_secret_key')
                            ->label('Secret Key')
                            ->password()
                            ->placeholder('sk_test_...'),

                        Toggle::make('paymongo_sandbox')
                            ->label('Sandbox Mode')
                            ->helperText(app()->environment(['local', 'testing']) ? 'Use PayMongo test environment (Test GCash number: 09123456789, OTP: 123456)' : null)
                            ->default(true),

                        Toggle::make('enable_qrph')
                            ->label('Enable QR Ph Payment Option')
                            ->helperText('Allow customers to pay via QR Ph (scan using any Philippine bank or e-wallet app at checkout).')
                            ->default(true),
                    ]),

                Section::make('BDO Payment Details')
                    ->description('Set your BDO bank transfer account name and account number shown to customers at checkout.')
                    ->columns(2)
                    ->components([
                        TextInput::make('bdo_account_name')
                            ->label('BDO Account Name')
                            ->placeholder('ABCDips & Treats')
                            ->default('ABCDips & Treats')
                            ->required(),

                        TextInput::make('bdo_account_number')
                            ->label('BDO Account Number')
                            ->placeholder('0012-3456-7890')
                            ->default('0012-3456-7890')
                            ->required(),

                        Textarea::make('bdo_instructions')
                            ->label('Custom BDO Payment Instructions (Optional)')
                            ->placeholder('e.g. Please present deposit slip or reference code upon delivery/pickup.')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    protected static function updateCompiledAddress(callable $set, callable $get): void
    {
        $regCode = $get('store_region');
        $provCode = $get('store_province');
        $cityCode = $get('store_city');
        $brgyCode = $get('store_barangay');
        $street = $get('store_street_address');

        $regions = PsgcService::getRegionsOptions();
        $provinces = PsgcService::getProvincesOptions($regCode);
        $cities = PsgcService::getCitiesOptions($regCode, $provCode);
        $barangays = PsgcService::getBarangaysOptions($cityCode);

        $regName = $regions[$regCode] ?? $regCode;
        $provName = $provinces[$provCode] ?? ($regCode === '130000000' ? 'Metro Manila' : $provCode);
        $cityName = $cities[$cityCode] ?? $cityCode;
        $brgyName = $barangays[$brgyCode] ?? $brgyCode;

        $parts = array_filter([$street, $brgyName, $cityName, $provName, $regName]);
        $set('store_address', implode(', ', $parts));
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            $group = match (true) {
                str_starts_with($key, 'store_') => 'store',
                str_starts_with($key, 'lalamove_') => 'lalamove',
                str_starts_with($key, 'paymongo_') => 'paymongo',
                str_starts_with($key, 'bdo_') => 'bdo',
                str_starts_with($key, 'about_') => 'about',
                str_starts_with($key, 'home_') => 'home',
                default => 'general',
            };

            if (is_array($value)) {
                // If FileUpload component returns an array of paths, pick first file path
                if (isset($value[0]) && is_string($value[0])) {
                    Setting::set($key, $value[0], $group);
                } else {
                    Setting::setJson($key, $value, $group);
                }
            } else {
                Setting::set($key, is_bool($value) ? ($value ? '1' : '0') : (string) ($value ?? ''), $group);
            }
        }

        Cache::flush();
        Notification::make()->title('All Store & Homepage Settings Saved Successfully!')->success()->send();
    }
}
