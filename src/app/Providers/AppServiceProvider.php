<?php

namespace App\Providers;

use App\Domains\Access\Models\Permission;
use App\Domains\Access\Models\Role;
use App\Domains\Audit\Models\AuditLog;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductAllergen;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Catalog\Models\ProductNutrition;
use App\Domains\Catalog\Models\Tag;
use App\Domains\Content\Models\BlogPost;
use App\Domains\Content\Models\ChatEscalation;
use App\Domains\Content\Models\ContactMessage;
use App\Domains\Content\Models\Faq;
use App\Domains\Content\Models\Suggestion;
use App\Domains\Costing\Models\CostingItem;
use App\Domains\Costing\Models\ProductCosting;
use App\Domains\Costing\Models\Recipe;
use App\Domains\Costing\Models\RecipeIngredient;
use App\Domains\Costing\Models\RecipePackaging;
use App\Domains\Employee\Models\Attendance;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Models\Payroll;
use App\Domains\Employee\Models\PayrollItem;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\PackagingMaterial;
use App\Domains\Inventory\Models\PackagingMovement;
use App\Domains\Inventory\Models\StockMovement;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Menu\Models\Menu;
use App\Domains\Preference\Models\Preference;
use App\Domains\Production\Models\ProductionBatch;
use App\Domains\Purchasing\Models\PurchaseOrder;
use App\Domains\Purchasing\Models\PurchaseOrderItem;
use App\Domains\Purchasing\Models\Quotation;
use App\Domains\Purchasing\Models\QuotationItem;
use App\Domains\Review\Models\Review;
use App\Domains\Review\Models\ReviewVote;
use App\Domains\Sales\Models\Cart;
use App\Domains\Sales\Models\CartItem;
use App\Domains\Sales\Models\Coupon;
use App\Domains\Sales\Models\CustomOrder;
use App\Domains\Sales\Models\DeliveryPool;
use App\Domains\Sales\Models\GiftCard;
use App\Domains\Sales\Models\Order;
use App\Domains\Sales\Models\OrderItem;
use App\Domains\Sales\Models\OrderStatusHistory;
use App\Domains\Sales\Models\Wishlist;
use App\Domains\Setting\Models\Setting;
use App\Domains\Storefront\Listeners\MergeGuestCart;
use App\Domains\User\Models\User;
use App\Http\Middleware\SecurityHeaders;
use App\Shared\Filesystem\PublicBucketAdapter;
use AsyncAws\S3\S3Client;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, MergeGuestCart::class);

        // `r2` disk (Cloudflare R2) through the light AsyncAws client: the full AWS SDK is ~70 MB,
        // which pushed the Vercel PHP function past its size limit and crashed it on start.
        Storage::extend('async-s3', function ($app, array $config) {
            $client = new S3Client([
                'endpoint' => (string) ($config['endpoint'] ?? ''),
                'accessKeyId' => (string) ($config['key'] ?? ''),
                'accessKeySecret' => (string) ($config['secret'] ?? ''),
                'region' => (string) ($config['region'] ?? 'auto'),
                'pathStyleEndpoint' => 'true',
            ]);
            $adapter = new AsyncAwsS3Adapter($client, (string) $config['bucket'], $config['prefix'] ?? '');

            return new PublicBucketAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });

        // Force https only when the app is actually served over https — a local
        // `docker compose` run on http://localhost:8000 must keep plain http links.
        if ($this->app->environment('production') && str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        $this->registerMorphMap();

        // Super admins pass every permission check (mirrored on the frontend in usePermissions()).
        Gate::before(fn ($user) => $user->hasRole('super_admin') ? true : null);

        try {
            $router = $this->app->make(Router::class);
            $router->pushMiddlewareToGroup('web', SecurityHeaders::class);
        } catch (\Throwable $e) {
            // Router isn't available during certain artisan commands.
        }
    }

    /**
     * Short, stable aliases stored in polymorphic columns (model_has_roles, media, notifications,
     * activity_log, audit_logs) instead of PHP class names, so moving a model never orphans rows.
     */
    private function registerMorphMap(): void
    {
        Relation::enforceMorphMap([
            'permission' => Permission::class,
            'role' => Role::class,
            'audit_log' => AuditLog::class,
            'product' => Product::class,
            'product_allergen' => ProductAllergen::class,
            'product_category' => ProductCategory::class,
            'product_nutrition' => ProductNutrition::class,
            'tag' => Tag::class,
            'blog_post' => BlogPost::class,
            'chat_escalation' => ChatEscalation::class,
            'contact_message' => ContactMessage::class,
            'faq' => Faq::class,
            'suggestion' => Suggestion::class,
            'costing_item' => CostingItem::class,
            'product_costing' => ProductCosting::class,
            'recipe' => Recipe::class,
            'recipe_ingredient' => RecipeIngredient::class,
            'recipe_packaging' => RecipePackaging::class,
            'attendance' => Attendance::class,
            'employee' => Employee::class,
            'payroll' => Payroll::class,
            'payroll_item' => PayrollItem::class,
            'ingredient' => Ingredient::class,
            'packaging_material' => PackagingMaterial::class,
            'packaging_movement' => PackagingMovement::class,
            'stock_movement' => StockMovement::class,
            'supplier' => Supplier::class,
            'menu' => Menu::class,
            'preference' => Preference::class,
            'production_batch' => ProductionBatch::class,
            'purchase_order' => PurchaseOrder::class,
            'purchase_order_item' => PurchaseOrderItem::class,
            'quotation' => Quotation::class,
            'quotation_item' => QuotationItem::class,
            'review' => Review::class,
            'review_vote' => ReviewVote::class,
            'cart' => Cart::class,
            'cart_item' => CartItem::class,
            'coupon' => Coupon::class,
            'custom_order' => CustomOrder::class,
            'delivery_pool' => DeliveryPool::class,
            'gift_card' => GiftCard::class,
            'order' => Order::class,
            'order_item' => OrderItem::class,
            'order_status_history' => OrderStatusHistory::class,
            'wishlist' => Wishlist::class,
            'setting' => Setting::class,
            'user' => User::class,
        ]);
    }
}
