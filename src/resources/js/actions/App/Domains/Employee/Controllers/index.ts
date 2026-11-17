import EmployeeController from './EmployeeController'
import AttendanceController from './AttendanceController'
import PayrollController from './PayrollController'

const Controllers = {
    EmployeeController: Object.assign(EmployeeController, EmployeeController),
    AttendanceController: Object.assign(AttendanceController, AttendanceController),
    PayrollController: Object.assign(PayrollController, PayrollController),
}

export default Controllers