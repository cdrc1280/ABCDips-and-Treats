<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PostgreSQL doesn't index foreign key columns automatically (MySQL did), so joins, parent
 * filters and ON DELETE cascades would scan whole tables as data grows. Index every one.
 */
return new class extends Migration
{
    /** @var array<string, list<string>> */
    private array $foreignKeys = [
        'blog_posts' => ['author_id'],
        'cart_items' => ['cart_id', 'product_id'],
        'carts' => ['user_id'],
        'chat_escalations' => ['user_id'],
        'contact_messages' => ['replied_by'],
        'costing_items' => ['ingredient_id', 'packaging_material_id', 'product_costing_id'],
        'custom_orders' => ['user_id'],
        'employees' => ['user_id'],
        'gift_cards' => ['purchased_by', 'redeemed_by'],
        'ingredients' => ['supplier_id'],
        'menus' => ['permission_id'],
        'order_status_histories' => ['changed_by_user_id', 'order_id'],
        'orders' => ['delivery_pool_id'],
        'packaging_movements' => ['packaging_material_id', 'user_id'],
        'payroll_items' => ['employee_id', 'payroll_id'],
        'product_allergens' => ['product_id'],
        'product_costings' => ['product_id'],
        'product_nutritions' => ['product_id'],
        'product_tag' => ['tag_id'],
        'production_batches' => ['baker_user_id', 'product_id', 'recipe_id'],
        'products' => ['category_id'],
        'purchase_order_items' => ['ingredient_id', 'purchase_order_id'],
        'purchase_orders' => ['created_by_user_id', 'quotation_id', 'supplier_id'],
        'quotation_items' => ['ingredient_id', 'quotation_id'],
        'quotations' => ['created_by_user_id', 'supplier_id'],
        'recipe_ingredients' => ['ingredient_id', 'recipe_id'],
        'recipe_packagings' => ['packaging_material_id', 'recipe_id'],
        'review_votes' => ['user_id'],
        'reviews' => ['product_id', 'user_id'],
        'role_has_permissions' => ['role_id'],
        'stock_movements' => ['created_by_user_id', 'ingredient_id'],
        'suggestions' => ['user_id'],
        'wishlists' => ['product_id'],
    ];

    public function up(): void
    {
        foreach ($this->foreignKeys as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column) {
                    $blueprint->index($column);
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->foreignKeys as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column) {
                    $blueprint->dropIndex([$column]);
                }
            });
        }
    }
};
