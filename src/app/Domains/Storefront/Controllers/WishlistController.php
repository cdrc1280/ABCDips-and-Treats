<?php

namespace App\Domains\Storefront\Controllers;

use App\Domains\Catalog\Models\Product;
use App\Domains\Sales\Models\Wishlist;
use App\Domains\Storefront\Resources\ProductPresenter;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class WishlistController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::query()
            ->forCustomer()
            ->withListingAggregates()
            ->with(['category', 'media'])
            ->whereIn('id', Wishlist::query()->where('user_id', $request->user()->id)->select('product_id'))
            ->get()
            ->map(fn (Product $p) => ProductPresenter::card($p))
            ->all();

        return Inertia::render('account/wishlist', ['products' => $products]);
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $existing = Wishlist::query()->where('user_id', $request->user()->id)->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            Inertia::flash('toast', ['type' => 'info', 'message' => __(':name removed from your wishlist.', ['name' => $product->name])]);
        } else {
            Wishlist::create(['user_id' => $request->user()->id, 'product_id' => $product->id]);
            Inertia::flash('toast', ['type' => 'success', 'message' => __(':name saved to your wishlist.', ['name' => $product->name])]);
        }

        return back();
    }
}
