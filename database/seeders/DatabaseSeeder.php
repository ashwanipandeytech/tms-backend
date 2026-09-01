<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Credentials Reference:
     * Super Admin: travel@demohandler.in / Admin@123
     * 
     * Company 1 (Radhey Shyam Travels - Enterprise):
     * Manager: manager@radheyshyam.com / Manager@123
     * Sales: sales@radheyshyam.com / Sales@123
     * Ops: ops@radheyshyam.com / Ops@123
     * Accounts: accounts@radheyshyam.com / Accounts@123
     * 
     * Company 2 (Sunrise Travel Agency - Professional):
     * Manager: manager@sunrisetravel.com / Manager@123
     * Sales: sales@sunrisetravel.com / Sales@123
     * Ops: ops@sunrisetravel.com / Ops@123
     * 
     * Company 3 (Wanderlust Adventures - Starter):
     * Manager: manager@wanderlust.com / Manager@123
     * Sales: sales@wanderlust.com / Sales@123
     * 
     * Company 4 (Budget Tours India - Free Trial):
     * Manager: manager@budgettours.com / Manager@123
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Subscription Plans
        $plans = [
            ['id' => 1, 'name' => 'Free Trial Plan', 'slug' => 'free-trial-plan', 'monthly_price' => 0, 'yearly_price' => 0, 'base_user_seats' => 1, 'addon_seat_price' => 0, 'modules' => json_encode(['leads', 'customers']), 'database_type' => 'shared', 'status' => 'active'],
            ['id' => 2, 'name' => 'Starter Plan', 'slug' => 'starter-plan', 'monthly_price' => 999, 'yearly_price' => 9990, 'base_user_seats' => 5, 'addon_seat_price' => 499, 'modules' => json_encode(['leads', 'customers', 'packages', 'quotations', 'bookings']), 'database_type' => 'shared', 'status' => 'active'],
            ['id' => 3, 'name' => 'Professional Plan', 'slug' => 'professional-plan', 'monthly_price' => 1999, 'yearly_price' => 19990, 'base_user_seats' => 15, 'addon_seat_price' => 499, 'modules' => json_encode(['leads', 'customers', 'packages', 'hotels', 'cabs', 'quotations', 'bookings', 'invoices', 'payments']), 'database_type' => 'shared', 'status' => 'active'],
            ['id' => 4, 'name' => 'Enterprise Plan', 'slug' => 'enterprise-plan', 'monthly_price' => 4999, 'yearly_price' => 49990, 'base_user_seats' => 999, 'addon_seat_price' => 399, 'modules' => json_encode(['leads', 'customers', 'packages', 'hotels', 'resorts', 'villas', 'cabs', 'quotations', 'bookings', 'invoices', 'payments', 'expenses', 'reports', 'whatsapp_templates', 'email_templates']), 'database_type' => 'dedicated', 'status' => 'active'],
        ];

        foreach ($plans as $plan) {
            DB::table('subscription_plans')->updateOrInsert(['id' => $plan['id']], $plan);
        }

        $companies = [
            ['id' => 1, 'name' => 'Radhey Shyam Travels', 'subdomain' => 'radheyshyam', 'plan_id' => 4, 'subscription_status' => 'active'],
            ['id' => 2, 'name' => 'Sunrise Travel Agency', 'subdomain' => 'sunrisetravel-demo', 'plan_id' => 3, 'subscription_status' => 'active'],
            ['id' => 3, 'name' => 'Wanderlust Adventures', 'subdomain' => 'wanderlust', 'plan_id' => 2, 'subscription_status' => 'active'],
            ['id' => 4, 'name' => 'Budget Tours India', 'subdomain' => 'budgettours', 'plan_id' => 1, 'subscription_status' => 'active'],
        ];

        foreach ($companies as $c) {
            DB::table('companies')->updateOrInsert(['id' => $c['id']], $c);
        }

        // 3. Permissions
        $modules = ['leads', 'followups', 'packages', 'hotels', 'resorts', 'villas', 'cabs', 'quotations', 'bookings', 'payments', 'invoices', 'expenses', 'reports', 'settings', 'staff', 'customers', 'dashboard'];
        $actions = ['view', 'create', 'edit', 'delete', 'export'];
        
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                DB::table('permissions')->updateOrInsert(
                    ['module' => $module, 'action' => $action],
                    ['description' => ucfirst($action) . ' ' . ucfirst($module)]
                );
            }
        }

        // 4. Roles (Universal)
        $roles = [
            ['id' => 1, 'company_id' => null, 'name' => 'Super Admin', 'description' => 'System Administrator'],
            ['id' => 2, 'company_id' => null, 'name' => 'Manager', 'description' => 'Company Manager'],
            ['id' => 3, 'company_id' => null, 'name' => 'Sales Executive', 'description' => 'Sales Representative'],
            ['id' => 4, 'company_id' => null, 'name' => 'Operation Team', 'description' => 'Operations Executive'],
            ['id' => 5, 'company_id' => null, 'name' => 'Accounts', 'description' => 'Accountant'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['id' => $role['id']], $role);
        }

        // Give Super Admin & Manager ALL permissions
        $allPermissions = DB::table('permissions')->pluck('id');
        foreach ($allPermissions as $permId) {
            DB::table('role_permissions')->updateOrInsert(['role_id' => 1, 'permission_id' => $permId]);
            DB::table('role_permissions')->updateOrInsert(['role_id' => 2, 'permission_id' => $permId]);
        }

        // Sales Executive permissions: leads, followups, quotations, bookings, customers, packages, dashboard
        $salesPermIds = DB::table('permissions')
            ->whereIn('module', ['leads', 'followups', 'quotations', 'bookings', 'customers', 'packages', 'dashboard'])
            ->pluck('id');
        foreach ($salesPermIds as $permId) {
            DB::table('role_permissions')->updateOrInsert(['role_id' => 3, 'permission_id' => $permId]);
        }

        // Operation Team permissions: bookings, cabs, hotels, resorts, villas, packages, dashboard
        $opsPermIds = DB::table('permissions')
            ->whereIn('module', ['bookings', 'cabs', 'hotels', 'resorts', 'villas', 'packages', 'dashboard'])
            ->pluck('id');
        foreach ($opsPermIds as $permId) {
            DB::table('role_permissions')->updateOrInsert(['role_id' => 4, 'permission_id' => $permId]);
        }

        // Accounts permissions: payments, invoices, expenses, reports, dashboard, bookings (view/export)
        $accountsPermIds = DB::table('permissions')
            ->whereIn('module', ['payments', 'invoices', 'expenses', 'reports', 'dashboard'])
            ->orWhere(fn($q) => $q->where('module', 'bookings')->whereIn('action', ['view', 'export']))
            ->pluck('id');
        foreach ($accountsPermIds as $permId) {
            DB::table('role_permissions')->updateOrInsert(['role_id' => 5, 'permission_id' => $permId]);
        }

        // 5. Users
        $users = [
            // Super Admin
            ['id' => 1, 'company_id' => null, 'role_id' => 1, 'name' => 'Super Admin', 'email' => 'travel@demohandler.in', 'password' => Hash::make('Admin@123'), 'status' => 'active'],
            
            // Company 1
            ['id' => 2, 'company_id' => 1, 'role_id' => 2, 'name' => 'RST Manager', 'email' => 'manager@radheyshyam.com', 'password' => Hash::make('Manager@123'), 'status' => 'active'],
            ['id' => 3, 'company_id' => 1, 'role_id' => 3, 'name' => 'RST Sales', 'email' => 'sales@radheyshyam.com', 'password' => Hash::make('Sales@123'), 'status' => 'active'],
            ['id' => 4, 'company_id' => 1, 'role_id' => 4, 'name' => 'RST Ops', 'email' => 'ops@radheyshyam.com', 'password' => Hash::make('Ops@123'), 'status' => 'active'],
            ['id' => 5, 'company_id' => 1, 'role_id' => 5, 'name' => 'RST Accounts', 'email' => 'accounts@radheyshyam.com', 'password' => Hash::make('Accounts@123'), 'status' => 'active'],

            // Company 2
            ['id' => 6, 'company_id' => 2, 'role_id' => 2, 'name' => 'STA Manager', 'email' => 'manager@sunrisetravel.com', 'password' => Hash::make('Manager@123'), 'status' => 'active'],
            ['id' => 7, 'company_id' => 2, 'role_id' => 3, 'name' => 'STA Sales', 'email' => 'sales@sunrisetravel.com', 'password' => Hash::make('Sales@123'), 'status' => 'active'],
            ['id' => 8, 'company_id' => 2, 'role_id' => 4, 'name' => 'STA Ops', 'email' => 'ops@sunrisetravel.com', 'password' => Hash::make('Ops@123'), 'status' => 'active'],

            // Company 3
            ['id' => 9, 'company_id' => 3, 'role_id' => 2, 'name' => 'WA Manager', 'email' => 'manager@wanderlust.com', 'password' => Hash::make('Manager@123'), 'status' => 'active'],
            ['id' => 10, 'company_id' => 3, 'role_id' => 3, 'name' => 'WA Sales', 'email' => 'sales@wanderlust.com', 'password' => Hash::make('Sales@123'), 'status' => 'active'],

            // Company 4
            ['id' => 11, 'company_id' => 4, 'role_id' => 2, 'name' => 'BTI Manager', 'email' => 'manager@budgettours.com', 'password' => Hash::make('Manager@123'), 'status' => 'active'],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(['email' => $user['email']], $user);
        }

        // Setup Tenant Data
        $this->seedCompany1($now);
        $this->seedCompany2($now);
        $this->seedCompany3($now);
        $this->seedCompany4($now);
    }

    private function seedCompany1(Carbon $now): void
    {
        $cId = 1;

        // Lead Sources
        DB::table('lead_sources')->updateOrInsert(['company_id' => $cId, 'name' => 'Facebook Ads']);
        DB::table('lead_sources')->updateOrInsert(['company_id' => $cId, 'name' => 'Google Search']);
        
        // Settings
        DB::table('settings')->updateOrInsert(['company_id' => $cId, 'setting_key' => 'currency'], ['setting_value' => 'INR']);

        // Package Categories & Destinations
        DB::table('package_categories')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['name' => 'Honeymoon', 'type' => 'domestic']);
        DB::table('package_categories')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['name' => 'Family', 'type' => 'domestic']);
        
        DB::table('destinations')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['category_id' => 1, 'name' => 'Kashmir']);
        DB::table('destinations')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['category_id' => 2, 'name' => 'Goa']);

        // Packages
        DB::table('packages')->updateOrInsert(
            ['id' => 1, 'company_id' => $cId],
            ['name' => 'Romantic Kashmir', 'destination_id' => 1, 'category_id' => 1, 'nights' => 5, 'days' => 6, 'price' => 35000, 'gst_applicable' => 1, 'gst_percent' => 5, 'status' => 'active']
        );
        DB::table('packages')->updateOrInsert(
            ['id' => 2, 'company_id' => $cId],
            ['name' => 'Goa Family Trip', 'destination_id' => 2, 'category_id' => 2, 'nights' => 3, 'days' => 4, 'price' => 15000, 'gst_applicable' => 1, 'gst_percent' => 5, 'status' => 'active']
        );
        DB::table('packages')->updateOrInsert(
            ['id' => 3, 'company_id' => $cId],
            ['name' => 'Kerala Backwaters', 'destination_id' => 2, 'category_id' => 2, 'nights' => 4, 'days' => 5, 'price' => 25000, 'gst_applicable' => 0, 'gst_percent' => 0, 'status' => 'active']
        );

        // Hotels & Rooms
        DB::table('hotels')->updateOrInsert(
            ['id' => 1, 'company_id' => $cId],
            ['name' => 'Taj Srinagar', 'location' => 'Srinagar', 'star_category' => '5', 'status' => 'active']
        );
        DB::table('hotel_rooms')->updateOrInsert(['hotel_id' => 1, 'room_type' => 'Deluxe'], ['meal_plan' => 'MAP', 'price' => 8000]);
        
        DB::table('hotels')->updateOrInsert(
            ['id' => 2, 'company_id' => $cId],
            ['name' => 'Goa Beach Resort', 'location' => 'North Goa', 'star_category' => '4', 'status' => 'active']
        );
        DB::table('hotel_rooms')->updateOrInsert(['hotel_id' => 2, 'room_type' => 'Sea View'], ['meal_plan' => 'CP', 'price' => 5000]);

        // Resorts & Villas
        DB::table('resorts')->updateOrInsert(
            ['id' => 1, 'company_id' => $cId],
            ['name' => 'Gulmarg Ski Resort', 'location' => 'Gulmarg', 'status' => 'active']
        );
        DB::table('resort_rooms')->updateOrInsert(['resort_id' => 1, 'room_type' => 'Chalet'], ['season' => 'peak', 'price' => 12000, 'inventory' => 5]);

        DB::table('villas')->updateOrInsert(
            ['id' => 1, 'company_id' => $cId],
            ['name' => 'Private Pool Villa Goa', 'location' => 'South Goa', 'capacity' => 6, 'bedrooms' => 3, 'price' => 20000, 'status' => 'active']
        );

        // Vehicles & Vendors
        DB::table('vendors')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['name' => 'Kashmir Cabs', 'type' => 'cab']);
        DB::table('vendors')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['name' => 'Goa Travels', 'type' => 'cab']);
        
        DB::table('vehicle_types')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['name' => 'Sedan']);
        DB::table('vehicle_types')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['name' => 'SUV']);

        DB::table('vehicles')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['vehicle_type_id' => 1, 'vendor_id' => 1, 'model' => 'Dzire', 'number_plate' => 'JK01AB1234', 'status' => 'available']);
        DB::table('vehicles')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['vehicle_type_id' => 2, 'vendor_id' => 1, 'model' => 'Innova', 'number_plate' => 'JK01AB5678', 'status' => 'booked']);
        DB::table('vehicles')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['vehicle_type_id' => 1, 'vendor_id' => 2, 'model' => 'Etios', 'number_plate' => 'GA03XY9999', 'status' => 'available']);

        DB::table('drivers')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['name' => 'Ramesh', 'phone' => '9876543210', 'vehicle_id' => 1]);
        DB::table('drivers')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['name' => 'Suresh', 'phone' => '9876543211', 'vehicle_id' => 2]);

        DB::table('cab_rates')->updateOrInsert(['company_id' => $cId, 'vehicle_type_id' => 1], ['rate_per_km' => 12, 'rate_per_day' => 2000, 'base_fare' => 500]);

        // Leads (All Statuses)
        $statuses = ['new', 'contacted', 'followup', 'interested', 'quotation_sent', 'negotiation', 'confirmed', 'lost'];
        foreach ($statuses as $idx => $status) {
            $leadId = $idx + 1;
            DB::table('leads')->updateOrInsert(
                ['id' => $leadId, 'company_id' => $cId],
                ['name' => 'Lead ' . $status, 'email' => "lead{$leadId}@test.com", 'phone' => "900000000{$leadId}", 'destination' => 'Kashmir', 'status' => $status, 'created_by' => 3]
            );

            // Lead Activities
            DB::table('lead_activities')->updateOrInsert(
                ['company_id' => $cId, 'lead_id' => $leadId, 'activity_type' => 'status_change'],
                ['user_id' => 3, 'description' => "Status changed to {$status}"]
            );
        }

        // Follow ups
        DB::table('follow_ups')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['lead_id' => 1, 'assigned_to' => 3, 'follow_up_date' => $now->toDateString(), 'type' => 'call', 'status' => 'pending']);
        DB::table('follow_ups')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['lead_id' => 2, 'assigned_to' => 3, 'follow_up_date' => $now->subDays(1)->toDateString(), 'type' => 'whatsapp', 'status' => 'missed']);
        DB::table('follow_ups')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['lead_id' => 3, 'assigned_to' => 3, 'follow_up_date' => $now->subDays(2)->toDateString(), 'type' => 'meeting', 'status' => 'done']);

        // Customers
        DB::table('customers')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['name' => 'John Doe', 'email' => 'john@test.com', 'phone' => '8000000001', 'lead_id' => 7, 'status' => 'active']);
        DB::table('customers')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['name' => 'Jane Smith', 'email' => 'jane@test.com', 'phone' => '8000000002', 'lead_id' => 8, 'status' => 'active']);
        DB::table('customers')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['name' => 'Bob Wilson', 'email' => 'bob@test.com', 'phone' => '8000000003', 'status' => 'inactive']);

        // Coupons
        DB::table('coupons')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['code' => 'FESTIVE10', 'type' => 'percent', 'value' => 10, 'status' => 'active']);

        // Quotations
        DB::table('quotations')->updateOrInsert(
            ['id' => 1, 'company_id' => $cId],
            ['quotation_no' => 'QT-C1-001', 'lead_id' => 7, 'customer_name' => 'John Doe', 'package_id' => 1, 'sub_total' => 35000, 'final_amount' => 36750, 'status' => 'accepted', 'created_by' => 3]
        );
        DB::table('quotation_items')->updateOrInsert(['quotation_id' => 1, 'item_type' => 'package'], ['description' => 'Romantic Kashmir', 'qty' => 1, 'amount' => 35000]);

        DB::table('quotations')->updateOrInsert(
            ['id' => 2, 'company_id' => $cId],
            ['quotation_no' => 'QT-C1-002', 'lead_id' => 6, 'customer_name' => 'Alice', 'package_id' => 2, 'sub_total' => 15000, 'final_amount' => 15750, 'status' => 'draft', 'created_by' => 3]
        );

        // Itineraries
        DB::table('itineraries')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['quotation_id' => 1, 'package_id' => 1, 'title' => '6D5N Kashmir Tour']);
        DB::table('itinerary_days')->updateOrInsert(['itinerary_id' => 1, 'day_number' => 1], ['title' => 'Arrival', 'description' => 'Arrive in Srinagar']);

        // Bookings (Status: confirmed, pending, completed, cancelled)
        DB::table('bookings')->updateOrInsert(
            ['id' => 1, 'company_id' => $cId],
            ['booking_no' => 'BK-C1-001', 'lead_id' => 7, 'quotation_id' => 1, 'customer_id' => 1, 'package_id' => 1, 'total_amount' => 36750, 'paid_amount' => 10000, 'due_amount' => 26750, 'status' => 'confirmed', 'created_by' => 3]
        );
        DB::table('bookings')->updateOrInsert(
            ['id' => 2, 'company_id' => $cId],
            ['booking_no' => 'BK-C1-002', 'lead_id' => 8, 'customer_id' => 2, 'package_id' => 2, 'total_amount' => 15750, 'paid_amount' => 15750, 'due_amount' => 0, 'status' => 'completed', 'created_by' => 3]
        );

        // Payments & Invoices
        DB::table('payments')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['booking_id' => 1, 'amount' => 10000, 'payment_type' => 'advance', 'payment_mode' => 'UPI', 'txn_reference' => 'UPI123456', 'paid_at' => $now]);
        DB::table('payments')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['booking_id' => 2, 'amount' => 15750, 'payment_type' => 'full', 'payment_mode' => 'Bank Transfer', 'txn_reference' => 'NEFT9876', 'paid_at' => $now->subDays(5)]);

        DB::table('invoices')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['invoice_no' => 'INV-C1-001', 'booking_id' => 1, 'amount' => 35000, 'gst_amount' => 1750, 'total' => 36750, 'status' => 'partial']);
        DB::table('invoices')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['invoice_no' => 'INV-C1-002', 'booking_id' => 2, 'amount' => 15000, 'gst_amount' => 750, 'total' => 15750, 'status' => 'paid']);

        // Expenses & Vendor Payments
        DB::table('expenses')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['category' => 'Marketing', 'amount' => 5000, 'description' => 'FB Ads', 'expense_date' => $now->toDateString(), 'created_by' => 2]);
        DB::table('vendor_payments')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['vendor_id' => 1, 'amount' => 8000, 'payment_date' => $now->toDateString(), 'reference' => 'CASH']);

        // Cab Bookings
        DB::table('cab_bookings')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['lead_id' => 7, 'vehicle_id' => 1, 'driver_id' => 1, 'pickup_location' => 'Srinagar Airport', 'drop_location' => 'Hotel Taj', 'amount' => 1500, 'status' => 'confirmed']);

        // Logs & Templates
        DB::table('whatsapp_templates')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['name' => 'Welcome', 'event_trigger' => 'lead_created', 'message_body' => 'Hello {name}, welcome to RST.', 'status' => 'active']);
        DB::table('whatsapp_logs')->updateOrInsert(['id' => 1, 'company_id' => $cId], ['lead_id' => 1, 'phone' => '9000000001', 'message' => 'Hello Lead new, welcome to RST.', 'status' => 'delivered']);
    }

    private function seedCompany2(Carbon $now): void
    {
        $cId = 2;
        
        DB::table('package_categories')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['name' => 'Adventure', 'type' => 'domestic']);
        DB::table('destinations')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['category_id' => 3, 'name' => 'Himachal']);
        
        DB::table('packages')->updateOrInsert(
            ['id' => 4, 'company_id' => $cId],
            ['name' => 'Manali Adventure', 'destination_id' => 3, 'category_id' => 3, 'nights' => 4, 'days' => 5, 'price' => 18000, 'gst_applicable' => 0, 'status' => 'active']
        );
        DB::table('packages')->updateOrInsert(
            ['id' => 5, 'company_id' => $cId],
            ['name' => 'Shimla Retreat', 'destination_id' => 3, 'category_id' => 3, 'nights' => 2, 'days' => 3, 'price' => 12000, 'gst_applicable' => 0, 'status' => 'active']
        );

        DB::table('hotels')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['name' => 'Snow Valley Manali', 'location' => 'Manali', 'status' => 'active']);
        DB::table('hotels')->updateOrInsert(['id' => 4, 'company_id' => $cId], ['name' => 'Radisson Shimla', 'location' => 'Shimla', 'status' => 'active']);
        
        DB::table('resorts')->updateOrInsert(['id' => 2, 'company_id' => $cId], ['name' => 'Kullu Nature Resort', 'location' => 'Kullu', 'status' => 'active']);

        DB::table('vendors')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['name' => 'Himachal Taxi', 'type' => 'cab']);
        DB::table('vehicle_types')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['name' => 'Hatchback']);
        DB::table('vehicles')->updateOrInsert(['id' => 4, 'company_id' => $cId], ['vehicle_type_id' => 3, 'vendor_id' => 3, 'model' => 'Alto', 'number_plate' => 'HP01CD1234', 'status' => 'available']);
        DB::table('drivers')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['name' => 'Kamal', 'phone' => '7777777777', 'vehicle_id' => 4]);

        for ($i=1; $i<=4; $i++) {
            $status = ['new', 'contacted', 'interested', 'confirmed'][$i-1];
            DB::table('leads')->updateOrInsert(
                ['id' => 10+$i, 'company_id' => $cId],
                ['name' => "STA Lead $i", 'email' => "stalead{$i}@test.com", 'phone' => "700000000{$i}", 'status' => $status]
            );
        }

        DB::table('customers')->updateOrInsert(['id' => 4, 'company_id' => $cId], ['name' => 'Charlie STA', 'email' => 'charlie@sta.com', 'phone' => '6000000001', 'status' => 'active']);
        DB::table('customers')->updateOrInsert(['id' => 5, 'company_id' => $cId], ['name' => 'Diana STA', 'email' => 'diana@sta.com', 'phone' => '6000000002', 'status' => 'active']);

        DB::table('quotations')->updateOrInsert(
            ['id' => 3, 'company_id' => $cId],
            ['quotation_no' => 'QT-C2-001', 'lead_id' => 14, 'customer_name' => 'Charlie STA', 'package_id' => 4, 'sub_total' => 18000, 'final_amount' => 18000, 'status' => 'accepted', 'created_by' => 7]
        );

        DB::table('bookings')->updateOrInsert(
            ['id' => 3, 'company_id' => $cId],
            ['booking_no' => 'BK-C2-001', 'lead_id' => 14, 'quotation_id' => 3, 'customer_id' => 4, 'package_id' => 4, 'total_amount' => 18000, 'paid_amount' => 18000, 'due_amount' => 0, 'status' => 'confirmed', 'created_by' => 7]
        );

        DB::table('payments')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['booking_id' => 3, 'amount' => 18000, 'payment_type' => 'full', 'payment_mode' => 'Credit Card', 'txn_reference' => 'CC123', 'paid_at' => $now]);
        DB::table('invoices')->updateOrInsert(['id' => 3, 'company_id' => $cId], ['invoice_no' => 'INV-C2-001', 'booking_id' => 3, 'amount' => 18000, 'gst_amount' => 0, 'total' => 18000, 'status' => 'paid']);
    }

    private function seedCompany3(Carbon $now): void
    {
        $cId = 3;

        DB::table('package_categories')->updateOrInsert(['id' => 4, 'company_id' => $cId], ['name' => 'Heritage', 'type' => 'domestic']);
        DB::table('destinations')->updateOrInsert(['id' => 4, 'company_id' => $cId], ['category_id' => 4, 'name' => 'Rajasthan']);
        
        DB::table('packages')->updateOrInsert(
            ['id' => 6, 'company_id' => $cId],
            ['name' => 'Jaipur Tour', 'destination_id' => 4, 'category_id' => 4, 'nights' => 2, 'days' => 3, 'price' => 10000, 'gst_applicable' => 0, 'status' => 'active']
        );

        DB::table('hotels')->updateOrInsert(['id' => 5, 'company_id' => $cId], ['name' => 'Rajputana Heritage', 'location' => 'Jaipur', 'status' => 'active']);
        DB::table('vendors')->updateOrInsert(['id' => 4, 'company_id' => $cId], ['name' => 'RJ Cabs', 'type' => 'cab']);
        DB::table('vehicle_types')->updateOrInsert(['id' => 4, 'company_id' => $cId], ['name' => 'Sedan']);
        DB::table('vehicles')->updateOrInsert(['id' => 5, 'company_id' => $cId], ['vehicle_type_id' => 4, 'vendor_id' => 4, 'model' => 'City', 'number_plate' => 'RJ14AA1111', 'status' => 'available']);

        for ($i=1; $i<=3; $i++) {
            DB::table('leads')->updateOrInsert(
                ['id' => 20+$i, 'company_id' => $cId],
                ['name' => "WA Lead $i", 'email' => "walead{$i}@test.com", 'phone' => "500000000{$i}", 'status' => 'new']
            );
        }

        DB::table('customers')->updateOrInsert(['id' => 6, 'company_id' => $cId], ['name' => 'Eve WA', 'email' => 'eve@wa.com', 'phone' => '4000000001', 'status' => 'active']);

        DB::table('quotations')->updateOrInsert(
            ['id' => 4, 'company_id' => $cId],
            ['quotation_no' => 'QT-C3-001', 'lead_id' => 21, 'customer_name' => 'Eve WA', 'package_id' => 6, 'sub_total' => 10000, 'final_amount' => 10000, 'status' => 'sent', 'created_by' => 10]
        );

        DB::table('bookings')->updateOrInsert(
            ['id' => 4, 'company_id' => $cId],
            ['booking_no' => 'BK-C3-001', 'lead_id' => 21, 'quotation_id' => 4, 'customer_id' => 6, 'package_id' => 6, 'total_amount' => 10000, 'paid_amount' => 0, 'due_amount' => 10000, 'status' => 'pending', 'created_by' => 10]
        );
    }

    private function seedCompany4(Carbon $now): void
    {
        $cId = 4;

        DB::table('package_categories')->updateOrInsert(['id' => 5, 'company_id' => $cId], ['name' => 'Budget', 'type' => 'domestic']);
        DB::table('destinations')->updateOrInsert(['id' => 5, 'company_id' => $cId], ['category_id' => 5, 'name' => 'Kerala']);
        
        DB::table('packages')->updateOrInsert(
            ['id' => 7, 'company_id' => $cId],
            ['name' => 'Munnar Quick Trip', 'destination_id' => 5, 'category_id' => 5, 'nights' => 1, 'days' => 2, 'price' => 5000, 'gst_applicable' => 0, 'status' => 'active']
        );

        for ($i=1; $i<=2; $i++) {
            DB::table('leads')->updateOrInsert(
                ['id' => 30+$i, 'company_id' => $cId],
                ['name' => "BTI Lead $i", 'email' => "btilead{$i}@test.com", 'phone' => "300000000{$i}", 'status' => 'new']
            );
        }

        DB::table('customers')->updateOrInsert(['id' => 7, 'company_id' => $cId], ['name' => 'Frank BTI', 'email' => 'frank@bti.com', 'phone' => '2000000001', 'status' => 'active']);
    }
}
