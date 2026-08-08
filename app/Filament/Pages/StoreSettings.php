<?php

namespace App\Filament\Pages;

use App\Models\Setting;
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
            'store_name' => Setting::get('store_name', 'ABCDips & Treats'),
            'store_phone' => Setting::get('store_phone', ''),
            'store_address' => Setting::get('store_address', 'Bacoor, Cavite, Philippines'),
            'store_lat' => Setting::get('store_lat', '14.4597'),
            'store_lng' => Setting::get('store_lng', '120.9640'),
            'store_email' => Setting::get('store_email', ''),

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

                        Textarea::make('store_address')
                            ->label('Store Pickup / Lalamove Origin Address')
                            ->helperText('Used as pickup origin for customer Lalamove quotes — specify street, barangay & city')
                            ->rows(2)
                            ->columnSpanFull()
                            ->required(),

                        ViewField::make('store_location_map')
                            ->view('filament.forms.components.store-location-map')
                            ->columnSpanFull(),

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

                Section::make('Lalamove Delivery API')
                    ->description('Automated doorstep delivery quotes. Register free at developers.lalamove.com.')
                    ->columns(2)
                    ->components([
                        TextInput::make('lalamove_api_key')
                            ->label('API Key')
                            ->placeholder('Your Lalamove API Key'),

                        TextInput::make('lalamove_api_secret')
                            ->label('API Secret')
                            ->password()
                            ->placeholder('Your Lalamove API Secret'),

                        Select::make('lalamove_service_type')
                            ->label('Vehicle / Service Type')
                            ->options([
                                'MOTORCYCLE' => 'Motorcycle (Standard Pastries)',
                                'SEDAN' => 'Sedan (Fragile Custom Cakes)',
                                'MPV' => 'MPV / Van (Bulk Orders)',
                            ])
                            ->default('MOTORCYCLE'),

                        Toggle::make('lalamove_sandbox')
                            ->label('Sandbox Mode')
                            ->helperText('Enable test environment (no real drivers dispatched)')
                            ->default(true),
                    ]),

                Section::make('PayMongo (GCash & Maya)')
                    ->description('Powers GCash & Maya checkout. Get test keys at dashboard.paymongo.com → API Keys.')
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
                            ->default(true)
                            ->columnSpanFull(),
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
                default => 'general',
            };

            if (is_array($value)) {
                Setting::setJson($key, $value, $group);
            } else {
                Setting::set($key, is_bool($value) ? ($value ? '1' : '0') : $value, $group);
            }
        }

        Cache::flush();
        Notification::make()->title('All Store Settings Saved Successfully!')->success()->send();
    }
}
