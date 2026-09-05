<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAdvisorService
{
    public function __construct(
        private readonly AnalyticsService $analyticsService
    ) {
    }

    public function ask(string $prompt, ?string $category = 'general'): array
    {
        $geminiKey = env('GEMINI_API_KEY') ?: config('services.gemini.key');
        $openaiKey = env('OPENAI_API_KEY') ?: config('services.openai.key');

        // Gather real contextual data from database
        $kpis = $this->analyticsService->getExecutiveSummary();
        $lowStock = Ingredient::whereColumn('stock_qty', '<=', 'min_stock_qty')->get(['name', 'stock_qty', 'min_stock_qty', 'unit']);
        $activeProducts = Product::forCustomer()->get(['name', 'price', 'sale_price', 'category_id']);

        $contextSummary = "Bakery Context (ABCDips & Treats, Cavite, Philippines):\n"
            . "- Total Revenue: ₱" . number_format($kpis['total_revenue'], 2) . "\n"
            . "- Total Orders: " . $kpis['total_orders'] . "\n"
            . "- Active Menu Products: " . $activeProducts->pluck('name')->implode(', ') . "\n"
            . "- Accepted Payments: GCash, Maya, BDO/BPI Bank Transfer, Cash on Delivery (COD)\n"
            . "- Delivery Options: Lalamove Doorstep Delivery & 100% Free Store Pickup\n";

        // 1. Try Gemini API if GEMINI_API_KEY is available
        if (!empty($geminiKey)) {
            try {
                $geminiModel = env('GEMINI_MODEL', 'gemini-2.0-flash');
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                [
                                    'text' => "You are the knowledgeable Pastry Assistant for ABCDips & Treats bakery in Cavite. Keep answers warm, professional, concise, and helpful. IMPORTANT: Do NOT disclose recipe ingredients, exact proportions, or proprietary ingredient lists under any circumstances. Direct users to product dietary allergen badges for dietary concerns.\n{$contextSummary}\n\nUser Question: {$prompt}"
                                ]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $aiText = $response->json('candidates.0.content.parts.0.text');
                    if ($aiText) {
                        return [
                            'prompt' => $prompt,
                            'response' => trim($aiText),
                            'source' => 'Gemini 2.0 Flash AI',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[AiAdvisorService] Gemini API call failed: ' . $e->getMessage());
            }
        }

        // 2. Try OpenAI API if OPENAI_API_KEY is available
        if (!empty($openaiKey)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$openaiKey}",
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are the knowledgeable Pastry Assistant for ABCDips & Treats. Provide warm, concise, expert bakery advice. IMPORTANT: Do NOT disclose recipe ingredients, exact proportions, or proprietary ingredient lists under any circumstances. Direct users to product dietary allergen badges for dietary concerns. {$contextSummary}"
                        ],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 500,
                ]);

                if ($response->successful()) {
                    $aiText = $response->json('choices.0.message.content');
                    if ($aiText) {
                        return [
                            'prompt' => $prompt,
                            'response' => trim($aiText),
                            'source' => 'OpenAI GPT-4o-mini',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[AiAdvisorService] OpenAI API call failed: ' . $e->getMessage());
            }
        }

        // 3. Smart Local AI Operations Engine (Fallback)
        $aiText = $this->generateLocalAiResponse($prompt, $lowStock, $kpis, $activeProducts);

        return [
            'prompt' => $prompt,
            'response' => $aiText,
            'source' => 'ABCDips Bakery AI Engine',
        ];
    }

    private function generateLocalAiResponse(string $prompt, $lowStock, array $kpis, $activeProducts): string
    {
        $p = strtolower($prompt);

        if (str_contains($p, 'best') || str_contains($p, 'popular') || str_contains($p, 'recommend') || str_contains($p, 'pastr') || str_contains($p, 'treat')) {
            return "**ABCDips Best Seller Recommendations:**\nOur customer-favorite handcrafted treats:\n1. **Classic Banana Bread Loaf** (Moist, loaded with real bananas & walnuts)\n2. **Cheesy Ube Pandesal** (Super soft, stuffed with real ube halaya & cheese)\n3. **Belgian Chocolate Fudge Brownies** (Decadent, rich chocolate fudge)\n4. **Custom Celebration Cakes** (Multi-tier designs for birthdays & weddings)\n\nHead over to our **Shop** page to add these treats to your basket!";
        }

        if (str_contains($p, 'custom') || str_contains($p, 'cake') || str_contains($p, 'wedding') || str_contains($p, 'birthday') || str_contains($p, 'event')) {
            return "**Custom Cake Orders:**\nWe bake custom cakes tailored for birthdays, anniversaries, and weddings!\n• Visit our **Custom Orders** page to select tier count, guest servings, cake flavors, and custom frosting colors.\n• Submit your order inquiry and our pastry team will confirm your quote within 24 hours.";
        }

        if (str_contains($p, 'delivery') || str_contains($p, 'lalamove') || str_contains($p, 'ship') || str_contains($p, 'fee') || str_contains($p, 'hour')) {
            return "**Delivery & Pickup Details:**\n• **Lalamove Doorstep Delivery**: Real-time delivery quotes calculated at checkout based on your exact pin distance across Cavite & Metro Manila.\n• **Store Pickup**: 100% FREE pickup directly at our Cavite bakery store!\n• **Baking Hours**: 8:00 AM – 6:00 PM (Monday to Saturday).";
        }

        if (str_contains($p, 'pay') || str_contains($p, 'gcash') || str_contains($p, 'maya') || str_contains($p, 'bank') || str_contains($p, 'bdo') || str_contains($p, 'bpi') || str_contains($p, 'cod') || str_contains($p, 'cash')) {
            return "**Accepted Payment Methods:**\nWe support flexible payment options:\n• **GCash & Maya E-Wallets** (Instant online redirect)\n• **Bank Transfer** (BDO, BPI, UnionBank, Metrobank)\n• **Cash on Delivery (COD)** for doorstep delivery\n• **Store Pickup** (Pay at counter)";
        }

        if (str_contains($p, 'allergy') || str_contains($p, 'gluten') || str_contains($p, 'nut') || str_contains($p, 'dairy') || str_contains($p, 'egg') || str_contains($p, 'diet')) {
            return "**Dietary & Allergen Notice:**\nAll ABCDips pastries feature specific dietary allergen badges (Gluten, Dairy, Eggs, Nuts, Soy) on their respective product pages. Please check product badges or include special instructions upon checkout if you have dietary restrictions.";
        }

        if (str_contains($p, 'track') || str_contains($p, 'order') || str_contains($p, 'status') || str_contains($p, 'invoice')) {
            return "**Order Tracking & Invoices:**\n• You can track your live order progress under **My Account -> My Orders** or using your tracking token on the **Track Order** page.\n• Official printable PDF invoices can be downloaded directly from your account order details.";
        }

        if (str_contains($p, 'perk') || str_contains($p, 'discount') || str_contains($p, 'account') || str_contains($p, 'reward') || str_contains($p, 'loyalty')) {
            return "**Account Perks & Rewards:**\nCreating a free ABCDips account unlocks:\n• Real-time delivery tracking\n• Birthday month special discounts\n• Loyalty reward points on every purchase\n• Exclusive members-only flash sales!";
        }

        if (str_contains($p, 'suggest') || str_contains($p, 'idea') || str_contains($p, 'feedback') || str_contains($p, 'request')) {
            return "**Share Your Ideas:**\nWe'd love to hear your feedback! Visit our **/suggestions** page to submit product ideas, service feedback, or feature requests directly to our management team.";
        }

        if (str_contains($p, 'contact') || str_contains($p, 'location') || str_contains($p, 'where') || str_contains($p, 'facebook') || str_contains($p, 'instagram') || str_contains($p, 'address')) {
            return "**Contact & Store Info:**\n• **Location**: Cavite, Philippines\n• **Facebook**: facebook.com/abcdipsandtreats\n• **Instagram**: @abcdips_treats\n• **Message Us**: Use our **Contact Us** page to send a direct message and we will get back to you within 24 hours!";
        }

        if (str_contains($p, 'stock') || str_contains($p, 'reorder') || str_contains($p, 'inventory') || str_contains($p, 'revenue') || str_contains($p, 'sales')) {
            if ($lowStock->isNotEmpty()) {
                $itemsList = $lowStock->map(fn($i) => "• **{$i->name}**: Only {$i->stock_qty} {$i->unit} remaining (Reorder threshold: {$i->min_stock_qty} {$i->unit})")->implode("\n");
                return "**Inventory Alert:**\nThe following items are below safe threshold levels:\n{$itemsList}";
            }
            return "**Bakery Performance Summary:**\n• Total Revenue: **₱" . number_format($kpis['total_revenue'], 2) . "** across **{$kpis['completed_orders']} orders**.\n• All stock levels are currently well-maintained above minimum thresholds!";
        }

        return "🧁 **ABCDips AI Helper:**\nHello! I'm Dips 🧁, your AI assistant for ABCDips & Treats.\nI can help you with pastry recommendations, custom cake orders, Lalamove delivery fees, payment options, allergen badges, order tracking, and store pickup options.\n\nWhat can I assist you with today?";
    }
}
