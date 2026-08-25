<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\FollowUp;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadSource;
use App\Models\Package;
use App\Models\PackageCategory;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Resort;
use App\Models\ResortRoom;
use App\Models\Role;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Vendor;
use App\Models\Villa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Subscription Plans
        $starterPlan = SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter-plan'],
            [
                'name'             => 'Starter Plan',
                'monthly_price'    => 49.00,
                'yearly_price'     => 490.00,
                'base_user_seats'  => 5,
                'addon_seat_price' => 5.00,
                'modules'          => ['leads', 'followups', 'bookings'],
                'database_type'    => 'shared',
                'status'           => 'active',
            ]
        );

        $proPlan = SubscriptionPlan::updateOrCreate(
            ['slug' => 'professional-plan'],
            [
                'name'             => 'Professional Plan',
                'monthly_price'    => 99.00,
                'yearly_price'     => 990.00,
                'base_user_seats'  => 15,
                'addon_seat_price' => 5.00,
                'modules'          => ['leads', 'followups', 'packages', 'inventory', 'bookings'],
                'database_type'    => 'shared',
                'status'           => 'active',
            ]
        );

        $enterprisePlan = SubscriptionPlan::updateOrCreate(
            ['slug' => 'enterprise-plan'],
            [
                'name'             => 'Enterprise Plan',
                'monthly_price'    => 249.00,
                'yearly_price'     => 2490.00,
                'base_user_seats'  => 999,
                'addon_seat_price' => 0.00,
                'modules'          => ['leads', 'followups', 'packages', 'inventory', 'bookings', 'finance', 'reports'],
                'database_type'    => 'dedicated',
                'status'           => 'active',
            ]
        );

        // 1. Roles
        $roles = [
            ['id' => 1, 'name' => 'Super Admin', 'description' => 'Full access across system'],
            ['id' => 2, 'name' => 'Manager', 'description' => 'Manage leads, packages, bookings'],
            ['id' => 3, 'name' => 'Sales Executive', 'description' => 'Handle assigned leads & quotations'],
            ['id' => 4, 'name' => 'Operation Team', 'description' => 'Manage tour fulfillment, cabs, hotels'],
            ['id' => 5, 'name' => 'Accounts', 'description' => 'Finance, invoices, payments, expenses'],
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

        $allPermissions = Permission::pluck('id')->toArray();
        $superAdminRole = Role::find(1);
        if ($superAdminRole) {
            $superAdminRole->permissions()->sync($allPermissions);
        }

        // 3. User Credentials for All Roles
        $adminUser = User::updateOrCreate(
            ['email' => 'travel@demohandler.in'],
            [
                'role_id'  => 1,
                'name'     => 'Super Admin',
                'phone'    => '9999999999',
                'password' => Hash::make('Admin@123'),
                'status'   => 'active',
            ]
        );

        $managerUser = User::updateOrCreate(
            ['email' => 'manager@demohandler.in'],
            [
                'role_id'  => 2,
                'name'     => 'Manager User',
                'phone'    => '9888811111',
                'password' => Hash::make('Manager@123'),
                'status'   => 'active',
            ]
        );

        $salesUser = User::updateOrCreate(
            ['email' => 'sales@demohandler.in'],
            [
                'role_id'  => 3,
                'name'     => 'Sales Executive User',
                'phone'    => '9888822222',
                'password' => Hash::make('Sales@123'),
                'status'   => 'active',
            ]
        );

        $opsUser = User::updateOrCreate(
            ['email' => 'ops@demohandler.in'],
            [
                'role_id'  => 4,
                'name'     => 'Operations Lead User',
                'phone'    => '9888833333',
                'password' => Hash::make('Ops@123'),
                'status'   => 'active',
            ]
        );

        $accountsUser = User::updateOrCreate(
            ['email' => 'accounts@demohandler.in'],
            [
                'role_id'  => 5,
                'name'     => 'Accounts Manager User',
                'phone'    => '9888844444',
                'password' => Hash::make('Accounts@123'),
                'status'   => 'active',
            ]
        );

        // 4. Lead Sources
        $sources = ['Website', 'WhatsApp', 'Meta Facebook Ads', 'Google Ads', 'Manual', 'CSV Upload'];
        foreach ($sources as $source) {
            LeadSource::firstOrCreate(['name' => $source]);
        }
        $webSource = LeadSource::where('name', 'Website')->first();
        $metaSource = LeadSource::where('name', 'Meta Facebook Ads')->first();

        // 5. Package Categories
        $catDomestic = PackageCategory::firstOrCreate(['name' => 'Domestic'], ['type' => 'domestic']);
        $catInternational = PackageCategory::firstOrCreate(['name' => 'International'], ['type' => 'international']);

        // 6. Vehicle Types & Cabs
        $vSedan = VehicleType::firstOrCreate(['name' => 'Sedan']);
        $vSUV = VehicleType::firstOrCreate(['name' => 'SUV (Innova / Ertiga)']);
        $vTempo = VehicleType::firstOrCreate(['name' => 'Tempo Traveller 12 Seater']);

        $vendor = Vendor::firstOrCreate(
            ['email' => 'booking@royalcabs.com'],
            ['name' => 'Royal Cab Services', 'contact' => '9811122233', 'address' => 'Srinagar, J&K']
        );

        $vehicle = Vehicle::firstOrCreate(
            ['number_plate' => 'JK01AB1234'],
            ['vehicle_type_id' => $vSUV->id, 'vendor_id' => $vendor->id, 'model' => 'Toyota Innova Crysta', 'status' => 'available']
        );

        // 7. Inventory: Hotels, Resorts, Villas
        $hotel = Hotel::firstOrCreate(
            ['name' => 'Grand Palace Hotel Srinagar'],
            ['location' => 'Boulevard Road, Srinagar', 'rating' => 4.5, 'status' => 'active']
        );
        HotelRoom::firstOrCreate(
            ['hotel_id' => $hotel->id, 'room_type' => 'Deluxe Lake View'],
            ['meal_plan' => 'CP', 'price' => 4500.00]
        );

        $resort = Resort::firstOrCreate(
            ['name' => 'Pine Resort Gulmarg'],
            ['location' => 'Gulmarg', 'facilities' => 'Heated Pool, Ski Equipment, Spa', 'status' => 'active']
        );
        ResortRoom::firstOrCreate(
            ['resort_id' => $resort->id, 'room_type' => 'Luxury Suite'],
            ['season' => 'regular', 'price' => 8500.00]
        );

        $villa = Villa::firstOrCreate(
            ['name' => 'Sea Breeze Luxury Villa'],
            ['location' => 'Candolim, Goa', 'price' => 12000.00, 'capacity' => 6, 'bedrooms' => 3, 'amenities' => 'Private Pool, BBQ, Chef on call', 'status' => 'active']
        );

        // 8. Tour Packages
        $pkg1 = Package::firstOrCreate(
            ['name' => 'Magical Kashmir 6D/5N'],
            [
                'category_id' => $catDomestic->id,
                'nights'      => 5,
                'days'        => 6,
                'price'       => 35000.00,
                'inclusions'  => '4-Star Hotels, Breakfast & Dinner, Shikara Ride, Gulmarg Cable Car Ticket',
                'exclusions'  => 'Airfare, Personal Expenses, Mineral Water',
                'status'      => 'active',
            ]
        );

        $pkg2 = Package::firstOrCreate(
            ['name' => 'Enchanting Himachal 7D/6N'],
            [
                'category_id' => $catDomestic->id,
                'nights'      => 6,
                'days'        => 7,
                'price'       => 42000.00,
                'inclusions'  => 'Shimla & Manali Stay, Private Cab, Solang Valley Sightseeing',
                'exclusions'  => 'Adventure activities, Flight tickets',
                'status'      => 'active',
            ]
        );

        // 9. Dummy Customers
        $customer1 = Customer::firstOrCreate(
            ['email' => 'vikram.m@example.com'],
            ['name' => 'Vikram Malhotra', 'phone' => '9876500001', 'status' => 'active']
        );

        $customer2 = Customer::firstOrCreate(
            ['email' => 'priya.sharma@example.com'],
            ['name' => 'Priya Sharma', 'phone' => '9876500002', 'status' => 'active']
        );

        // 10. Dummy Leads & Lead Activities
        $lead1 = Lead::firstOrCreate(
            ['phone' => '9876500001'],
            [
                'name'            => 'Vikram Malhotra',
                'email'           => 'vikram.m@example.com',
                'source_id'       => $webSource->id,
                'campaign_source' => 'Website Popup',
                'destination'     => 'Kashmir',
                'travel_date'     => now()->addDays(20),
                'pax_adults'      => 2,
                'pax_children'    => 1,
                'budget'          => 75000.00,
                'status'          => 'confirmed',
                'assigned_to'     => $salesUser->id,
                'notes'           => 'Honeymoon couple, requested lake view room',
                'created_by'      => $adminUser->id,
            ]
        );

        $lead2 = Lead::firstOrCreate(
            ['phone' => '9876500002'],
            [
                'name'            => 'Priya Sharma',
                'email'           => 'priya.sharma@example.com',
                'source_id'       => $metaSource->id,
                'campaign_source' => 'Meta Facebook Ads',
                'destination'     => 'Himachal Pradesh',
                'travel_date'     => now()->addDays(35),
                'pax_adults'      => 4,
                'pax_children'    => 0,
                'budget'          => 120000.00,
                'status'          => 'interested',
                'assigned_to'     => $salesUser->id,
                'notes'           => 'Family tour, prefers tempo traveller',
                'created_by'      => $adminUser->id,
            ]
        );

        LeadActivity::firstOrCreate([
            'lead_id'       => $lead1->id,
            'activity_type' => 'enquiry',
            'description'   => 'Lead created via Website Form',
            'user_id'       => $adminUser->id,
        ]);

        FollowUp::firstOrCreate([
            'lead_id'        => $lead2->id,
            'assigned_to'    => $salesUser->id,
            'follow_up_date' => now()->addDays(2)->format('Y-m-d'),
            'type'           => 'call',
            'remarks'        => 'Send revised itinerary with Volvo bus option',
            'status'         => 'pending',
        ]);

        // 11. Quotation
        $quotation = Quotation::firstOrCreate(
            ['lead_id' => $lead1->id],
            [
                'quotation_no' => 'QT-2026-001',
                'package_id'   => $pkg1->id,
                'sub_total'    => 75000.00,
                'discount'     => 5000.00,
                'final_amount' => 70000.00,
                'status'       => 'accepted',
                'created_by'   => $salesUser->id,
            ]
        );

        QuotationItem::firstOrCreate([
            'quotation_id' => $quotation->id,
            'item_type'    => 'package',
            'description'  => 'Kashmir Super Tour 6D/5N Package',
            'qty'          => 1,
            'amount'       => 70000.00,
        ]);

        // 12. Booking & Operations Assignment
        $booking = Booking::firstOrCreate(
            ['lead_id' => $lead1->id],
            [
                'booking_no'    => 'BK-2026-1001',
                'customer_id'   => $customer1->id,
                'package_id'    => $pkg1->id,
                'quotation_id'  => $quotation->id,
                'operations_id' => $opsUser->id,
                'travel_date'   => now()->addDays(20),
                'total_amount'  => 70000.00,
                'paid_amount'   => 30000.00,
                'due_amount'    => 40000.00,
                'status'        => 'confirmed',
                'created_by'    => $salesUser->id,
            ]
        );

        // 13. Payments & Invoices
        Payment::firstOrCreate(
            ['txn_reference' => 'TXN99887766'],
            [
                'booking_id'   => $booking->id,
                'amount'       => 30000.00,
                'payment_type' => 'advance',
                'payment_mode' => 'upi',
                'paid_at'      => now(),
            ]
        );

        Invoice::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'invoice_no'  => 'INV-2026-001',
                'amount'      => 70000.00,
                'gst_amount'  => 3500.00,
                'total'       => 73500.00,
                'status'      => 'partial',
            ]
        );

        // 14. Expenses & Coupons
        Expense::firstOrCreate(
            ['description' => 'Hotel Advance Booking Payment for Srinagar Stay'],
            [
                'category'     => 'Hotel Booking',
                'amount'       => 15000.00,
                'expense_date' => now()->format('Y-m-d'),
                'created_by'   => $accountsUser->id,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'SUMMER2026'],
            [
                'type'       => 'percent',
                'value'      => 10.00,
                'status'     => 'active',
            ]
        );

        // 15. System Settings
        $settings = [
            ['setting_key' => 'company_name', 'setting_value' => 'Safar Musafir CRM'],
            ['setting_key' => 'gst_number', 'setting_value' => '07AAAAA0000A1Z5'],
            ['setting_key' => 'default_gst_percent', 'setting_value' => '5'],
            ['setting_key' => 'currency', 'setting_value' => 'INR'],
        ];
        foreach ($settings as $setting) {
            Setting::firstOrCreate(['setting_key' => $setting['setting_key']], $setting);
        }
    }
}
