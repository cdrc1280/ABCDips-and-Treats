# Review

<!-- auto:start -->

_Product reviews, moderation and helpful votes._

### Files

**src/app/Domains/Review/Actions/**
- [`CreateReviewAction.php`](../../src/app/Domains/Review/Actions/CreateReviewAction.php)
- [`DeleteReviewAction.php`](../../src/app/Domains/Review/Actions/DeleteReviewAction.php)
- [`FeatureReviewAction.php`](../../src/app/Domains/Review/Actions/FeatureReviewAction.php) — Toggle featured
- [`UpdateReviewAction.php`](../../src/app/Domains/Review/Actions/UpdateReviewAction.php)

**src/app/Domains/Review/Controllers/**
- [`ReviewController.php`](../../src/app/Domains/Review/Controllers/ReviewController.php)

**src/app/Domains/Review/Data/**
- [`ReviewData.php`](../../src/app/Domains/Review/Data/ReviewData.php)
- [`ReviewFilterData.php`](../../src/app/Domains/Review/Data/ReviewFilterData.php)

**src/app/Domains/Review/Enums/**
- [`ReviewPermission.php`](../../src/app/Domains/Review/Enums/ReviewPermission.php)

**src/app/Domains/Review/Models/**
- [`Review.php`](../../src/app/Domains/Review/Models/Review.php)
- [`ReviewVote.php`](../../src/app/Domains/Review/Models/ReviewVote.php)

**src/app/Domains/Review/Queries/**
- [`ReviewQueries.php`](../../src/app/Domains/Review/Queries/ReviewQueries.php)
- [`ReviewSummaryQuery.php`](../../src/app/Domains/Review/Queries/ReviewSummaryQuery.php)

**src/app/Domains/Review/Resources/**
- [`ReviewResource.php`](../../src/app/Domains/Review/Resources/ReviewResource.php)

**src/app/Services/**
- [`ReviewService.php`](../../src/app/Services/ReviewService.php)

**src/app/Http/Resources/**
- [`ReviewResource.php`](../../src/app/Http/Resources/ReviewResource.php)

### Data model

**`Review`** → table `reviews` · traits: HasUuids, InteractsWithMedia, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `product_id`?, `user_id`?, `reviewer_name`, `reviewer_email`, `rating`, `title`?, `comment`, `is_verified_buyer`, `is_approved`, `is_featured`, `helpful_votes`, `created_at`?, `updated_at`?, `is_anonymous`, `deleted_at`?
- Relations: `product()` BelongsTo `Product`, `user()` BelongsTo `User`, `votes()` HasMany `ReviewVote`

**`ReviewVote`** → table `review_votes` · traits: HasUuids, InteractsWithModelFactory
- Columns: `id`, `review_id`, `user_id`?, `ip_address`?, `created_at`?, `updated_at`?
- Relations: `review()` BelongsTo `Review`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/reviews` | `admin.reviews.index` | `ReviewController@index` | `review/index` | reviews-view |
| POST | `/admin/reviews` | `admin.reviews.store` | `ReviewController@store` | — | reviews-create |
| GET | `/admin/reviews/create` | `admin.reviews.create` | `ReviewController@create` | `review/create` | reviews-create |
| PUT | `/admin/reviews/{review}` | `admin.reviews.update` | `ReviewController@update` | — | reviews-edit |
| DELETE | `/admin/reviews/{review}` | `admin.reviews.destroy` | `ReviewController@destroy` | — | reviews-delete |
| GET | `/admin/reviews/{review}/edit` | `admin.reviews.edit` | `ReviewController@edit` | `review/edit` | reviews-edit |
| PATCH | `/admin/reviews/{review}/feature` | `admin.reviews.feature` | `ReviewController@feature` | — | reviews-edit |
| PATCH | `/admin/reviews/{review}/toggle` | `admin.reviews.toggle` | `ReviewController@toggle` | — | reviews-edit |

### Permissions

`reviews-view`, `reviews-create`, `reviews-edit`, `reviews-delete`

### Pages (React)

[`src/review/create.tsx`](../../src/resources/js/pages/review/create.tsx), [`src/review/edit.tsx`](../../src/resources/js/pages/review/edit.tsx), [`src/review/index.tsx`](../../src/resources/js/pages/review/index.tsx)

### Tests

[`ReviewTest`](../../src/tests/Feature/Review/Review/ReviewTest.php)

<!-- auto:end -->

## Business flows

See [flows §8](flows.md#8-reviews-a4).

## Rules & invariants

- One helpful vote per user/IP per review.
- Verified buyer = completed order containing the product.

## Improvement audit

- A4: reviews are auto-approved.

Full list: [improvements.md](improvements.md).
