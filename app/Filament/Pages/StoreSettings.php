<?php

namespace App\Filament\Pages;

use App\Models\Setting;
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

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static string|\UnitEnum|null   $navigationGroup = 'Configuration';
    protected static ?string                 $navigationLabel = 'Store Settings';
    protected static ?int                    $navigationSort  = 99;
    protected string                         $view = 'filament.pages.store-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'store_name'            => Setting::get('store_name', 'ABCDips & Treats'),
            'store_phone'           => Setting::get('store_phone', ''),
            'store_address'         => Setting::get('store_address', 'Bacoor, Cavite, Philippines'),
            'store_lat'             => Setting::get('store_lat', '14.4597'),
            'store_lng'             => Setting::get('store_lng', '120.9640'),
            'store_email'           => Setting::get('store_email', ''),

            'lalamove_api_key'      => Setting::get('lalamove_api_key', ''),
            'lalamove_api_secret'   => Setting::get('lalamove_api_secret', ''),
            'lalamove_service_type' => Setting::get('lalamove_service_type', 'MOTORCYCLE'),
            'lalamove_sandbox'      => (bool) Setting::get('lalamove_sandbox', true),

            'paymongo_public_key'   => Setting::get('paymongo_public_key', ''),
            'paymongo_secret_key'   => Setting::get('paymongo_secret_key', ''),
            'paymongo_sandbox'      => (bool) Setting::get('paymongo_sandbox', true),

            'bdo_account_name'      => Setting::get('bdo_account_name', 'ABCDips & Treats'),
            'bdo_account_number'    => Setting::get('bdo_account_number', '0012-3456-7890'),
            'bdo_instructions'      => Setting::get('bdo_instructions', ''),
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
                                'SEDAN'      => 'Sedan (Fragile Custom Cakes)',
                                'MPV'        => 'MPV / Van (Bulk Orders)',
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
                            ->helperText('Use PayMongo test environment (Test GCash number: 09123456789, OTP: 123456)')
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
                default => 'general',
            };

            Setting::set($key, is_bool($value) ? ($value ? '1' : '0') : $value, $group);
        }

        Cache::flush();
        Notification::make()->title('All Store Settings Saved Successfully!')->success()->send();
    }
}
