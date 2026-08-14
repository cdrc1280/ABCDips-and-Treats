import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Layouts
const StorefrontLayout = () => import('@/layouts/StorefrontLayout.vue')
const AuthLayout = () => import('@/layouts/AuthLayout.vue')
const AccountLayout = () => import('@/layouts/AccountLayout.vue')

// Storefront pages
const HomePage = () => import('@/pages/storefront/HomePage.vue')
const ShopPage = () => import('@/pages/storefront/ShopPage.vue')
const CategoryPage = () => import('@/pages/storefront/CategoryPage.vue')
const ProductDetailPage = () => import('@/pages/storefront/ProductDetailPage.vue')
const BestSellersPage = () => import('@/pages/storefront/BestSellersPage.vue')
const FeaturedPage = () => import('@/pages/storefront/FeaturedPage.vue')
const NewArrivalsPage = () => import('@/pages/storefront/NewArrivalsPage.vue')
const CustomOrderPage = () => import('@/pages/storefront/CustomOrderPage.vue')
const CartPage = () => import('@/pages/storefront/CartPage.vue')
const CheckoutPage = () => import('@/pages/storefront/CheckoutPage.vue')
const PaymentSuccessPage = () => import('@/pages/storefront/PaymentSuccessPage.vue')
const OrderConfirmationPage = () => import('@/pages/storefront/OrderConfirmationPage.vue')
const OrderTrackingPage = () => import('@/pages/storefront/OrderTrackingPage.vue')
const AboutPage = () => import('@/pages/storefront/AboutPage.vue')
const FaqPage = () => import('@/pages/storefront/FaqPage.vue')
const ContactPage = () => import('@/pages/storefront/ContactPage.vue')
const BlogPage = () => import('@/pages/storefront/BlogPage.vue')
const BlogPostDetailPage = () => import('@/pages/storefront/BlogPostDetailPage.vue')
const PrivacyPolicyPage = () => import('@/pages/storefront/PrivacyPolicyPage.vue')
const TermsPage = () => import('@/pages/storefront/TermsPage.vue')

// Account pages
const AccountOrdersPage = () => import('@/pages/account/AccountOrdersPage.vue')
const AccountWishlistPage = () => import('@/pages/account/AccountWishlistPage.vue')
const AccountProfilePage = () => import('@/pages/account/AccountProfilePage.vue')

// Auth pages
const LoginPage = () => import('@/pages/auth/LoginPage.vue')
const RegisterPage = () => import('@/pages/auth/RegisterPage.vue')

// Dev, POS & AI
const ComponentsPage = () => import('@/pages/dev/ComponentsPage.vue')
const PosPage = () => import('@/pages/dev/PosPage.vue')
const AiAdvisorPage = () => import('@/pages/dev/AiAdvisorPage.vue')

const routes = [
  {
    path: '/',
    component: StorefrontLayout,
    children: [
      { path: '', name: 'home', component: HomePage, meta: { title: 'ABCDips & Treats — Handcrafted Pastries' } },
      { path: 'shop', name: 'shop', component: ShopPage, meta: { title: 'Shop — ABCDips & Treats' } },
      { path: 'category/:slug', name: 'category', component: CategoryPage, meta: { title: 'Category — ABCDips & Treats' } },
      { path: 'products/:slug', name: 'product', component: ProductDetailPage, meta: { title: 'Product — ABCDips & Treats' } },
      { path: 'best-sellers', name: 'best-sellers', component: BestSellersPage, meta: { title: 'Best Sellers — ABCDips & Treats' } },
      { path: 'featured', name: 'featured', component: FeaturedPage, meta: { title: 'Featured — ABCDips & Treats' } },
      { path: 'new-arrivals', name: 'new-arrivals', component: NewArrivalsPage, meta: { title: 'New Arrivals — ABCDips & Treats' } },
      { path: 'custom-orders', name: 'custom-orders', component: CustomOrderPage, meta: { title: 'Custom Cake Orders — ABCDips & Treats' } },
      { path: 'cart', name: 'cart', component: CartPage, meta: { title: 'Your Cart — ABCDips & Treats' } },
      { path: 'checkout', name: 'checkout', component: CheckoutPage, meta: { title: 'Checkout — ABCDips & Treats', requiresAuth: true } },
      { path: 'checkout/payment-success', name: 'payment-success', component: PaymentSuccessPage, meta: { title: 'Payment Confirmed — ABCDips & Treats' } },
      { path: 'orders/:token/confirmation', name: 'order-confirmation', component: OrderConfirmationPage, meta: { title: 'Order Confirmed — ABCDips & Treats' } },
      { path: 'track/:token', name: 'order-tracking', component: OrderTrackingPage, alias: ['/orders/track/:token', 'orders/track/:token'], meta: { title: 'Track Order — ABCDips & Treats' } },
      { path: 'about', name: 'about', component: AboutPage, meta: { title: 'About Us — ABCDips & Treats' } },
      { path: 'faq', name: 'faq', component: FaqPage, meta: { title: 'FAQ — ABCDips & Treats' } },
      { path: 'contact', name: 'contact', component: ContactPage, meta: { title: 'Contact Us — ABCDips & Treats' } },
      { path: 'blog', name: 'blog', component: BlogPage, alias: ['/vlog', 'vlog'], meta: { title: 'The ABCDips Journal & Vlog — ABCDips & Treats' } },
      { path: 'blog/:slug', name: 'blog-post', component: BlogPostDetailPage, alias: ['/vlog/:slug'], meta: { title: 'Journal Post — ABCDips & Treats' } },
      { path: 'privacy', name: 'privacy', component: PrivacyPolicyPage, meta: { title: 'Privacy Policy — ABCDips & Treats' } },
      { path: 'terms', name: 'terms', component: TermsPage, meta: { title: 'Terms of Service — ABCDips & Treats' } },
      { path: 'suggestions', name: 'suggestions', component: () => import('../pages/storefront/SuggestionPage.vue'), meta: { title: 'Suggest a Feature — ABCDips & Treats' } },
    ],
  },
  {
    path: '/account',
    component: AccountLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'account', redirect: { name: 'account-orders' } },
      { path: 'orders', name: 'account-orders', component: AccountOrdersPage, meta: { title: 'My Orders' } },
      { path: 'wishlist', name: 'account-wishlist', component: AccountWishlistPage, meta: { title: 'My Wishlist' } },
      { path: 'profile', name: 'account-profile', component: AccountProfilePage, meta: { title: 'My Profile' } },
    ],
  },
  {
    path: '/auth',
    component: AuthLayout,
    meta: { guestOnly: true },
    children: [
      { path: 'login', name: 'login', component: LoginPage, meta: { title: 'Sign In — ABCDips & Treats' } },
      { path: 'register', name: 'register', component: RegisterPage, meta: { title: 'Create Account — ABCDips & Treats' } },
    ],
  },
  { path: '/dev/components', name: 'dev-components', component: ComponentsPage, meta: { requiresAdmin: true, title: 'Dev Components — ABCDips & Treats' } },
  { path: '/pos', name: 'pos', component: PosPage, meta: { requiresAdmin: true, title: 'POS Terminal — ABCDips & Treats' } },
  { path: '/ai-advisor', name: 'ai-advisor', component: AiAdvisorPage, meta: { requiresAdmin: true, title: 'AI Bakery Advisor' } },
  { path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('@/pages/NotFoundPage.vue') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    if (to.hash) return { el: to.hash, behavior: 'smooth' }
    return { top: 0, behavior: 'smooth' }
  },
})

router.beforeEach(async (to) => {
  document.title = to.meta.title || 'ABCDips & Treats'
  const authStore = useAuthStore()

  // Always sync from localStorage on every navigation.
  // This detects: logout in another tab, browser back-button bfcache restores,
  // or page reloads where the JS store state is fresh but token was cleared.
  const tokenPresent = authStore.syncFromStorage()

  // If token is gone and store thinks we're initialized, force a full re-check
  if (!tokenPresent) {
    authStore.initialized = false
  }

  // Restore authenticated session from token/API before checking route guards
  if (!authStore.initialized) {
    try {
      await authStore.fetchUser()
    } catch (err) {
      console.error('Error during router auth initialization:', err)
    }
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.requiresAdmin) {
    if (!authStore.isAuthenticated) {
      return { name: 'login', query: { redirect: to.fullPath } }
    }
    if (!authStore.isAdmin) {
      return { name: 'home' }
    }
  }

  if (to.meta.guestOnly && authStore.isAuthenticated) {
    return { name: 'home' }
  }
})

// Handle browser back-forward cache (bfcache) restores.
// When user goes back after logout, the browser may restore the JS state from cache.
// This listener detects that and forces a re-check of the actual auth state.
if (typeof window !== 'undefined') {
  window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
      // Page was restored from bfcache — force sync auth state
      const authStore = useAuthStore()
      const tokenPresent = authStore.syncFromStorage()
      if (!tokenPresent && authStore.isAuthenticated) {
        // State was stale — force hard reload so the UI reflects the logged-out state
        window.location.reload()
      }
    }
  })
}

export default router
