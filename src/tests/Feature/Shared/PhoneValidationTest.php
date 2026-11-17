<?php

namespace Tests\Feature\Shared;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Access\Models\Role;
use App\Domains\Employee\Enums\EmployeePermission;
use App\Domains\Inventory\Enums\SupplierPermission;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Setting\Enums\SettingPermission;
use App\Domains\User\Enums\CustomerPermission;
use App\Domains\User\Enums\UserPermission;
use App\Domains\User\Models\User;
use App\Rules\PhilippinePhone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Every phone field, back office and storefront: exactly 11 digits, numbers only, starting with 09. */
class PhoneValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{mixed}> */
    public static function invalidPhones(): array
    {
        return [
            '10 digits' => ['0917123456'],
            '12 digits' => ['091712345678'],
            'plus 63 prefix' => ['+639171234567'],
            '63 prefix' => ['639171234567'],
            'spaces' => ['0917 123 4567'],
            'dashes' => ['0917-123-4567'],
            'letters' => ['0917abc4567'],
            'landline' => ['02-8888-1234'],
            'not starting with 09' => ['19171234567'],
            'integer' => [9171234567],
        ];
    }

    #[DataProvider('invalidPhones')]
    public function test_rule_rejects(mixed $phone): void
    {
        $this->assertTrue(Validator::make(['phone' => $phone], ['phone' => [new PhilippinePhone]])->fails());
    }

    public function test_rule_accepts_an_11_digit_mobile_number(): void
    {
        $this->assertTrue(Validator::make(['phone' => '09171234567'], ['phone' => [new PhilippinePhone]])->passes());
    }

    public function test_back_office_forms_reject_a_non_numeric_or_short_phone(): void
    {
        $this->actingAs($this->userWith(
            AccessPermission::DashboardView,
            ...SupplierPermission::cases(),
            ...EmployeePermission::cases(),
            ...CustomerPermission::cases(),
            ...UserPermission::cases(),
        ));

        foreach (['/admin/suppliers', '/admin/employees', '/admin/users'] as $url) {
            $this->post($url, ['phone' => '0917-123-456'])->assertSessionHasErrors('phone');
            $this->post($url, ['phone' => '09171234567'])->assertSessionDoesntHaveErrors('phone');
        }

        Role::findOrCreate('customer', 'web');
        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $this->put("/admin/customers/{$customer->id}", ['name' => 'Ana', 'email' => $customer->email, 'phone' => '0917abc4567'])->assertSessionHasErrors('phone');
        $this->put("/admin/customers/{$customer->id}", ['name' => 'Ana', 'email' => $customer->email, 'phone' => '09171234567'])->assertSessionDoesntHaveErrors('phone');
    }

    public function test_supplier_update_rejects_a_landline(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...SupplierPermission::cases()));
        $supplier = Supplier::query()->create(['name' => 'Mill', 'contact_person' => 'Ana', 'email' => 'mill@example.test', 'phone' => '09171234567', 'address' => 'Cavite']);

        $this->put("/admin/suppliers/{$supplier->id}", ['name' => 'Mill', 'contact_person' => 'Ana', 'email' => 'mill@example.test', 'phone' => '02-8888-1234', 'address' => 'Cavite', 'payment_terms' => 'COD'])
            ->assertSessionHasErrors('phone');

        $this->assertSame('09171234567', $supplier->fresh()?->phone);
    }

    public function test_storefront_contact_and_profile_reject_a_bad_phone(): void
    {
        $this->post('/contact', ['name' => 'Ana', 'email' => 'ana@example.com', 'phone' => '+639171234567', 'subject' => 'Hello', 'message' => 'I would like to order 50 boxes.'])
            ->assertSessionHasErrors('phone');

        $customer = User::factory()->create();
        $this->actingAs($customer)
            ->put('/account/profile', ['name' => 'Ana', 'email' => $customer->email, 'phone' => '0917123456'])
            ->assertSessionHasErrors('phone');
    }

    public function test_store_phone_setting_must_be_11_digits(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...SettingPermission::cases()));

        $this->put('/admin/settings', ['values' => ['store_phone' => '(046) 123-4567']])->assertSessionHasErrors('values.store_phone');
        $this->put('/admin/settings', ['values' => ['store_phone' => '09171234567']])->assertSessionDoesntHaveErrors('values.store_phone');
    }

    public function test_migration_normalizes_convertible_numbers_and_leaves_the_rest(): void
    {
        $plus = Supplier::query()->create(['name' => 'A', 'contact_person' => 'A', 'email' => 'a@x.test', 'phone' => '+63 917 123 4567', 'address' => 'Cavite']);
        $dashed = Supplier::query()->create(['name' => 'B', 'contact_person' => 'B', 'email' => 'b@x.test', 'phone' => '0917-765-4321', 'address' => 'Cavite']);
        $landline = Supplier::query()->create(['name' => 'C', 'contact_person' => 'C', 'email' => 'c@x.test', 'phone' => '02-8888-1234', 'address' => 'Cavite']);

        (require database_path('migrations/2026_11_17_000002_normalize_phone_numbers.php'))->up();

        $this->assertSame('09171234567', DB::table('suppliers')->where('id', $plus->id)->value('phone'));
        $this->assertSame('09177654321', DB::table('suppliers')->where('id', $dashed->id)->value('phone'));
        $this->assertSame('02-8888-1234', DB::table('suppliers')->where('id', $landline->id)->value('phone'));
    }
}
