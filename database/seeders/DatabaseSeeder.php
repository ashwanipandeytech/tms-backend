<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\LeadSource;
use App\Models\PackageCategory;
use App\Models\VehicleType;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            ['id' => 1, 'name' => 'Super Admin', 'description' => 'Full access across system'],
            ['id' => 2, 'name' => 'Manager', 'description' => 'Manage leads, packages, bookings'],
            ['id' => 3, 'name' => 'Sales Executive', 'description' => 'Handle assigned leads & quotations'],
            ['id' => 4, 'name' => 'Operation Team', 'description' => 'Manage tours, cabs, hotels'],
            ['id' => 5, 'name' => 'Accounts', 'description' => 'Finance, invoices, payments'],
        ];
        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }

        // 2. Permissions
        $modules = ['leads', 'followups', 'packages', 'hotels', 'resorts', 'villas', 'cabs', 'quotations', 'bookings', 'payments', 'invoices', 'expenses', 'reports', 'settings', 'staff'];
        $actions = ['view', 'create', 'edit', 'delete', 'export'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::updateOrCreate(
                    ['module' => $module, 'action' => $action],
                    ['description' => ucfirst($action) . ' ' . ucfirst($module)]
                );
            }
        }

        // Assign all permissions to Super Admin role
        $allPermissions = Permission::pluck('id')->toArray();
        $superAdminRole = Role::find(1);
        if ($superAdminRole) {
            $superAdminRole->permissions()->sync($allPermissions);
        }

        // 3. User (Default Super Admin: admin@safarmusafir.com / Admin@123)
        User::updateOrCreate(
            ['email' => 'admin@safarmusafir.com'],
            [
                'role_id'  => 1,
                'name'     => 'Super Admin',
                'phone'    => '9999999999',
                'password' => Hash::make('Admin@123'),
                'status'   => 'active',
            ]
        );

        // 4. Lead Sources
        $sources = ['Website', 'WhatsApp', 'Facebook Ads', 'Google Ads', 'Manual', 'CSV Upload'];
        foreach ($sources as $source) {
            LeadSource::firstOrCreate(['name' => $source]);
        }

        // 5. Package Categories
        $categories = [
            ['name' => 'Domestic', 'type' => 'domestic'],
            ['name' => 'International', 'type' => 'international'],
        ];
        foreach ($categories as $cat) {
            PackageCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 6. Vehicle Types
        $vTypes = ['Sedan', 'SUV', 'Tempo Traveller', 'Luxury Car', 'Bus'];
        foreach ($vTypes as $vt) {
            VehicleType::firstOrCreate(['name' => $vt]);
        }

        // 7. Settings
        $settings = [
            ['setting_key' => 'company_name', 'setting_value' => 'Safar Musafir CRM'],
            ['setting_key' => 'gst_number', 'setting_value' => ''],
            ['setting_key' => 'default_gst_percent', 'setting_value' => '5'],
            ['setting_key' => 'currency', 'setting_value' => 'INR'],
        ];
        foreach ($settings as $setting) {
            Setting::firstOrCreate(['setting_key' => $setting['setting_key']], $setting);
        }
    }
}
