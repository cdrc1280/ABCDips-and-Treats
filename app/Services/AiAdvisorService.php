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

        if (str_contains($promptLower, 'stock') || str_contains($promptLower, 'reorder') || str_contains($promptLower, 'ingredient')) {
            if ($lowStock->isEmpty()) {
                return "🥐 **Inventory Advisory:** All raw ingredients are currently above minimum stock thresholds. No immediate reorders are required today!";
            }

            $itemsList = $lowStock->map(fn ($i) => "• **{$i->name}**: Only {$i->stock_qty} {$i->unit} remaining (Reorder threshold: {$i->min_stock_qty} {$i->unit})")->implode("\n");
            return "⚠️ **Inventory Alert & Reorder Advisory:**\nThe following raw ingredients have dropped below safe threshold levels and should be reordered from suppliers immediately:\n\n{$itemsList}\n\n*Tip: You can create a Purchase Order automatically in the Purchasing section!*";
        }

        if (str_contains($promptLower, 'price') || str_contains($promptLower, 'margin') || str_contains($promptLower, 'cost')) {
            return "💡 **Pricing & Gross Margin Advisor:**\nTo achieve a target **65% Gross Margin** on a pastry with a raw ingredient batch cost of ₱150 per loaf:\n• Target Selling Price = `Batch Cost / (1 - 0.65)` = **₱428.50**\n• Recommended Retail Price: **₱450.00** (provides 66.7% gross margin).\n\n*Check the Recipe Costing section in Filament to audit all gross margins!*";
        }

        if (str_contains($promptLower, 'sales') || str_contains($promptLower, 'revenue') || str_contains($promptLower, 'best')) {
            return "📊 **Sales Performance Summary:**\n• Total Bakery Revenue: **₱" . number_format($kpis['total_revenue'], 2) . "** across **{$kpis['completed_orders']} completed orders**.\n• Current Top Products in Stock: " . $topProducts->pluck('name')->implode(', ') . ".\n\n*Recommendation: Increase morning production batch quantities for high-margin items like Ube Cheesecake and Chocolate Chip Cookies!*";
        }

        return "🥖 **ABCDips AI Operations Assistant:**\nHello! I am your AI Bakery Operations Advisor. I can analyze raw ingredient inventory levels, calculate target pricing for 65%+ gross margins, suggest optimal baking batch sizes, and generate sales performance insights.\n\nTry asking me:\n- *Which ingredients need reordering?*\n- *What price should I charge for 65% gross margin?*\n- *Summarize current sales performance.*";
    }
}
