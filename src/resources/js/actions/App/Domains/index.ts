import Sales from './Sales'
import Preference from './Preference'
import Access from './Access'
import Analytics from './Analytics'
import Audit from './Audit'
import Catalog from './Catalog'
import Content from './Content'
import Costing from './Costing'
import Employee from './Employee'
import Inventory from './Inventory'
import User from './User'
import Pos from './Pos'
import Production from './Production'
import Purchasing from './Purchasing'
import Review from './Review'
import Setting from './Setting'
import Storefront from './Storefront'

const Domains = {
    Sales: Object.assign(Sales, Sales),
    Preference: Object.assign(Preference, Preference),
    Access: Object.assign(Access, Access),
    Analytics: Object.assign(Analytics, Analytics),
    Audit: Object.assign(Audit, Audit),
    Catalog: Object.assign(Catalog, Catalog),
    Content: Object.assign(Content, Content),
    Costing: Object.assign(Costing, Costing),
    Employee: Object.assign(Employee, Employee),
    Inventory: Object.assign(Inventory, Inventory),
    User: Object.assign(User, User),
    Pos: Object.assign(Pos, Pos),
    Production: Object.assign(Production, Production),
    Purchasing: Object.assign(Purchasing, Purchasing),
    Review: Object.assign(Review, Review),
    Setting: Object.assign(Setting, Setting),
    Storefront: Object.assign(Storefront, Storefront),
}

export default Domains