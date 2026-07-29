<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Order;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Filament\Resources\OrderResource;

$latestOrder = Order::latest()->first();
$admin = User::find(1);

echo "Testing notification with Filament\\Actions\\Action..." . PHP_EOL;

try {
    $orderUrl = OrderResource::getUrl('edit', ['record' => $latestOrder]);
    Notification::make()
        ->title("🛍️ New Order #{$latestOrder->order_number}")
        ->body("👤 Customer: **{$latestOrder->customer_name}**\n💰 Total: **₱" . number_format($latestOrder->total, 2) . "**\n📦 Items: {$latestOrder->items->count()} item(s)")
        ->icon('heroicon-o-shopping-bag')
        ->iconColor('success')
        ->actions([
            Action::make('view_order')
                ->label('👁️ View & Manage Order')
                ->button()
                ->color('primary')
                ->url($orderUrl),
        ])
        ->sendToDatabase($admin);

    echo "SUCCESS! Notification sent to Admin User #1 ({$admin->email})" . PHP_EOL;
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}

echo "Total Notifications in Database: " . \Illuminate\Support\Facades\DB::table('notifications')->count() . PHP_EOL;
