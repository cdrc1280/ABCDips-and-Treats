<?php

namespace App\Domains\Setting\Controllers;

use App\Domains\Setting\Actions\UpdateStoreSettingsAction;
use App\Domains\Setting\Data\StoreSettingsData;
use App\Domains\Setting\Enums\SettingPermission;
use App\Domains\Setting\Models\Setting;
use App\Domains\Setting\Support\StoreSettingsSchema;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class StoreSettingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.SettingPermission::SettingsView->value, only: ['edit']),
            new Middleware('can:'.SettingPermission::SettingsEdit->value, only: ['update']),
        ];
    }

    public function edit(): Response
    {
        $values = [];
        $secretsSet = [];
        $imageUrls = [];

        foreach (StoreSettingsSchema::fields() as $key => $field) {
            $stored = $field['type'] === 'json_list'
                ? Setting::getJson($key, $field['default'])
                : Setting::get($key, $field['default']);

            switch ($field['type']) {
                case 'secret':
                    $secretsSet[$key] = (string) $stored !== '';
                    $values[$key] = '';   // never send secrets to the browser
                    break;
                case 'image':
                    $path = (string) $stored;
                    $imageUrls[$key] = $path === '' ? null : (str_starts_with($path, 'http') ? $path : asset('storage/'.ltrim($path, '/')));
                    $values[$key] = null;
                    break;
                case 'bool':
                    $values[$key] = filter_var($stored, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'json_list':
                    $values[$key] = array_map(fn ($row) => array_map(fn ($v) => (string) ($v ?? ''), (array) $row), (array) $stored);
                    break;
                default:
                    $values[$key] = (string) ($stored ?? '');
            }
        }

        return Inertia::render('settings/index', [
            'sections' => StoreSettingsSchema::sections(),
            'values' => $values,
            'secrets_set' => $secretsSet,
            'image_urls' => $imageUrls,
        ]);
    }

    public function update(StoreSettingsData $data, UpdateStoreSettingsAction $update): RedirectResponse
    {
        $update->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Store settings saved.')]);

        return to_route('admin.settings.edit');
    }
}
