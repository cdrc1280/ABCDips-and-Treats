<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\Recipe;

use Illuminate\Support\Facades\Http;

class AiAdvisorService
{
    public function __construct(
        private readonly AnalyticsService $analyticsService
    ) {}

    public function ask(string $prompt, ?string $category = 'general'): array
    {
        $apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY') ?? env('GEMINI_API_KEY');

        // Gather real contextual data from database
        $kpis = $this->analyticsService->getExecutiveSummary();
        $lowStock = Ingredient::whereColumn('stock_qty', '<=', 'min_stock_qty')->get(['name', 'stock_qty', 'min_stock_qty', 'unit']);
        $topProducts = Product::where('is_active', true)->orderByDesc('stock_qty')->limit(5)->get(['name', 'price', 'stock_qty']);

        $contextSummary = "Bakery Context:\n"
            . "- Total Revenue: ₱" . number_format($kpis['total_revenue'], 2) . "\n"
            . "- Total Orders: " . $kpis['total_orders'] . "\n"
            . "- Low Stock Ingredients Count: " . $lowStock->count() . "\n";

        if ($lowStock->isNotEmpty()) {
            $contextSummary .= "- Low Stock Items: " . $lowStock->map(fn ($i) => "{$i->name} ({$i->stock_qty} {$i->unit} left, min {$i->min_stock_qty})")->implode(', ') . "\n";
        }

        // Call OpenAI API if API key is provided
        if (!empty($apiKey)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type'  => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model'    => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role'    => 'system',
                            'content' => "You are Antigravity AI, the chief AI Operations Advisor for ABCDips & Treats bakery. Provide concise, expert, practical baking business advice in warm bakery tone. {$contextSummary}"
                        ],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 500,
                ]);

                if ($response->successful()) {
                    $aiText = $response->json('choices.0.message.content');
                    return [
                        'prompt'   => $prompt,
                        'response' => $aiText,
                        'source'   => 'OpenAI GPT-4o-mini',
                    ];
                }
            } catch (\Throwable $e) {
                // Fallback to local intelligence model
            }
        }

        // Smart Local AI Operations Engine (Fallback)
        $aiText = $this->generateLocalAiResponse($prompt, $lowStock, $kpis, $topProducts);

        return [
            'prompt'   => $prompt,
            'response' => $aiText,
            'source'   => 'ABCDips Bakery AI Engine',
        ];
    }

    private function generateLocalAiResponse(string $prompt, $lowStock, array $kpis, $topProducts): string
    {
        $promptLower = strtolower($prompt);

        if (str_contains($promptLower, 'best') || str_contains($promptLower, 'popular') || str_contains($promptLower, 'recommend')) {
            return "🧁 **ABCDips Best Sellers:**\nOur customer favorites are:\n1. **Cheesy Ube Pandesal** (Soft, filled with real ube halaya & cheese)\n2. **Fudge Chocolate Brownies** (Rich Belgian chocolate fudge)\n3. **Artisan Banana Bread** (Moist loaf topped with walnuts & chocolate chips)\n4. **Custom Celebration Cakes** (Bespoke multi-tier designs)\n\nCheck out our Shop page to place an order today!";
        }

        if (str_contains($promptLower, 'custom') || str_contains($promptLower, 'cake') || str_contains($promptLower, 'wedding') || str_contains($promptLower, 'birthday')) {
            return "🎂 **Custom Cake Orders:**\nWe create custom multi-tier cakes for birthdays, weddings, and special events!\n• You can use our **Custom Cake Builder** on the storefront to choose your tiers, guest count, flavors, and frosting style.\n• Our head chef will review your inquiry and send you a formal quote within 24 hours.";
        }

        if (str_contains($promptLower, 'delivery') || str_contains($promptLower, 'lalamove') || str_contains($promptLower, 'ship') || str_contains($promptLower, 'rate')) {
            return "🛵 **Doorstep Delivery Info:**\n• We offer dynamic **Lalamove Doorstep Delivery** across Cavite and nearby areas!\n• Delivery rates are calculated live at Checkout based on your exact pin distance from our store.\n• You can also select **Store Pickup** for 100% FREE pickup!";
        }

        if (str_contains($promptLower, 'pay') || str_contains($promptLower, 'gcash') || str_contains($promptLower, 'maya') || str_contains($promptLower, 'bank') || str_contains($promptLower, 'bdo') || str_contains($promptLower, 'cod')) {
            return "💳 **Accepted Payment Methods:**\nWe accept:\n• **GCash** & **Maya** (PayMaya) E-Wallets\n• **Bank Transfer** (BDO, BPI, Metrobank, UnionBank, Landbank, etc.)\n• **Cash on Delivery (COD)** for doorstep delivery\n• **Store Pickup** (Pay at counter or online)";
        }

        if (str_contains($promptLower, 'allergy') || str_contains($promptLower, 'gluten') || str_contains($promptLower, 'nut') || str_contains($promptLower, 'dairy')) {
            return "🥜 **Allergen & Ingredient Notice:**\nAll our pastries are baked fresh using 100% real butter, fresh eggs, and high-grade flour. Each product listing displays specific allergen tags (Gluten, Dairy, Eggs, Nuts, Soy). Please review product tags before ordering if you have severe allergies.";
        }

        if (str_contains($promptLower, 'stock') || str_contains($promptLower, 'reorder') || str_contains($promptLower, 'inventory')) {
            if ($lowStock->isEmpty()) {
                return "🥐 **Inventory Advisory:** All raw ingredients are currently above minimum stock thresholds. No immediate reorders are required today!";
            }

            $itemsList = $lowStock->map(fn ($i) => "• **{$i->name}**: Only {$i->stock_qty} {$i->unit} remaining (Reorder threshold: {$i->min_stock_qty} {$i->unit})")->implode("\n");
            return "⚠️ **Inventory Alert & Reorder Advisory:**\nThe following raw ingredients have dropped below safe threshold levels and should be reordered from suppliers immediately:\n\n{$itemsList}";
        }

        if (str_contains($promptLower, 'price') || str_contains($promptLower, 'margin') || str_contains($promptLower, 'cost')) {
            return "💡 **Pricing & Gross Margin Advisor:**\nTo achieve a target **65% Gross Margin** on a pastry batch with a raw ingredient cost of ₱150:\n• Target RRP = `Cost / (1 - 0.65)` = **₱428.50**\n• Recommended Price: **₱450.00** (provides 66.7% gross margin).";
        }

        if (str_contains($promptLower, 'sales') || str_contains($promptLower, 'revenue')) {
            return "📊 **Sales Performance Summary:**\n• Total Bakery Revenue: **₱" . number_format($kpis['total_revenue'], 2) . "** across **{$kpis['completed_orders']} completed orders**.\n• Current Top Products in Stock: " . $topProducts->pluck('name')->implode(', ') . ".";
        }

        return "🥖 **Dips AI Assistant:**\nHello! I'm Dips 🧁, your AI assistant for ABCDips & Treats.\nI can answer questions about our fresh pastries, custom cake inquiries, Lalamove doorstep delivery rates, payment methods, allergen details, and store pickup options.\n\nHow can I help you today?";
    }
}
