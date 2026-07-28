<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class PurchasingAndPayrollSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Suppliers
        Supplier::create([
            'name'           => 'San Miguel Mills, Inc.',
            'contact_person' => 'Juan Dela Cruz',
            'email'          => 'sales@sanmiguelmills.ph',
            'phone'          => '02-8888-1234',
            'address'        => 'Ortigas Center, Pasig City',
            'payment_terms'  => 'Net 30',
        ]);

        Supplier::create([
            'name'           => 'Anchor Dairy Philippines',
            'contact_person' => 'Maria Clara',
            'email'          => 'orders@anchordairy.ph',
            'phone'          => '02-8777-5678',
            'address'        => 'BGC, Taguig City',
            'payment_terms'  => 'Net 15',
        ]);

        Supplier::create([
            'name'           => 'Callebaut Fine Chocolate PH',
            'contact_person' => 'Pierre Laurent',
            'email'          => 'orders@callebaut.ph',
            'phone'          => '02-8999-4321',
            'address'        => 'Makati City',
            'payment_terms'  => 'COD',
        ]);

        // 2. Employees
        Employee::create([
            'employee_number'      => 'EMP-2026-001',
            'first_name'           => 'Chef Marco',
            'last_name'            => 'Santos',
            'email'                => 'headbaker@abcdips.test',
            'phone'                => '09171112233',
            'role_title'           => 'Head Baker',
            'employment_type'      => 'full_time',
            'basic_monthly_salary' => 35000.00,
            'hired_at'             => '2025-01-15',
        ]);

        Employee::create([
            'employee_number'      => 'EMP-2026-002',
            'first_name'           => 'Elena',
            'last_name'            => 'Reyes',
            'email'                => 'assistantbaker@abcdips.test',
            'phone'                => '09172223344',
            'role_title'           => 'Assistant Baker',
            'employment_type'      => 'full_time',
            'basic_monthly_salary' => 22000.00,
            'hired_at'             => '2025-06-01',
        ]);

        Employee::create([
            'employee_number'      => 'EMP-2026-003',
            'first_name'           => 'Liza',
            'last_name'            => 'Manalo',
            'email'                => 'cashier@abcdips.test',
            'phone'                => '09173334455',
            'role_title'           => 'Cashier',
            'employment_type'      => 'full_time',
            'basic_monthly_salary' => 18500.00,
            'hired_at'             => '2025-08-10',
        ]);
    }
}
