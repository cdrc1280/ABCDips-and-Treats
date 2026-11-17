import NotificationController from './NotificationController'
import UserController from './UserController'
import CustomerController from './CustomerController'

const Controllers = {
    NotificationController: Object.assign(NotificationController, NotificationController),
    UserController: Object.assign(UserController, UserController),
    CustomerController: Object.assign(CustomerController, CustomerController),
}

export default Controllers