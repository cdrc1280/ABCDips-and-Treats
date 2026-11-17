import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import reports from './reports'
import preferences from './preferences'
import roles from './roles'
import aiAdvisor from './ai-advisor'
import activityLog from './activity-log'
import categories from './categories'
import products from './products'
import blogPosts from './blog-posts'
import contactMessages from './contact-messages'
import suggestions from './suggestions'
import chatEscalations from './chat-escalations'
import recipes from './recipes'
import productCostings from './product-costings'
import employees from './employees'
import attendances from './attendances'
import payrolls from './payrolls'
import suppliers from './suppliers'
import ingredients from './ingredients'
import packagingMaterials from './packaging-materials'
import notifications from './notifications'
import pos from './pos'
import productionBatches from './production-batches'
import purchaseOrders from './purchase-orders'
import quotations from './quotations'
import reviews from './reviews'
import coupons from './coupons'
import customOrders from './custom-orders'
import orders from './orders'
import deliveryPools from './delivery-pools'
import settings from './settings'
import users from './users'
import customers from './customers'
/**
* @see \App\Domains\Analytics\Controllers\DashboardController::dashboard
* @see Domains/Analytics/Controllers/DashboardController.php:28
* @route '/admin'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Domains\Analytics\Controllers\DashboardController::dashboard
* @see Domains/Analytics/Controllers/DashboardController.php:28
* @route '/admin'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Domains\Analytics\Controllers\DashboardController::dashboard
* @see Domains/Analytics/Controllers/DashboardController.php:28
* @route '/admin'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Analytics\Controllers\DashboardController::dashboard
* @see Domains/Analytics/Controllers/DashboardController.php:28
* @route '/admin'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Domains\Analytics\Controllers\DashboardController::dashboard
* @see Domains/Analytics/Controllers/DashboardController.php:28
* @route '/admin'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Analytics\Controllers\DashboardController::dashboard
* @see Domains/Analytics/Controllers/DashboardController.php:28
* @route '/admin'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Domains\Analytics\Controllers\DashboardController::dashboard
* @see Domains/Analytics/Controllers/DashboardController.php:28
* @route '/admin'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

const admin = {
    reports: Object.assign(reports, reports),
    preferences: Object.assign(preferences, preferences),
    roles: Object.assign(roles, roles),
    aiAdvisor: Object.assign(aiAdvisor, aiAdvisor),
    activityLog: Object.assign(activityLog, activityLog),
    categories: Object.assign(categories, categories),
    products: Object.assign(products, products),
    blogPosts: Object.assign(blogPosts, blogPosts),
    contactMessages: Object.assign(contactMessages, contactMessages),
    suggestions: Object.assign(suggestions, suggestions),
    chatEscalations: Object.assign(chatEscalations, chatEscalations),
    recipes: Object.assign(recipes, recipes),
    productCostings: Object.assign(productCostings, productCostings),
    dashboard: Object.assign(dashboard, dashboard),
    employees: Object.assign(employees, employees),
    attendances: Object.assign(attendances, attendances),
    payrolls: Object.assign(payrolls, payrolls),
    suppliers: Object.assign(suppliers, suppliers),
    ingredients: Object.assign(ingredients, ingredients),
    packagingMaterials: Object.assign(packagingMaterials, packagingMaterials),
    notifications: Object.assign(notifications, notifications),
    pos: Object.assign(pos, pos),
    productionBatches: Object.assign(productionBatches, productionBatches),
    purchaseOrders: Object.assign(purchaseOrders, purchaseOrders),
    quotations: Object.assign(quotations, quotations),
    reviews: Object.assign(reviews, reviews),
    coupons: Object.assign(coupons, coupons),
    customOrders: Object.assign(customOrders, customOrders),
    orders: Object.assign(orders, orders),
    deliveryPools: Object.assign(deliveryPools, deliveryPools),
    settings: Object.assign(settings, settings),
    users: Object.assign(users, users),
    customers: Object.assign(customers, customers),
}

export default admin