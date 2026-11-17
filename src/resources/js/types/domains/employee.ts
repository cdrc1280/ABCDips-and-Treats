/** Mirrors App\Domains\Employee\Resources\EmployeeResource (snake_case). */
export interface EmployeeRecord {
    id: string;
    first_name: string;
    last_name: string;
    employee_number: string | null;
    email: string;
    phone: string | null;
    user_id: string | null;
    user_email: string | null;
    role_title: string;
    role_title_label: string | null;
    employment_type: string;
    employment_type_label: string | null;
    basic_monthly_salary: number;
    hired_at: string | null;
    is_active: boolean;
    full_name: string;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Employee\Resources\AttendanceResource (snake_case). */
export interface AttendanceRecord {
    id: string;
    employee_id: string;
    employee_name: string | null;
    date: string | null;
    status: string;
    status_label: string | null;
    time_in: string | null;
    time_out: string | null;
    hours_worked: number;
    is_overtime: boolean;
    overtime_hours: number;
    is_holiday: boolean;
    notes: string | null;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Employee\Resources\PayrollResource (snake_case). */
export interface PayrollRecord {
    id: string;
    payroll_number: string | null;
    period_start: string | null;
    period_end: string | null;
    status: string;
    status_label: string | null;
    notes: string | null;
    items: Array<{
        id: string;
        employee_id: string | null;
        basic_pay: number | null;
        overtime_pay: number | null;
        holiday_pay: number | null;
        bonuses: number | null;
        attendance_deduction: number | null;
        attendance_note: string | null;
        sss_deduction: number | null;
        philhealth_deduction: number | null;
        pagibig_deduction: number | null;
        withholding_tax: number | null;
        employee_name: string | null;
        net_pay: number;
    }>;
    total_gross: number;
    total_deductions: number;
    total_net: number;
    items_count: number;
    paid_at: string | null;
    is_paid: boolean;
    created_at: string | null;
    updated_at: string | null;
}
