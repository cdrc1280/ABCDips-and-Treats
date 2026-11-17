import SupplierController from './SupplierController'
import IngredientController from './IngredientController'
import PackagingMaterialController from './PackagingMaterialController'

const Controllers = {
    SupplierController: Object.assign(SupplierController, SupplierController),
    IngredientController: Object.assign(IngredientController, IngredientController),
    PackagingMaterialController: Object.assign(PackagingMaterialController, PackagingMaterialController),
}

export default Controllers