<?php

namespace App\Domains\Catalog\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class CategoryData extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description = null,
        public ?int $sortOrder = null,
        public bool $isActive = true,
        public ?UploadedFile $image = null,
        public bool $removeImage = false,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? ''));
        $slug = trim((string) ($properties['slug'] ?? ''));
        $properties['slug'] = Str::slug($slug !== '' ? $slug : $properties['name']);
        $properties['description'] = trim((string) ($properties['description'] ?? '')) ?: null;
        $properties['sort_order'] = ($properties['sort_order'] ?? '') === '' ? null : (int) $properties['sort_order'];
        $properties['is_active'] = filter_var($properties['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $properties['remove_image'] = filter_var($properties['remove_image'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('category');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('product_categories', 'slug')->ignore($id)->whereNull('deleted_at')],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['boolean'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [
            'slug.unique' => __('Another category already uses this URL slug.'),
            'slug.alpha_dash' => __('The slug may only contain letters, numbers, dashes and underscores.'),
            'image.max' => __('The image must be 4 MB or smaller.'),
        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return ['sort_order' => __('sort order'), 'is_active' => __('status')];
    }
}
