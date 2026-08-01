<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAllergen;
use App\Models\ProductCategory;
use App\Models\ProductNutrition;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categories = [
            [
                'name' => 'Banana Bread & Loaves',
                'slug' => 'banana-bread-loaves',
                'description' => 'Moist, oven-baked banana loaves made with fresh ripe bananas and pure butter.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Handcrafted Cookies',
                'slug' => 'handcrafted-cookies',
                'description' => 'Crispy on the edges, chewy in the center. Freshly baked in small batches daily.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Fudgy Brownies',
                'slug' => 'fudgy-brownies',
                'description' => 'Rich Belgian dark chocolate brownies with crackly tops and dense, gooey centers.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Cheesecakes & Cakes',
                'slug' => 'cheesecakes-cakes',
                'description' => 'Creamy cheesecakes and decadent celebration cakes baked for sweet moments.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Cinnamon Rolls & Pastries',
                'slug' => 'cinnamon-rolls-pastries',
                'description' => 'Soft, pillowy cinnamon rolls glazed with rich cream cheese frosting.',
                'sort_order' => 5,
            ],
            [
                'name' => 'Cupcakes & Treats',
                'slug' => 'cupcakes-treats',
                'description' => 'Bite-sized delights, cupcake boxes, and seasonal specialty treats.',
                'sort_order' => 6,
            ],
        ];

        $catMap = [];
        foreach ($categories as $catData) {
            $catMap[$catData['slug']] = ProductCategory::create($catData);
        }

        // 2. Tags
        $tags = ['Best Seller', 'Freshly Baked', 'Customer Favorite', 'New Recipe', 'Seasonal', 'Gluten Free', 'Nut Free', 'Pre-Order'];
        $tagMap = [];
        foreach ($tags as $tagName) {
            $tagMap[$tagName] = Tag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }

        // 3. Products List (15 real products)
        $products = [
            [
                'category_slug' => 'banana-bread-loaves',
                'sku' => 'ABCD-BB-001',
                'name' => 'Classic Banana Bread Loaf',
                'slug' => 'classic-banana-bread-loaf',
                'short_description' => 'Moist, rich banana bread baked with ripe local bananas and pure unsalted butter.',
                'description' => 'Our signature Classic Banana Bread Loaf is crafted using fresh, naturally ripened bananas, pure unsalted creamery butter, and warm brown sugar. Oven-baked daily to create a caramelized golden crust and incredibly tender, moist crumb.',
                'price' => 280.00,
                'sale_price' => null,
                'prep_time_minutes' => 45,
                'stock_qty' => 40,
                'is_featured' => true,
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Best Seller', 'Freshly Baked', 'Customer Favorite'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 slice (80g)', 'calories' => 240, 'fat_g' => 9.5, 'carbs_g' => 36.0, 'protein_g' => 3.5, 'sodium_mg' => 180.0, 'sugar_g' => 18.0],
            ],
            [
                'category_slug' => 'handcrafted-cookies',
                'sku' => 'ABCD-CK-002',
                'name' => 'Classic Chocolate Chip Cookies (6 pcs)',
                'slug' => 'classic-chocolate-chip-cookies-6-pcs',
                'short_description' => 'Golden brown cookies loaded with semi-sweet chocolate chunks and topped with sea salt flakes.',
                'description' => 'Thick, chewy handcrafted cookies stuffed generously with semi-sweet Belgian chocolate chunks. Baked until the edges are crispy and golden, then sprinkled lightly with premium sea salt flakes to balance the sweetness.',
                'price' => 180.00,
                'sale_price' => 165.00,
                'prep_time_minutes' => 30,
                'stock_qty' => 60,
                'is_featured' => true,
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Best Seller', 'Freshly Baked'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 cookie (50g)', 'calories' => 210, 'fat_g' => 10.0, 'carbs_g' => 28.0, 'protein_g' => 2.5, 'sodium_mg' => 140.0, 'sugar_g' => 16.0],
            ],
            [
                'category_slug' => 'fudgy-brownies',
                'sku' => 'ABCD-BR-003',
                'name' => 'Fudgy Dark Chocolate Brownies (Box of 9)',
                'slug' => 'fudgy-dark-chocolate-brownies-box-of-9',
                'short_description' => 'Ultra-fudgy dark chocolate brownies with shiny crackly tops.',
                'description' => 'Baked with 70% dark Belgian chocolate and Dutch cocoa powder. Dense, chewy, and melt-in-your-mouth chocolate bliss in every square. Perfect for chocolate purists.',
                'price' => 320.00,
                'sale_price' => null,
                'prep_time_minutes' => 40,
                'stock_qty' => 35,
                'is_featured' => true,
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Best Seller', 'Customer Favorite'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 brownie (60g)', 'calories' => 260, 'fat_g' => 14.0, 'carbs_g' => 32.0, 'protein_g' => 4.0, 'sodium_mg' => 120.0, 'sugar_g' => 22.0],
            ],
            [
                'category_slug' => 'cinnamon-rolls-pastries',
                'sku' => 'ABCD-CR-004',
                'name' => 'Glazed Cinnamon Rolls (4 pcs)',
                'slug' => 'glazed-cinnamon-rolls-4-pcs',
                'short_description' => 'Soft brioche cinnamon rolls smothered in rich cream cheese glaze.',
                'description' => 'Hand-rolled brioche dough layered with aromatic Korintje cinnamon and brown sugar butter paste. Baked fresh and drizzled while warm with our signature sweet cream cheese frosting.',
                'price' => 260.00,
                'sale_price' => null,
                'prep_time_minutes' => 50,
                'stock_qty' => 25,
                'is_featured' => false,
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Freshly Baked', 'Customer Favorite'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 roll (90g)', 'calories' => 310, 'fat_g' => 12.0, 'carbs_g' => 46.0, 'protein_g' => 5.0, 'sodium_mg' => 210.0, 'sugar_g' => 24.0],
            ],
            [
                'category_slug' => 'cheesecakes-cakes',
                'sku' => 'ABCD-CC-005',
                'name' => 'Classic Baked Cheesecake (6-inch)',
                'slug' => 'classic-baked-cheesecake-6-inch',
                'short_description' => 'Smooth and creamy New York style cheesecake on a buttery Graham crust.',
                'description' => 'A timeless classic! Made with rich Philadelphia cream cheese, real Madagascar vanilla bean paste, and a buttery toasted Graham cracker crust. Slow-baked in a water bath for a velvety, melt-in-your-mouth texture.',
                'price' => 650.00,
                'sale_price' => null,
                'prep_time_minutes' => 60,
                'stock_qty' => 15,
                'is_featured' => true,
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Best Seller', 'Pre-Order'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 slice (110g)', 'calories' => 380, 'fat_g' => 24.0, 'carbs_g' => 32.0, 'protein_g' => 6.5, 'sodium_mg' => 280.0, 'sugar_g' => 22.0],
            ],
            [
                'category_slug' => 'cheesecakes-cakes',
                'sku' => 'ABCD-UC-006',
                'name' => 'Signature Ube Cheesecake (6-inch)',
                'slug' => 'signature-ube-cheesecake-6-inch',
                'short_description' => 'Real authentic Philippine Ube Halaya infused into a creamy baked cheesecake.',
                'description' => 'Our proud Filipino twist! Real homemade Ube Halaya (purple yam jam) folded into rich cream cheese, set on a toasted coconut-Graham base and topped with white chocolate ube drizzle.',
                'price' => 720.00,
                'sale_price' => 680.00,
                'prep_time_minutes' => 60,
                'stock_qty' => 12,
                'is_featured' => true,
                'is_best_seller' => true,
                'is_new_arrival' => true,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Customer Favorite', 'New Recipe'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 slice (115g)', 'calories' => 395, 'fat_g' => 22.0, 'carbs_g' => 39.0, 'protein_g' => 6.0, 'sodium_mg' => 260.0, 'sugar_g' => 26.0],
            ],
            [
                'category_slug' => 'cheesecakes-cakes',
                'sku' => 'ABCD-RV-007',
                'name' => 'Red Velvet Layer Cake (6-inch)',
                'slug' => 'red-velvet-layer-cake-6-inch',
                'short_description' => 'Moist cocoa-scented red velvet sponges layered with silky cream cheese frosting.',
                'description' => 'Three layers of soft red velvet cake with subtle cocoa notes, filled and coated with smooth tangy-sweet cream cheese frosting and dusted with cake crumbs.',
                'price' => 750.00,
                'sale_price' => null,
                'prep_time_minutes' => 90,
                'stock_qty' => 10,
                'is_featured' => false,
                'is_best_seller' => false,
                'is_new_arrival' => false,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Pre-Order'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 slice (120g)', 'calories' => 410, 'fat_g' => 20.0, 'carbs_g' => 48.0, 'protein_g' => 5.0, 'sodium_mg' => 310.0, 'sugar_g' => 32.0],
            ],
            [
                'category_slug' => 'cupcakes-treats',
                'sku' => 'ABCD-CP-008',
                'name' => 'Assorted Gourmet Cupcakes (Box of 6)',
                'slug' => 'assorted-gourmet-cupcakes-box-of-6',
                'short_description' => 'Sampler box featuring 2 Chocolate Fudge, 2 Red Velvet, and 2 Vanilla Bean cupcakes.',
                'description' => 'The ultimate crowd-pleaser box! Contains two decadent Belgian Chocolate Fudge, two signature Red Velvet, and two aromatic Madagascar Vanilla Bean cupcakes topped with fluffy buttercream swirl.',
                'price' => 380.00,
                'sale_price' => 350.00,
                'prep_time_minutes' => 30,
                'stock_qty' => 20,
                'is_featured' => true,
                'is_best_seller' => false,
                'is_new_arrival' => true,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Freshly Baked', 'New Recipe'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 cupcake (75g)', 'calories' => 280, 'fat_g' => 13.0, 'carbs_g' => 38.0, 'protein_g' => 3.0, 'sodium_mg' => 190.0, 'sugar_g' => 28.0],
            ],
            [
                'category_slug' => 'fudgy-brownies',
                'sku' => 'ABCD-SCB-009',
                'name' => 'Salted Caramel Brownies (Box of 9)',
                'slug' => 'salted-caramel-brownies-box-of-9',
                'short_description' => 'Fudgy cocoa brownies swirled with homemade salted butter caramel.',
                'description' => 'Our rich dark chocolate brownie base ribboned liberally with house-made slow-cooked salted butter caramel and dusted with Maldon sea salt.',
                'price' => 360.00,
                'sale_price' => null,
                'prep_time_minutes' => 45,
                'stock_qty' => 30,
                'is_featured' => false,
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Customer Favorite', 'Best Seller'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 brownie (65g)', 'calories' => 285, 'fat_g' => 15.0, 'carbs_g' => 35.0, 'protein_g' => 3.8, 'sodium_mg' => 160.0, 'sugar_g' => 25.0],
            ],
            [
                'category_slug' => 'cheesecakes-cakes',
                'sku' => 'ABCD-STC-010',
                'name' => 'Fresh Strawberry Shortcake (6-inch)',
                'slug' => 'fresh-strawberry-shortcake-6-inch',
                'short_description' => 'Light Japanese vanilla sponge layered with fresh strawberries and whipped cream.',
                'description' => 'Airy, delicate vanilla chiffon cake layered with fresh sliced sweet strawberries and light Chantilly whipped cream. Perfectly balanced and not overly sweet.',
                'price' => 700.00,
                'sale_price' => null,
                'prep_time_minutes' => 60,
                'stock_qty' => 8,
                'is_featured' => true,
                'is_best_seller' => false,
                'is_new_arrival' => true,
                'is_seasonal' => true,
                'is_limited' => false,
                'tags' => ['Seasonal', 'Pre-Order'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 slice (100g)', 'calories' => 290, 'fat_g' => 14.0, 'carbs_g' => 34.0, 'protein_g' => 4.5, 'sodium_mg' => 150.0, 'sugar_g' => 20.0],
            ],
            [
                'category_slug' => 'banana-bread-loaves',
                'sku' => 'ABCD-SD-011',
                'name' => 'Sourdough Country Loaf',
                'slug' => 'sourdough-country-loaf',
                'short_description' => 'Naturally fermented 36-hour wild sourdough loaf with a blistered crispy crust.',
                'description' => 'Crafted with unbleached flour, water, sea salt, and our 4-year-old wild starter culture. Naturally fermented over 36 hours for complex flavor, open crumb, and enhanced digestibility.',
                'price' => 350.00,
                'sale_price' => null,
                'prep_time_minutes' => 120,
                'stock_qty' => 15,
                'is_featured' => false,
                'is_best_seller' => false,
                'is_new_arrival' => true,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['Freshly Baked', 'New Recipe'],
                'allergens' => [['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 slice (60g)', 'calories' => 160, 'fat_g' => 1.0, 'carbs_g' => 31.0, 'protein_g' => 6.0, 'sodium_mg' => 290.0, 'sugar_g' => 0.5],
            ],
            [
                'category_slug' => 'handcrafted-cookies',
                'sku' => 'ABCD-MWC-012',
                'name' => 'Matcha White Choco Cookies (6 pcs)',
                'slug' => 'matcha-white-choco-cookies-6-pcs',
                'short_description' => 'Uji Matcha green tea cookies with creamy Belgian white chocolate chunks.',
                'description-[#5C3A22]' => 'Premium ceremonial-grade Uji Matcha blended into our soft-baked cookie dough, packed with sweet creamy Belgian white chocolate pieces to complement the earthiness of matcha.',
                'price' => 200.00,
                'sale_price' => null,
                'prep_time_minutes' => 30,
                'stock_qty' => 45,
                'is_featured' => false,
                'is_best_seller' => false,
                'is_new_arrival' => true,
                'is_seasonal' => false,
                'is_limited' => false,
                'tags' => ['New Recipe', 'Freshly Baked'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 cookie (50g)', 'calories' => 220, 'fat_g' => 10.5, 'carbs_g' => 29.0, 'protein_g' => 3.0, 'sodium_mg' => 135.0, 'sugar_g' => 17.0],
            ],
            [
                'category_slug' => 'cheesecakes-cakes',
                'sku' => 'ABCD-CCK-013',
                'name' => 'Custom Celebration Cake',
                'slug' => 'custom-celebration-cake',
                'short_description' => 'Bespoke tiered cakes designed to your exact flavor, theme, and color preferences.',
                'description' => 'Work directly with our head pastry chef to create your dream celebration cake for birthdays, weddings, anniversaries, or milestones. Starting price includes 6-inch base cake with custom frosting and topper.',
                'price' => 900.00,
                'sale_price' => null,
                'prep_time_minutes' => 180,
                'stock_qty' => 5,
                'is_featured' => true,
                'is_best_seller' => false,
                'is_new_arrival' => false,
                'is_seasonal' => false,
                'is_limited' => true,
                'tags' => ['Pre-Order'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 slice (120g)', 'calories' => 420, 'fat_g' => 22.0, 'carbs_g' => 50.0, 'protein_g' => 5.5, 'sodium_mg' => 320.0, 'sugar_g' => 35.0],
            ],
            [
                'category_slug' => 'cheesecakes-cakes',
                'sku' => 'ABCD-BPC-014',
                'name' => 'Seasonal Buko Pandan Cake (6-inch)',
                'slug' => 'seasonal-buko-pandan-cake-6-inch',
                'short_description' => 'Aromatic screwpine pandan cake filled with fresh young coconut strips.',
                'description' => 'Fragrant pandan chiffon sponge infused with fresh screwpine extract, layered with young coconut (buko) meat and macapuno strings, enrobed in light pandan whipped frosting.',
                'price' => 680.00,
                'sale_price' => null,
                'prep_time_minutes' => 60,
                'stock_qty' => 10,
                'is_featured' => true,
                'is_best_seller' => false,
                'is_new_arrival' => true,
                'is_seasonal' => true,
                'is_limited' => true,
                'tags' => ['Seasonal', 'New Recipe', 'Pre-Order'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 slice (110g)', 'calories' => 340, 'fat_g' => 16.0, 'carbs_g' => 42.0, 'protein_g' => 4.5, 'sodium_mg' => 210.0, 'sugar_g' => 25.0],
            ],
            [
                'category_slug' => 'handcrafted-cookies',
                'sku' => 'ABCD-CRK-015',
                'name' => 'Fudgy Chocolate Crinkles (Box of 12)',
                'slug' => 'fudgy-chocolate-crinkles-box-of-12',
                'short_description' => 'Soft fudge chocolate cookies dusted heavily with confectioners powdered sugar.',
                'description' => 'Fudge-like dark chocolate crinkle cookies with snowy powdered sugar crackles. Rich, brownie-like texture inside each pillowy bite-sized cookie.',
                'price' => 240.00,
                'sale_price' => null,
                'prep_time_minutes' => 30,
                'stock_qty' => 50,
                'is_featured' => false,
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_seasonal' => false,
                'is_limited' => true,
                'tags' => ['Best Seller', 'Freshly Baked'],
                'allergens' => [['name' => 'Milk', 'type' => 'Contains'], ['name' => 'Eggs', 'type' => 'Contains'], ['name' => 'Wheat / Gluten', 'type' => 'Contains']],
                'nutrition' => ['serving_size' => '1 cookie (30g)', 'calories' => 130, 'fat_g' => 5.0, 'carbs_g' => 20.0, 'protein_g' => 2.0, 'sodium_mg' => 75.0, 'sugar_g' => 13.0],
            ],
        ];

        foreach ($products as $pData) {
            $cat = $catMap[$pData['category_slug']];

            $product = Product::create([
                'category_id' => $cat->id,
                'sku' => $pData['sku'],
                'name' => $pData['name'],
                'slug' => $pData['slug'],
                'short_description' => $pData['short_description'],
                'description' => $pData['description'] ?? $pData['short_description'],
                'price' => $pData['price'],
                'sale_price' => $pData['sale_price'],
                'prep_time_minutes' => $pData['prep_time_minutes'],
                'stock_qty' => $pData['stock_qty'],
                'is_featured' => $pData['is_featured'],
                'is_best_seller' => $pData['is_best_seller'],
                'is_new_arrival' => $pData['is_new_arrival'],
                'is_seasonal' => $pData['is_seasonal'],
                'is_limited' => $pData['is_limited'],
                'is_active' => true,
                'seo_title' => $pData['name'] . ' | ABCDips & Treats',
                'seo_description' => $pData['short_description'],
            ]);

            // Sync tags
            $tagIds = [];
            foreach ($pData['tags'] as $tagName) {
                if (isset($tagMap[$tagName])) {
                    $tagIds[] = $tagMap[$tagName]->id;
                }
            }
            $product->tags()->sync($tagIds);

            // Allergens
            foreach ($pData['allergens'] as $alg) {
                ProductAllergen::create([
                    'product_id' => $product->id,
                    'allergen_name' => $alg['name'],
                    'type' => $alg['type'],
                ]);
            }

            // Nutrition
            ProductNutrition::create(array_merge(
                ['product_id' => $product->id],
                $pData['nutrition']
            ));
        }

        $this->command->info('✅ Seeded 6 categories and 15 ABCDips products with allergens, nutrition, and tags!');
    }
}
