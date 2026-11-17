<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Phones are now strictly 11 digits starting with 09 (App\Rules\PhilippinePhone).
 * Rewrites stored numbers that convert cleanly ("+63 917 123 4567", "0917-123-4567")
 * so their edit forms still save. Anything else (landlines, typos) is left as is
 * and must be corrected by hand the next time the record is edited.
 */
return new class extends Migration
{
    /** @var array<string, string> */
    private array $columns = [
        'users' => 'phone',
        'employees' => 'phone',
        'suppliers' => 'phone',
        'contact_messages' => 'phone',
        'orders' => 'customer_phone',
        'custom_orders' => 'customer_phone',
    ];

    public function up(): void
    {
        foreach ($this->columns as $table => $column) {
            DB::table($table)->whereNotNull($column)->orderBy('id')->select(['id', $column])
                ->each(function (object $row) use ($table, $column) {
                    $normalized = $this->normalize((string) $row->{$column});
                    if ($normalized !== null && $normalized !== $row->{$column}) {
                        DB::table($table)->where('id', $row->id)->update([$column => $normalized]);
                    }
                });
        }

        $store = DB::table('settings')->where('key', 'store_phone')->value('value');
        if (is_string($store) && ($normalized = $this->normalize($store)) !== null && $normalized !== $store) {
            DB::table('settings')->where('key', 'store_phone')->update(['value' => $normalized]);
            Cache::forget('setting:v2:store_phone');
        }
    }

    public function down(): void
    {
        // Normalisation is lossless in meaning; nothing to restore.
    }

    private function normalize(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (strlen($digits) === 12 && str_starts_with($digits, '639')) {
            $digits = '0'.substr($digits, 2);
        }

        return preg_match('/^09\d{9}$/', $digits) ? $digits : null;
    }
};
