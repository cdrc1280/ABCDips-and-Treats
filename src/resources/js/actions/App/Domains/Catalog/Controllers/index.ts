import CategoryController from './CategoryController'
import ProductController from './ProductController'

const Controllers = {
    CategoryController: Object.assign(CategoryController, CategoryController),
    ProductController: Object.assign(ProductController, ProductController),
}

export default Controllers