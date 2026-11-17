<?php

namespace App\Domains\Catalog\Actions;

use App\Domains\Catalog\Data\CategoryData;
use App\Domains\Catalog\Models\ProductCategory;
use App\Shared\Services\PublicUploads;
use Illuminate\Support\Facades\DB;

final class UpdateCategoryAction
{
    public function __construct(private readonly PublicUploads $uploads) {}

    public function execute(ProductCategory $category, CategoryData $data): ProductCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update([
                'name' => $data->name,
                'slug' => $data->slug,
                'description' => $data->description,
                'sort_order' => $data->sortOrder ?? $category->sort_order,
                'is_active' => $data->isActive,
                'image_path' => $this->uploads->replace($category->image_path, $data->image, 'categories', $data->removeImage),
            ]);

            return $category;
        });
    }
}
