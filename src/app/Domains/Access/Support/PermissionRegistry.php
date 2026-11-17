<?php

namespace App\Domains\Access\Support;

use App\Domains\Access\Contracts\DefinesPermissions;
use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Access\Enums\RolePermission;
use App\Domains\Analytics\Enums\AiAdvisorPermission;
use App\Domains\Analytics\Enums\ReportPermission;
use App\Domains\Catalog\Enums\CategoryPermission;
use App\Domains\Catalog\Enums\ProductPermission;
use App\Domains\Content\Enums\BlogPostPermission;
use App\Domains\Content\Enums\ChatEscalationPermission;
use App\Domains\Content\Enums\ContactMessagePermission;
use App\Domains\Content\Enums\SuggestionPermission;
use App\Domains\Costing\Enums\ProductCostingPermission;
use App\Domains\Costing\Enums\RecipePermission;
use App\Domains\Employee\Enums\AttendancePermission;
use App\Domains\Employee\Enums\EmployeePermission;
use App\Domains\Employee\Enums\PayrollPermission;
use App\Domains\Inventory\Enums\IngredientPermission;
use App\Domains\Inventory\Enums\PackagingMaterialPermission;
use App\Domains\Inventory\Enums\SupplierPermission;
use App\Domains\Pos\Enums\PosPermission;
use App\Domains\Production\Enums\ProductionBatchPermission;
use App\Domains\Purchasing\Enums\PurchaseOrderPermission;
use App\Domains\Purchasing\Enums\QuotationPermission;
use App\Domains\Review\Enums\ReviewPermission;
use App\Domains\Sales\Enums\CouponPermission;
use App\Domains\Sales\Enums\CustomOrderPermission;
use App\Domains\Sales\Enums\DeliveryPoolPermission;
use App\Domains\Sales\Enums\OrderPermission;
use App\Domains\Setting\Enums\SettingPermission;
use App\Domains\User\Enums\CustomerPermission;
use App\Domains\User\Enums\UserPermission;
use BackedEnum;

/**
 * Single list of every permission enum. AccessSeeder seeds from it, and permissions
 * listed here are protected from rename/delete in the roles UI.
 */
final class PermissionRegistry
{
    /** @var list<class-string<DefinesPermissions&BackedEnum>> */
    public const ENUMS = [
        AccessPermission::class,
        CategoryPermission::class,
        SupplierPermission::class,
        IngredientPermission::class,
        PackagingMaterialPermission::class,
        CouponPermission::class,
        EmployeePermission::class,
        AttendancePermission::class,
        ReviewPermission::class,
        BlogPostPermission::class,
        ContactMessagePermission::class,
        SuggestionPermission::class,
        ProductionBatchPermission::class,
        CustomOrderPermission::class,
        ProductPermission::class,
        RecipePermission::class,
        ProductCostingPermission::class,
        QuotationPermission::class,
        PurchaseOrderPermission::class,
        PayrollPermission::class,
        OrderPermission::class,
        DeliveryPoolPermission::class,
        ChatEscalationPermission::class,
        UserPermission::class,
        CustomerPermission::class,
        RolePermission::class,
        SettingPermission::class,
        ReportPermission::class,
        PosPermission::class,
        AiAdvisorPermission::class,
    ];

    /**
     * @return list<DefinesPermissions&BackedEnum>
     */
    public static function all(): array
    {
        $cases = [];
        foreach (self::ENUMS as $enum) {
            foreach ($enum::cases() as $case) {
                $cases[] = $case;
            }
        }

        return $cases;
    }

    /**
     * @return list<string>
     */
    public static function names(): array
    {
        return array_map(fn (BackedEnum $case) => (string) $case->value, self::all());
    }

    public static function isProtected(string $name): bool
    {
        return in_array($name, self::names(), true);
    }

    /**
     * @return array<string, list<array{name: string, description: string}>>
     */
    public static function grouped(): array
    {
        $groups = [];
        foreach (self::all() as $case) {
            $groups[$case->group()][] = ['name' => (string) $case->value, 'description' => $case->description()];
        }

        return $groups;
    }

    /**
     * For the roles form: "Catalog · View products" labels, permission names as values.
     *
     * @return list<array{label: string, value: string, description: string}>
     */
    public static function options(): array
    {
        return array_map(fn ($case) => [
            'label' => $case->group().' · '.$case->description(),
            'value' => (string) $case->value,
            'description' => (string) $case->value,
        ], self::all());
    }
}
