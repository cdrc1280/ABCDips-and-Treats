<?php

namespace App\Domains\Setting\Actions;

use App\Domains\Setting\Data\StoreSettingsData;
use App\Domains\Setting\Models\Setting;
use App\Domains\Setting\Support\StoreSettingsSchema;
use App\Shared\Services\PublicUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final class UpdateStoreSettingsAction
{
    public function __construct(private readonly PublicUploads $uploads) {}

    public function execute(StoreSettingsData $data): void
    {
        DB::transaction(function () use ($data) {
            foreach (StoreSettingsSchema::fields() as $key => $field) {
                $group = StoreSettingsSchema::group($key);
                $value = $data->values[$key] ?? null;

                switch ($field['type']) {
                    case 'secret':
                        // Write-only: an empty field means "keep the current secret".
                        if (is_string($value) && $value !== '') {
                            Setting::set($key, $value, $group);
                        }
                        break;

                    case 'image':
                        $current = (string) Setting::get($key, '');
                        $new = $value instanceof UploadedFile ? $value : null;
                        $remove = (bool) ($data->remove[$key] ?? false);
                        if ($new || $remove) {
                            Setting::set($key, (string) $this->uploads->replace($current ?: null, $new, 'settings', $remove), $group);
                        }
                        break;

                    case 'json_list':
                        if (array_key_exists($key, $data->values)) {
                            Setting::setJson($key, array_map(
                                fn (array $row) => array_intersect_key(array_map(fn ($v) => (string) ($v ?? ''), $row), $field['columns']),
                                (array) $value,
                            ), $group);
                        }
                        break;

                    case 'bool':
                        if (array_key_exists($key, $data->values)) {
                            Setting::set($key, $value ? '1' : '0', $group);
                        }
                        break;

                    default:
                        if (array_key_exists($key, $data->values)) {
                            Setting::set($key, (string) ($value ?? ''), $group);
                        }
                }
            }
        });
    }
}
