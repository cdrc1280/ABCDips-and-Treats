/**
 * Permission names, mirrored from the PHP enums (App\Domains\*\Enums\*Permission).
 * Never use one without the other: controllers enforce, the UI hides.
 */

/** Mirrors `App\Domains\Access\Enums\AccessPermission`. */
export const AccessPermission = {
    DashboardView: 'dashboard-view',
    RolesView: 'roles-view',
    RolesCreate: 'roles-create',
    RolesEdit: 'roles-edit',
    RolesDelete: 'roles-delete',
    AuditLogsView: 'audit-logs-view',
} as const;

/** Mirrors `App\Domains\Catalog\Enums\CategoryPermission`. */
export const CategoryPermission = {
    CategoriesView: 'categories-view',
    CategoriesCreate: 'categories-create',
    CategoriesEdit: 'categories-edit',
    CategoriesDelete: 'categories-delete',
} as const;

/** Mirrors `App\Domains\Inventory\Enums\SupplierPermission`. */
export const SupplierPermission = {
    SuppliersView: 'suppliers-view',
    SuppliersCreate: 'suppliers-create',
    SuppliersEdit: 'suppliers-edit',
    SuppliersDelete: 'suppliers-delete',
} as const;

/** Mirrors `App\Domains\Inventory\Enums\IngredientPermission`. */
export const IngredientPermission = {
    IngredientsView: 'ingredients-view',
    IngredientsCreate: 'ingredients-create',
    IngredientsEdit: 'ingredients-edit',
    IngredientsDelete: 'ingredients-delete',
} as const;

/** Mirrors `App\Domains\Inventory\Enums\PackagingMaterialPermission`. */
export const PackagingMaterialPermission = {
    PackagingMaterialsView: 'packaging-materials-view',
    PackagingMaterialsCreate: 'packaging-materials-create',
    PackagingMaterialsEdit: 'packaging-materials-edit',
    PackagingMaterialsDelete: 'packaging-materials-delete',
} as const;

/** Mirrors `App\Domains\Sales\Enums\CouponPermission`. */
export const CouponPermission = {
    CouponsView: 'coupons-view',
    CouponsCreate: 'coupons-create',
    CouponsEdit: 'coupons-edit',
    CouponsDelete: 'coupons-delete',
} as const;

/** Mirrors `App\Domains\Employee\Enums\EmployeePermission`. */
export const EmployeePermission = {
    EmployeesView: 'employees-view',
    EmployeesCreate: 'employees-create',
    EmployeesEdit: 'employees-edit',
    EmployeesDelete: 'employees-delete',
} as const;

/** Mirrors `App\Domains\Employee\Enums\AttendancePermission`. */
export const AttendancePermission = {
    AttendancesView: 'attendances-view',
    AttendancesCreate: 'attendances-create',
    AttendancesEdit: 'attendances-edit',
    AttendancesDelete: 'attendances-delete',
} as const;

/** Mirrors `App\Domains\Review\Enums\ReviewPermission`. */
export const ReviewPermission = {
    ReviewsView: 'reviews-view',
    ReviewsCreate: 'reviews-create',
    ReviewsEdit: 'reviews-edit',
    ReviewsDelete: 'reviews-delete',
} as const;

/** Mirrors `App\Domains\Content\Enums\BlogPostPermission`. */
export const BlogPostPermission = {
    BlogPostsView: 'blog-posts-view',
    BlogPostsCreate: 'blog-posts-create',
    BlogPostsEdit: 'blog-posts-edit',
    BlogPostsDelete: 'blog-posts-delete',
} as const;

/** Mirrors `App\Domains\Content\Enums\ContactMessagePermission`. */
export const ContactMessagePermission = {
    ContactMessagesView: 'contact-messages-view',
    ContactMessagesEdit: 'contact-messages-edit',
    ContactMessagesDelete: 'contact-messages-delete',
} as const;

/** Mirrors `App\Domains\Content\Enums\SuggestionPermission`. */
export const SuggestionPermission = {
    SuggestionsView: 'suggestions-view',
    SuggestionsEdit: 'suggestions-edit',
    SuggestionsDelete: 'suggestions-delete',
} as const;

/** Mirrors `App\Domains\Production\Enums\ProductionBatchPermission`. */
export const ProductionBatchPermission = {
    ProductionBatchesView: 'production-batches-view',
    ProductionBatchesCreate: 'production-batches-create',
    ProductionBatchesEdit: 'production-batches-edit',
    ProductionBatchesDelete: 'production-batches-delete',
} as const;

/** Mirrors `App\Domains\Sales\Enums\CustomOrderPermission`. */
export const CustomOrderPermission = {
    CustomOrdersView: 'custom-orders-view',
    CustomOrdersCreate: 'custom-orders-create',
    CustomOrdersEdit: 'custom-orders-edit',
    CustomOrdersDelete: 'custom-orders-delete',
} as const;

/** Mirrors `App\Domains\Catalog\Enums\ProductPermission`. */
export const ProductPermission = {
    ProductsView: 'products-view',
    ProductsCreate: 'products-create',
    ProductsEdit: 'products-edit',
    ProductsDelete: 'products-delete',
} as const;

/** Mirrors `App\Domains\Costing\Enums\RecipePermission`. */
export const RecipePermission = {
    RecipesView: 'recipes-view',
    RecipesCreate: 'recipes-create',
    RecipesEdit: 'recipes-edit',
    RecipesDelete: 'recipes-delete',
} as const;

/** Mirrors `App\Domains\Costing\Enums\ProductCostingPermission`. */
export const ProductCostingPermission = {
    ProductCostingsView: 'product-costings-view',
    ProductCostingsCreate: 'product-costings-create',
    ProductCostingsEdit: 'product-costings-edit',
    ProductCostingsDelete: 'product-costings-delete',
} as const;

/** Mirrors `App\Domains\Purchasing\Enums\QuotationPermission`. */
export const QuotationPermission = {
    QuotationsView: 'quotations-view',
    QuotationsCreate: 'quotations-create',
    QuotationsEdit: 'quotations-edit',
    QuotationsDelete: 'quotations-delete',
} as const;

/** Mirrors `App\Domains\Purchasing\Enums\PurchaseOrderPermission`. */
export const PurchaseOrderPermission = {
    PurchaseOrdersView: 'purchase-orders-view',
    PurchaseOrdersCreate: 'purchase-orders-create',
    PurchaseOrdersEdit: 'purchase-orders-edit',
    PurchaseOrdersDelete: 'purchase-orders-delete',
} as const;

/** Mirrors `App\Domains\Employee\Enums\PayrollPermission`. */
export const PayrollPermission = {
    PayrollsView: 'payrolls-view',
    PayrollsCreate: 'payrolls-create',
    PayrollsEdit: 'payrolls-edit',
    PayrollsDelete: 'payrolls-delete',
} as const;

/** Mirrors `App\Domains\Sales\Enums\OrderPermission`. */
export const OrderPermission = {
    OrdersView: 'orders-view',
    OrdersEdit: 'orders-edit',
} as const;

/** Mirrors `App\Domains\Sales\Enums\DeliveryPoolPermission`. */
export const DeliveryPoolPermission = {
    DeliveryPoolsView: 'delivery-pools-view',
    DeliveryPoolsCreate: 'delivery-pools-create',
    DeliveryPoolsEdit: 'delivery-pools-edit',
    DeliveryPoolsDelete: 'delivery-pools-delete',
} as const;

/** Mirrors `App\Domains\Content\Enums\ChatEscalationPermission`. */
export const ChatEscalationPermission = {
    ChatEscalationsView: 'chat-escalations-view',
    ChatEscalationsEdit: 'chat-escalations-edit',
    ChatEscalationsDelete: 'chat-escalations-delete',
} as const;

/** Mirrors `App\Domains\User\Enums\UserPermission`. */
export const UserPermission = {
    UsersView: 'users-view',
    UsersCreate: 'users-create',
    UsersEdit: 'users-edit',
    UsersDelete: 'users-delete',
} as const;

/** Mirrors `App\Domains\User\Enums\CustomerPermission`. */
export const CustomerPermission = {
    CustomersView: 'customers-view',
    CustomersEdit: 'customers-edit',
} as const;

/** Mirrors `App\Domains\Access\Enums\RolePermission`. */
export const RolePermission = {
    RolesView: 'roles-view',
    RolesCreate: 'roles-create',
    RolesEdit: 'roles-edit',
    RolesDelete: 'roles-delete',
} as const;

/** Mirrors `App\Domains\Setting\Enums\SettingPermission`. */
export const SettingPermission = {
    SettingsView: 'settings-view',
    SettingsEdit: 'settings-edit',
} as const;

/** Mirrors `App\Domains\Analytics\Enums\ReportPermission`. */
export const ReportPermission = {
    ReportsExport: 'reports-export',
} as const;

/** Mirrors `App\Domains\Pos\Enums\PosPermission`. */
export const PosPermission = {
    PosUse: 'pos-use',
} as const;

/** Mirrors `App\Domains\Analytics\Enums\AiAdvisorPermission`. */
export const AiAdvisorPermission = {
    AiAdvisorUse: 'ai-advisor-use',
} as const;

type ValuesOf<T> = T[keyof T];
export type PermissionName =
    | ValuesOf<typeof AccessPermission>
    | ValuesOf<typeof CategoryPermission>
    | ValuesOf<typeof SupplierPermission>
    | ValuesOf<typeof ProductPermission>
    | ValuesOf<typeof DeliveryPoolPermission>
    | ValuesOf<typeof ChatEscalationPermission>
    | ValuesOf<typeof UserPermission>
    | ValuesOf<typeof SettingPermission>
    | ValuesOf<typeof ReportPermission>
    | ValuesOf<typeof PosPermission>
    | ValuesOf<typeof AiAdvisorPermission>;
