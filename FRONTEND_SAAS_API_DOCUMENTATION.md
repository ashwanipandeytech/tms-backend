# Frontend API Integration Documentation — SaaS Subscription ERP System

## Overview for Frontend Engineering Team

This document details **only** the new and modified API endpoints, error codes, payload structures, and header requirements introduced for the **SaaS Multi-Tenant Subscription ERP System**.

---

## 1. Authentication & Tenant Session (`POST /api/v1/login`) `[MODIFIED]`

### Endpoints Details
- **URL**: `POST /api/v1/login`
- **Authentication**: Public (No Token Required)

### Request Body Payload
```json
{
  "email": "travel@demohandler.in",
  "password": "Admin@123",
  "role_type": "Super Admin"
}
```

### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Super Admin",
      "email": "travel@demohandler.in",
      "company_id": 1,
      "role": {
        "id": 1,
        "name": "Super Admin",
        "permissions": ["leads.view", "leads.create", "bookings.view", "finance.view"]
      },
      "company": {
        "id": 1,
        "name": "Safar Musafir CRM",
        "subdomain": "safarmusafir",
        "subscription_status": "active",
        "total_allowed_seats": 999
      }
    },
    "token": "1|sanctum_bearer_token_string_here"
  }
}
```

---

## 2. Subscription Plans Management API (`/api/v1/plans`) `[NEW]`

### 2.1 List All Subscription Plans (Public Pricing Page & Authenticated Dashboard)
- **URL**: `GET /api/v1/plans`
- **Authentication**: **Public & Optional Bearer Token**
  - *Public Call*: Returns all active subscription plans.
  - *Authenticated Call* (`Authorization: Bearer <token>`): Includes `"is_current_plan": true` on the subscriber's active plan.

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Subscription plans retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Free Trial Plan",
      "slug": "free-trial-plan",
      "monthly_price": "0.00",
      "yearly_price": "0.00",
      "base_user_seats": 1,
      "addon_seat_price": "0.00",
      "modules": ["leads", "followups", "packages", "inventory", "bookings", "finance", "reports"],
      "database_type": "shared",
      "status": "active",
      "is_current_plan": true
    },
    {
      "id": 2,
      "name": "Starter Plan",
      "slug": "starter-plan",
      "monthly_price": "49.00",
      "yearly_price": "490.00",
      "base_user_seats": 5,
      "addon_seat_price": "5.00",
      "modules": ["leads", "followups", "bookings"],
      "database_type": "shared",
      "status": "active",
      "is_current_plan": false
    },
    {
      "id": 3,
      "name": "Professional Plan",
      "slug": "professional-plan",
      "monthly_price": "99.00",
      "yearly_price": "990.00",
      "base_user_seats": 15,
      "addon_seat_price": "5.00",
      "modules": ["leads", "followups", "packages", "inventory", "bookings"],
      "database_type": "shared",
      "status": "active",
      "is_current_plan": false
    },
    {
      "id": 4,
      "name": "Enterprise Plan",
      "slug": "enterprise-plan",
      "monthly_price": "249.00",
      "yearly_price": "2490.00",
      "base_user_seats": 999,
      "addon_seat_price": "0.00",
      "modules": ["leads", "followups", "packages", "inventory", "bookings", "finance", "reports"],
      "database_type": "dedicated",
      "status": "active",
      "is_current_plan": false
    }
  ]
}
```

---

### 2.2 Create Subscription Plan (Super Admin)
- **URL**: `POST /api/v1/plans`
- **Headers**: `Authorization: Bearer <super_admin_token>`

#### Request Body Payload
```json
{
  "name": "Custom Agency Plan",
  "monthly_price": 79.00,
  "yearly_price": 790.00,
  "base_user_seats": 10,
  "addon_seat_price": 5.00,
  "modules": ["leads", "followups", "packages", "bookings"],
  "database_type": "shared",
  "status": "active"
}
```

---

## 3. Tenant Onboarding API (`/api/v1/admin/tenants`) `[NEW]`

### 3.1 Onboard / Register New Company Account (Self-Service Website & Super Admin)
- **URL**: `POST /api/v1/admin/tenants`
- **Authentication**: **Public & Optional Bearer Token** (No Token required for public website signups; Super Admin token accepted if logged in).

#### Request Body Payload
```json
{
  "company_name": "Sunrise Travel Agency",
  "subdomain": "sunrisetravel",
  "plan_id": 1,
  "billing_cycle": "monthly",
  "addon_user_seats": 0,
  "database_type": "shared",
  "admin_name": "Rajesh Kumar",
  "admin_email": "admin@sunrisetravel.com",
  "admin_phone": "9811122334",
  "initial_password": "Password@123"
}
```

#### Success Response (201 Created)
```json
{
  "success": true,
  "message": "Company subscription account set up successfully",
  "data": {
    "company": {
      "id": 105,
      "name": "Sunrise Travel Agency",
      "subdomain": "sunrisetravel",
      "plan_id": 1,
      "addon_user_seats": 0,
      "subscription_status": "active",
      "billing_cycle": "monthly",
      "subscription_starts_at": "2026-08-25T12:00:00Z",
      "subscription_ends_at": "2026-09-25T12:00:00Z",
      "database_type": "shared",
      "subscription_plan": {
        "id": 1,
        "name": "Free Trial Plan",
        "base_user_seats": 1
      }
    },
    "total_allowed_seats": 1,
    "tenant_admin": {
      "id": 42,
      "name": "Rajesh Kumar",
      "email": "admin@sunrisetravel.com"
    }
  }
}
```

---

### 3.2 List All Subscribers (Super Admin Only)
- **URL**: `GET /api/v1/admin/tenants`
- **Headers**: `Authorization: Bearer <super_admin_token>`

#### Query Parameters
- `page` (integer, optional)
- `per_page` (integer, default: 15)

---

### 3.3 Purchase / Update Add-on User Seats (Super Admin Only)
- **URL**: `PUT /api/v1/admin/tenants/{id}/addon-seats`
- **Headers**: `Authorization: Bearer <super_admin_token>`

#### Request Body Payload
```json
{
  "addon_user_seats": 5
}
```

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Add-on user seats updated successfully",
  "data": {
    "company_id": 105,
    "company_name": "Sunrise Travel Agency",
    "base_user_seats": 5,
    "addon_user_seats": 5,
    "total_allowed_seats": 10
  }
}
```

---

## 4. Resource Creation Limit Errors `[NEW]`

### 4.1 Free Trial / Demo Plan 1-Entry Limit (`422 Unprocessable Entity`)
Subscribers under the **Free Trial Plan** can view, edit, and explore all modules, but are restricted to creating a maximum of **1 entry per module** (1 lead, 1 booking, 1 hotel, etc.).

When creating a 2nd entry in any module, the API returns:
```json
{
  "success": false,
  "error_code": "DEMO_PLAN_LIMIT_REACHED",
  "message": "The Free Trial plan is restricted to 1 entry per module. Please upgrade your subscription plan to create additional leads.",
  "errors": {
    "demo_limit": [
      "Free Trial limit of 1 entry reached for module 'leads'."
    ]
  }
}
```

#### Frontend Action Required
Catch `error_code === 'DEMO_PLAN_LIMIT_REACHED'` and open an Upgrade Modal prompting the user to upgrade to Starter or Professional Plan.

---

### 4.2 User Seat Limit Enforcement (`422 Unprocessable Entity`)
When a Company Admin attempts to add a new staff user (`POST /api/v1/users`), if active staff count exceeds allowed seats:

```json
{
  "success": false,
  "error_code": "USER_SEAT_LIMIT_REACHED",
  "message": "User seat limit reached (8 max seats). Please purchase add-on seats or upgrade your subscription plan.",
  "errors": {
    "seat_limit": [
      "Maximum allowed user seats (8) reached."
    ]
  }
}
```

---

## 5. Subscription Status & Feature Gating Errors

### 5.1 Expired Subscription (`402 Payment Required`)
If the subscriber's subscription date has passed or status is suspended:

```json
{
  "success": false,
  "error_code": "SUBSCRIPTION_EXPIRED",
  "message": "Your company subscription has expired or been suspended. Please renew your subscription to access API endpoints."
}
```

---

### 5.2 Plan Feature Restricted (`403 Forbidden`)
If a subscriber attempts to call an endpoint not included in their subscription plan:

```json
{
  "success": false,
  "error_code": "PLAN_FEATURE_RESTRICTED",
  "message": "The 'finance' module is not enabled under your company's current 'Starter Plan'. Please upgrade your plan to access this feature."
}
```
