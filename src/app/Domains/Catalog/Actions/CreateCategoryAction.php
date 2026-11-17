<?php

namespace App\Domains\Catalog\Actions;

use App\Domains\Catalog\Data\CategoryData;
use App\Domains\Catalog\Models\ProductCategory;
use App\Shared\Services\PublicUploads;
use Illuminate\Support\Facades\DB;

final class CreateCategoryAction
{
    public function __construct(private readonly PublicUploads $uploads) {}

    public function execute(CategoryData $data): ProductCategory
    {
        return DB::transaction(fn () => ProductCategory::query()->create([
            'name' => $data->name,
            'slug' => $data->slug,
            'description' => $data->description,
            'sort_order' => $data->sortOrder,
            'is_active' => $data->isActive,
            'image_path' => $data->image ? $this->uploads->store($data->image, 'categories') : null,
        ]));
    }
}
