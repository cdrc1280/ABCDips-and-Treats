<?php

namespace Tests\Feature\Sales\Order;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Sales\Enums\OrderPermission;
use App\Domains\Sales\Notifications\NewOrderPlaced;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

class NewOrderNotificationTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    private function placeOrder(): void
    {
        $customer = User::factory()->customer()->create(['email_verified_at' => now()]);
        $this->actingAs($customer)->post('/cart/items', ['product_id' => $this->product(['price' => 250, 'stock_qty' => 5])->id, 'qty' => 2]);
        $this->post('/checkout', [
            'customer_name' => 'Ana Cruz', 'customer_email' => 'ana@example.com', 'customer_phone' => '09171234567',
            'fulfillment_type' => 'pickup', 'delivery_mode' => 'pooling', 'payment_method' => 'bank_transfer',
        ])->assertRedirect();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Http::preventStrayRequests();
    }

    public function test_staff_who_can_view_orders_are_notified_and_others_are_not(): void
    {
        $staff = $this->userWith(AccessPermission::DashboardView, OrderPermission::OrdersView);
        $bystander = $this->userWith(AccessPermission::DashboardView);

        $this->placeOrder();

        $this->assertSame(1, $staff->notifications()->count());
        $this->assertSame(0, $bystander->notifications()->count());
        $data = $staff->notifications()->first()->data;
        $this->assertSame(NewOrderPlaced::class, $staff->notifications()->first()->type);
        $this->assertStringStartsWith('/admin/orders/', $data['url']);
        $this->assertStringContainsString('Ana Cruz', $data['body']);
    }

    public function test_bell_lists_counts_and_marks_notifications_read(): void
    {
        $staff = $this->userWith(AccessPermission::DashboardView, OrderPermission::OrdersView);
        $this->placeOrder();
        // A legacy Filament row: its /filament link must not be offered any more.
        $staff->notifications()->create(['id' => (string) Str::uuid(), 'type' => 'Filament\\Notifications\\DatabaseNotification',
            'data' => ['title' => 'Old', 'body' => '**Bold** text', 'actions' => [['url' => 'http://localhost/filament/orders/1/edit']], 'format' => 'filament']]);

        $this->actingAs($staff)->get('/admin')->assertInertia(fn (Assert $page) => $page->where('notifications.unread', 2));

        $list = $this->getJson('/admin/notifications')->assertOk()->assertJsonPath('unread', 2)->json('items');
        $legacy = collect($list)->firstWhere('title', 'Old');
        $this->assertNull($legacy['url']);
        $this->assertSame('Bold text', $legacy['body']);

        $this->postJson("/admin/notifications/{$legacy['id']}/read")->assertOk()->assertJsonPath('unread', 1);
        $this->postJson('/admin/notifications/read-all')->assertOk()->assertJsonPath('unread', 0);
        $this->assertSame(0, $staff->unreadNotifications()->count());
    }

    public function test_users_cannot_touch_someone_elses_notifications(): void
    {
        $staff = $this->userWith(AccessPermission::DashboardView, OrderPermission::OrdersView);
        $this->placeOrder();
        $id = $staff->notifications()->first()->id;

        $this->actingAs($this->userWith(AccessPermission::DashboardView))->postJson("/admin/notifications/{$id}/read")->assertNotFound();
        $this->assertSame(1, $staff->unreadNotifications()->count());
    }
}
