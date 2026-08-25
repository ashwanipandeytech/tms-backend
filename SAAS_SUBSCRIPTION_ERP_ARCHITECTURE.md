# SaaS Multi-Tenant Subscription ERP System Architecture

## Executive Summary

This document specifies the production-ready architectural design for transforming the CRM into an Enterprise SaaS Multi-Tenant Subscription ERP System. It covers hybrid multi-tenancy models, plan-based feature gating, pay-per-user add-on seats, 3-layer authorization mechanics, strict tenant data isolation, and detailed API specifications for website and frontend integration.

---

## 1. Multi-Tenancy Architecture (Hybrid Model)

To cater to both budget-conscious subscribers and high-security enterprise clients, the system supports a **Hybrid Multi-Tenancy Architecture**:

```
                              ┌─────────────────────────────────────────┐
                              │     Super Admin / Public API Gateway    │
                              └────────────────────┬────────────────────┘
                                                   │
                                   ┌───────────────┴───────────────┐
                                   ▼                               ▼
                      [ Shared Database Model ]         [ Dedicated Database Model ]
                      Standard & Professional Plans         Enterprise Plan (Custom DB)
                                   │                               │
                       ┌───────────┴───────────┐       ┌───────────┴───────────┐
                       │ MySQL: `crmtravel_db` │       │ MySQL: `tenant_102_db`│
                       │ (Row-Level Security)  │       │ (Isolated Schema)     │
                       └───────────────────────┘       └───────────────────────┘
```

### 1.1 Shared Database Multi-Tenancy (Row-Level Isolation)
- **Target Subscribers**: Starter & Professional Plan Subscribers.
- **Mechanism**: Every tenant table includes a non-nullable `company_id` foreign key.
- **Enforcement**: Laravel Eloquent Global Scope (`BelongsToTenantTrait`) automatically injects `WHERE company_id = auth()->user()->company_id` on every `SELECT`, `UPDATE`, and `DELETE` query.

### 1.2 Dedicated Database Multi-Tenancy (Schema Isolation)
- **Target Subscribers**: Enterprise Subscribers requiring strict database separation.
- **Mechanism**: Each enterprise tenant has an isolated database schema (e.g. `tenant_comp_105_db`).
- **Enforcement**: `TenantDatabaseSwitcher` middleware dynamically reconfigures Laravel's database connection (`config(['database.connections.tenant.database' => $dbName])`) upon authenticating the bearer token.

---

## 2. Subscription Plans, Gating Matrix & Pay-Per-User Add-on Seats

Super Admin creates and manages Subscription Plans. Each plan defines active feature modules, base included user seats, add-on seat pricing, and billing cycles.

### 2.1 Standard Subscription Plan Tiers

| Feature / Module | Starter Plan | Professional Plan | Enterprise Plan |
|---|:---:|:---:|:---:|
| **Leads & Follow-ups** | ✅ Included | ✅ Included | ✅ Included |
| **Tour Packages & Itineraries** | ❌ Disabled | ✅ Included | ✅ Included |
| **Hotels, Resorts & Villas** | ❌ Disabled | ✅ Included | ✅ Included |
| **Cab & Vendor Management** | ❌ Disabled | ✅ Included | ✅ Included |
| **Bookings Management** | ✅ Included | ✅ Included | ✅ Included |
| **Finance (Invoices & Expenses)** | ❌ Disabled | ❌ Disabled | ✅ Included |
| **Custom Webhooks & API Access** | ❌ Disabled | ✅ Included | ✅ Included |
| **Base User Seats Included** | **Up to 5 Users** | **Up to 15 Users** | **Unlimited / Custom** |
| **Pay-Per-User Add-on Seats** | ✅ Supported ($5 / user / mo) | ✅ Supported ($5 / user / mo) | ✅ Custom Bulk Seats |
| **Database Model** | Shared DB | Shared DB | Choice of Dedicated DB |

---

### 2.2 Pay-Per-User Add-on Seat Mechanics (Add-on Seats)

Subscribers can purchase extra user seats beyond their base plan limit without upgrading their entire subscription tier.

#### Effective Seat Formula
$$\text{Total Allowed Seats} = \text{Base Plan Included Seats} + \text{Purchased Add-on Seats}$$

#### Enforcement Logic (`POST /api/v1/users`)
When a Company Admin attempts to register a new staff user:
```php
$activeUsers = User::where('company_id', $company->id)->where('status', 'active')->count();
$allowedSeats = $company->subscriptionPlan->base_user_seats + $company->addon_user_seats;

if ($activeUsers >= $allowedSeats) {
    return response()->json([
        'success'    => false,
        'error_code' => 'USER_SEAT_LIMIT_REACHED',
        'message'    => "You have reached your seat limit of {$allowedSeats} users. Please purchase additional user add-on seats or upgrade your plan."
    ], 422);
}
```

---

## 3. 3-Layer Access Control & Effective Permission Engine

To calculate whether an API request is **ALLOWED** or **DENIED**, the system executes a 3-layer authorization stack:

$$\text{Effective Access} = \text{Layer 1 (Active Subscription)} \land \text{Layer 2 (Plan Feature Gate)} \land \text{Layer 3 (Role & Permission)}$$

```
[ Incoming Request ] ──► [ Layer 1: Subscription Active? ] ──► NO  ──► 402 Payment Required
                                    │ YES
                                    ▼
                         [ Layer 2: Plan Module Enabled? ] ──► NO  ──► 403 Feature Disabled
                                    │ YES
                                    ▼
                         [ Layer 3: User Role Granted? ]   ──► NO  ──► 403 Forbidden
                                    │ YES
                                    ▼
                            [ 200 OK ALLOWED ]
```

### 3.1 Effective Access Evaluation Matrix Examples

| Permission Key | Target Module | User Role Status | Tenant Plan Status | Subscription Status | Effective Result | HTTP Response |
|---|---|---|---|---|---|---|
| `inventory:read` | Inventory | Granted | Enabled (Professional) | Active | **ALLOWED** | `200 OK` |
| `inventory:write` | Inventory | Granted | Enabled (Professional) | Active | **ALLOWED** | `200 OK` |
| `finance:read` | Finance | Granted | Disabled (Professional) | Active | **DENIED** | `403 Feature Restricted` |
| `finance:export` | Finance | Granted | Enabled (Enterprise) | Expired | **DENIED** | `402 Payment Required` |
| `leads:delete` | Leads | Revoked | Enabled (Starter) | Active | **DENIED** | `403 Forbidden` |

---

## 4. Tenant Data Security & Isolation Guarantees

### 4.1 Cross-Tenant Leakage Protection
- **Compound Unique Constraints**: Email and phone unique constraints are scoped per company:
  ```sql
  ALTER TABLE users ADD UNIQUE KEY unique_company_user_email (company_id, email);
  ```
  *Result*: Company A and Company B can both register users with the email `admin@gmail.com` without any collisions or account cross-contamination.

### 4.2 Super Admin vs Subscriber Privilege Isolation
- **Subscriber Users**: Locked strictly to their assigned `company_id`. They cannot view, query, or modify data outside their company.
- **Super Admin**: Operates outside standard tenant global scopes to perform system-wide billing, onboarding, and platform analytics.

---

## 5. API Specification for Website & Subscription Onboarding

### 5.1 Super Admin: Setup Tenant Account with Add-on Seats

**URL**: `POST /api/v1/admin/tenants`  
**Method**: `POST`  
**Headers**: `Authorization: Bearer <super_admin_token>`  

#### Request Body Payload
```json
{
  "company_name": "Sunrise Travel Agency",
  "domain": "sunrisetravel.demohandler.in",
  "plan_id": 1,
  "billing_cycle": "yearly",
  "database_type": "shared",
  "addon_user_seats": 3,
  "admin_name": "Rajesh Kumar",
  "admin_email": "admin@sunrisetravel.com",
  "admin_phone": "9811122334",
  "initial_password": "Password@123"
}
```

#### Response (201 Created)
```json
{
  "success": true,
  "message": "Company subscription setup successfully",
  "data": {
    "company_id": 105,
    "company_name": "Sunrise Travel Agency",
    "domain": "sunrisetravel.demohandler.in",
    "plan": {
      "id": 1,
      "name": "Starter Plan",
      "base_user_seats": 5,
      "addon_user_seats": 3,
      "total_allowed_seats": 8,
      "modules": ["leads", "bookings"]
    },
    "subscription": {
      "status": "active",
      "starts_at": "2026-08-25",
      "ends_at": "2027-08-25"
    },
    "tenant_admin": {
      "id": 42,
      "email": "admin@sunrisetravel.com"
    }
  }
}
```

---

### 5.2 Super Admin: Purchase Add-on User Seats for Tenant

**URL**: `PUT /api/v1/admin/tenants/{id}/addon-seats`  
**Method**: `PUT`  
**Headers**: `Authorization: Bearer <super_admin_token>`  

#### Request Body Payload
```json
{
  "additional_seats": 2
}
```

#### Response (200 OK)
```json
{
  "success": true,
  "message": "2 add-on user seats added successfully",
  "data": {
    "company_id": 105,
    "base_user_seats": 5,
    "previous_addon_seats": 3,
    "new_addon_seats": 5,
    "total_allowed_seats": 10
  }
}
```

---

## 6. Suggestions & Architectural Recommendations for Best Execution

1. **Self-Service Seat Upgrade in Subscriber Panel**:
   - Allow Company Admin to click "Add User Seat" directly in their billing settings panel, which triggers an automated payment gateway flow and instantly increments `addon_user_seats`.
2. **Automated Tenant Onboarding Pipeline**:
   - When Super Admin sets up a company, automatically generate default roles (`Manager`, `Sales Executive`, `Operation Team`, `Accounts`) specifically for that company ID.
3. **Automated Email Notifications on Subscription Expiry**:
   - Send automated reminders 7 days and 1 day prior to `subscription_end_date`.
