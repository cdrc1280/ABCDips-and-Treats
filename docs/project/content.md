# Content

<!-- auto:start -->

_Blog, FAQ, contact messages, suggestions and AI-chat escalations._

### Files

**src/app/Domains/Content/Actions/**
- [`CreateBlogPostAction.php`](../../src/app/Domains/Content/Actions/CreateBlogPostAction.php)
- [`DeleteBlogPostAction.php`](../../src/app/Domains/Content/Actions/DeleteBlogPostAction.php)
- [`DeleteChatEscalationAction.php`](../../src/app/Domains/Content/Actions/DeleteChatEscalationAction.php)
- [`DeleteContactMessageAction.php`](../../src/app/Domains/Content/Actions/DeleteContactMessageAction.php)
- [`DeleteSuggestionAction.php`](../../src/app/Domains/Content/Actions/DeleteSuggestionAction.php)
- [`MarkImplementedSuggestionAction.php`](../../src/app/Domains/Content/Actions/MarkImplementedSuggestionAction.php) — Mark implemented
- [`MarkReadContactMessageAction.php`](../../src/app/Domains/Content/Actions/MarkReadContactMessageAction.php) — Mark as read
- [`MarkRepliedContactMessageAction.php`](../../src/app/Domains/Content/Actions/MarkRepliedContactMessageAction.php) — Mark as replied
- [`MarkReviewingSuggestionAction.php`](../../src/app/Domains/Content/Actions/MarkReviewingSuggestionAction.php) — Start reviewing
- [`PublishBlogPostAction.php`](../../src/app/Domains/Content/Actions/PublishBlogPostAction.php) — Publish now
- [`ReopenChatEscalationAction.php`](../../src/app/Domains/Content/Actions/ReopenChatEscalationAction.php) — Reopen
- [`ReplyChatEscalationAction.php`](../../src/app/Domains/Content/Actions/ReplyChatEscalationAction.php) — Reply
- [`ResolveChatEscalationAction.php`](../../src/app/Domains/Content/Actions/ResolveChatEscalationAction.php) — Mark resolved
- [`UpdateBlogPostAction.php`](../../src/app/Domains/Content/Actions/UpdateBlogPostAction.php)
- [`UpdateChatEscalationAction.php`](../../src/app/Domains/Content/Actions/UpdateChatEscalationAction.php)
- [`UpdateContactMessageAction.php`](../../src/app/Domains/Content/Actions/UpdateContactMessageAction.php)
- [`UpdateSuggestionAction.php`](../../src/app/Domains/Content/Actions/UpdateSuggestionAction.php)

**src/app/Domains/Content/Controllers/**
- [`BlogPostController.php`](../../src/app/Domains/Content/Controllers/BlogPostController.php)
- [`ChatEscalationController.php`](../../src/app/Domains/Content/Controllers/ChatEscalationController.php)
- [`ContactMessageController.php`](../../src/app/Domains/Content/Controllers/ContactMessageController.php)
- [`SuggestionController.php`](../../src/app/Domains/Content/Controllers/SuggestionController.php)

**src/app/Domains/Content/Data/**
- [`BlogPostData.php`](../../src/app/Domains/Content/Data/BlogPostData.php)
- [`BlogPostFilterData.php`](../../src/app/Domains/Content/Data/BlogPostFilterData.php)
- [`ChatEscalationData.php`](../../src/app/Domains/Content/Data/ChatEscalationData.php)
- [`ChatEscalationFilterData.php`](../../src/app/Domains/Content/Data/ChatEscalationFilterData.php)
- [`ContactMessageData.php`](../../src/app/Domains/Content/Data/ContactMessageData.php)
- [`ContactMessageFilterData.php`](../../src/app/Domains/Content/Data/ContactMessageFilterData.php)
- [`SuggestionData.php`](../../src/app/Domains/Content/Data/SuggestionData.php)
- [`SuggestionFilterData.php`](../../src/app/Domains/Content/Data/SuggestionFilterData.php)

**src/app/Domains/Content/Enums/**
- [`BlogPostPermission.php`](../../src/app/Domains/Content/Enums/BlogPostPermission.php)
- [`ChatEscalationPermission.php`](../../src/app/Domains/Content/Enums/ChatEscalationPermission.php)
- [`ContactMessagePermission.php`](../../src/app/Domains/Content/Enums/ContactMessagePermission.php)
- [`SuggestionPermission.php`](../../src/app/Domains/Content/Enums/SuggestionPermission.php)

**src/app/Domains/Content/Models/**
- [`BlogPost.php`](../../src/app/Domains/Content/Models/BlogPost.php)
- [`ChatEscalation.php`](../../src/app/Domains/Content/Models/ChatEscalation.php)
- [`ContactMessage.php`](../../src/app/Domains/Content/Models/ContactMessage.php)
- [`Faq.php`](../../src/app/Domains/Content/Models/Faq.php)
- [`Suggestion.php`](../../src/app/Domains/Content/Models/Suggestion.php)

**src/app/Domains/Content/Queries/**
- [`BlogPostQueries.php`](../../src/app/Domains/Content/Queries/BlogPostQueries.php)
- [`ChatEscalationQueries.php`](../../src/app/Domains/Content/Queries/ChatEscalationQueries.php)
- [`ChatEscalationSummaryQuery.php`](../../src/app/Domains/Content/Queries/ChatEscalationSummaryQuery.php)
- [`ContactMessageQueries.php`](../../src/app/Domains/Content/Queries/ContactMessageQueries.php)
- [`ContactMessageSummaryQuery.php`](../../src/app/Domains/Content/Queries/ContactMessageSummaryQuery.php)
- [`SuggestionQueries.php`](../../src/app/Domains/Content/Queries/SuggestionQueries.php)
- [`SuggestionSummaryQuery.php`](../../src/app/Domains/Content/Queries/SuggestionSummaryQuery.php)

**src/app/Domains/Content/Resources/**
- [`BlogPostResource.php`](../../src/app/Domains/Content/Resources/BlogPostResource.php)
- [`ChatEscalationResource.php`](../../src/app/Domains/Content/Resources/ChatEscalationResource.php)
- [`ContactMessageResource.php`](../../src/app/Domains/Content/Resources/ContactMessageResource.php)
- [`SuggestionResource.php`](../../src/app/Domains/Content/Resources/SuggestionResource.php)

### Data model

**`BlogPost`** → table `blog_posts` · traits: FreesUniqueValuesOnDelete, HasUuids, LogsModelActivity, SoftDeletes
- Columns: `id`, `title`, `slug`, `excerpt`?, `body`, `cover_image`?, `status`, `category`, `tags`?, `seo_title`?, `seo_description`?, `author_id`?, `published_at`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `author()` BelongsTo `User`

**`ChatEscalation`** → table `chat_escalations` · traits: HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `user_id`?, `guest_name`?, `guest_email`?, `conversation`?, `status`, `admin_notes`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `user()` BelongsTo `User`

**`ContactMessage`** → table `contact_messages` · traits: HasUuids, LogsModelActivity, SoftDeletes
- Columns: `id`, `name`, `email`, `phone`?, `subject`, `message`, `status`, `admin_notes`?, `replied_by`?, `replied_at`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `repliedBy()` BelongsTo `User`

**`Faq`** → table `faqs` · traits: HasUuids
- Columns: `id`, `question`, `answer`, `category`, `sort_order`, `is_active`, `created_at`?, `updated_at`?

**`Suggestion`** → table `suggestions` · traits: HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes
- Columns: `id`, `user_id`?, `name`, `email`, `category`, `subject`, `message`, `status`, `admin_notes`?, `created_at`?, `updated_at`?, `deleted_at`?
- Relations: `user()` BelongsTo `User`

### Routes

| Method | URL | Route name | Action | Page | Access |
|---|---|---|---|---|---|
| GET | `/admin/blog-posts` | `admin.blog-posts.index` | `BlogPostController@index` | `blog-post/index` | blog-posts-view |
| POST | `/admin/blog-posts` | `admin.blog-posts.store` | `BlogPostController@store` | — | blog-posts-create |
| GET | `/admin/blog-posts/create` | `admin.blog-posts.create` | `BlogPostController@create` | `blog-post/create` | blog-posts-create |
| PUT | `/admin/blog-posts/{blogPost}` | `admin.blog-posts.update` | `BlogPostController@update` | — | blog-posts-edit |
| DELETE | `/admin/blog-posts/{blogPost}` | `admin.blog-posts.destroy` | `BlogPostController@destroy` | — | blog-posts-delete |
| GET | `/admin/blog-posts/{blogPost}/edit` | `admin.blog-posts.edit` | `BlogPostController@edit` | `blog-post/edit` | blog-posts-edit |
| PATCH | `/admin/blog-posts/{blogPost}/publish` | `admin.blog-posts.publish` | `BlogPostController@publish` | — | blog-posts-edit |
| GET | `/admin/chat-escalations` | `admin.chat-escalations.index` | `ChatEscalationController@index` | `chat-escalation/index` | chat-escalations-view |
| PUT | `/admin/chat-escalations/{chatEscalation}` | `admin.chat-escalations.update` | `ChatEscalationController@update` | — | chat-escalations-edit |
| DELETE | `/admin/chat-escalations/{chatEscalation}` | `admin.chat-escalations.destroy` | `ChatEscalationController@destroy` | — | chat-escalations-delete |
| GET | `/admin/chat-escalations/{chatEscalation}/edit` | `admin.chat-escalations.edit` | `ChatEscalationController@edit` | `chat-escalation/edit` | chat-escalations-edit |
| PATCH | `/admin/chat-escalations/{chatEscalation}/reopen` | `admin.chat-escalations.reopen` | `ChatEscalationController@reopen` | — | chat-escalations-edit |
| PATCH | `/admin/chat-escalations/{chatEscalation}/reply` | `admin.chat-escalations.reply` | `ChatEscalationController@reply` | — | chat-escalations-edit |
| PATCH | `/admin/chat-escalations/{chatEscalation}/resolve` | `admin.chat-escalations.resolve` | `ChatEscalationController@resolve` | — | chat-escalations-edit |
| GET | `/admin/contact-messages` | `admin.contact-messages.index` | `ContactMessageController@index` | `contact-message/index` | contact-messages-view |
| PUT | `/admin/contact-messages/{contactMessage}` | `admin.contact-messages.update` | `ContactMessageController@update` | — | contact-messages-edit |
| DELETE | `/admin/contact-messages/{contactMessage}` | `admin.contact-messages.destroy` | `ContactMessageController@destroy` | — | contact-messages-delete |
| GET | `/admin/contact-messages/{contactMessage}/edit` | `admin.contact-messages.edit` | `ContactMessageController@edit` | `contact-message/edit` | contact-messages-edit |
| PATCH | `/admin/contact-messages/{contactMessage}/mark-read` | `admin.contact-messages.mark-read` | `ContactMessageController@markRead` | — | contact-messages-edit |
| PATCH | `/admin/contact-messages/{contactMessage}/mark-replied` | `admin.contact-messages.mark-replied` | `ContactMessageController@markReplied` | — | contact-messages-edit |
| GET | `/admin/suggestions` | `admin.suggestions.index` | `SuggestionController@index` | `suggestion/index` | suggestions-view |
| PUT | `/admin/suggestions/{suggestion}` | `admin.suggestions.update` | `SuggestionController@update` | — | suggestions-edit |
| DELETE | `/admin/suggestions/{suggestion}` | `admin.suggestions.destroy` | `SuggestionController@destroy` | — | suggestions-delete |
| GET | `/admin/suggestions/{suggestion}/edit` | `admin.suggestions.edit` | `SuggestionController@edit` | `suggestion/edit` | suggestions-edit |
| PATCH | `/admin/suggestions/{suggestion}/mark-implemented` | `admin.suggestions.mark-implemented` | `SuggestionController@markImplemented` | — | suggestions-edit |
| PATCH | `/admin/suggestions/{suggestion}/mark-reviewing` | `admin.suggestions.mark-reviewing` | `SuggestionController@markReviewing` | — | suggestions-edit |

### Permissions

`blog-posts-view`, `blog-posts-create`, `blog-posts-edit`, `blog-posts-delete`, `chat-escalations-view`, `chat-escalations-edit`, `chat-escalations-delete`, `contact-messages-view`, `contact-messages-edit`, `contact-messages-delete`, `suggestions-view`, `suggestions-edit`, `suggestions-delete`

### Pages (React)

[`src/blog-post/create.tsx`](../../src/resources/js/pages/blog-post/create.tsx), [`src/blog-post/edit.tsx`](../../src/resources/js/pages/blog-post/edit.tsx), [`src/blog-post/index.tsx`](../../src/resources/js/pages/blog-post/index.tsx), [`src/chat-escalation/edit.tsx`](../../src/resources/js/pages/chat-escalation/edit.tsx), [`src/chat-escalation/index.tsx`](../../src/resources/js/pages/chat-escalation/index.tsx), [`src/contact-message/edit.tsx`](../../src/resources/js/pages/contact-message/edit.tsx), [`src/contact-message/index.tsx`](../../src/resources/js/pages/contact-message/index.tsx), [`src/suggestion/edit.tsx`](../../src/resources/js/pages/suggestion/edit.tsx), [`src/suggestion/index.tsx`](../../src/resources/js/pages/suggestion/index.tsx)

### Tests

[`BlogPostTest`](../../src/tests/Feature/Content/BlogPost/BlogPostTest.php), [`ChatEscalationTest`](../../src/tests/Feature/Content/ChatEscalation/ChatEscalationTest.php), [`ContactMessageTest`](../../src/tests/Feature/Content/ContactMessage/ContactMessageTest.php), [`SuggestionTest`](../../src/tests/Feature/Content/Suggestion/SuggestionTest.php)

<!-- auto:end -->

## Business flows

See [flows §10](flows.md#10-customer-contact--back-office). Blog posts publish on `published_at` (scheduled posts stay hidden).

## Rules & invariants

- Blog HTML is sanitized (`Storefront/Support/SafeHtml`).
- Chat threads are bound to the user or the session; clients can't post as staff.

## Improvement audit

- Planned flow test: submission → back-office inbox → reply.

Full list: [improvements.md](improvements.md).
