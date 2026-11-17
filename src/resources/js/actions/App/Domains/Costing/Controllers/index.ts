import RecipeController from './RecipeController'
import ProductCostingController from './ProductCostingController'

const Controllers = {
    RecipeController: Object.assign(RecipeController, RecipeController),
    ProductCostingController: Object.assign(ProductCostingController, ProductCostingController),
}

export default Controllers