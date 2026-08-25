# Frontend API Integration Documentation — SaaS Subscription ERP System

## Overview for Frontend Engineering Team

This document details **only** the new and modified API endpoints, error codes, payload structures, and header requirements introduced for the **SaaS Multi-Tenant Subscription ERP System**.

---

## 1. Authentication & Tenant Session (`POST /api/v1/login`) `[MODIFIED]`

### Endpoints Details
- **URL**: `POST /api/v1/login`
- **Authentication**: Public

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

### 2.1 List All Subscription Plans
- **URL**: `GET /api/v1/plans`
- **Headers**: `Authorization: Bearer <token>`

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Subscription plans retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Starter Plan",
      "slug": "starter-plan",
      "monthly_price": "49.00",
      "yearly_price": "490.00",
      "base_user_seats": 5,
      "addon_seat_price": "5.00",
      "modules": ["leads", "followups", "bookings"],
      "database_type": "shared",
      "status": "active"
    },
    {
      "id": 2,
      "name": "Professional Plan",
      "slug": "professional-plan",
      "monthly_price": "99.00",
      "yearly_price": "990.00",
      "base_user_seats": 15,
      "addon_seat_price": "5.00",
      "modules": ["leads", "followups", "packages", "inventory", "bookings"],
      "database_type": "shared",
      "status": "active"
    },
    {
      "id": 3,
      "name": "Enterprise Plan",
      "slug": "enterprise-plan",
      "monthly_price": "249.00",
      "yearly_price": "2490.00",
      "base_user_seats": 999,
      "addon_seat_price": "0.00",
      "modules": ["leads", "followups", "packages", "inventory", "bookings", "finance", "reports"],
      "database_type": "dedicated",
      "status": "active"
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

## 3. Super Admin Tenant Onboarding API (`/api/v1/admin/tenants`) `[NEW]`

### 3.1 Onboard New Subscriber Company Account
- **URL**: `POST /api/v1/admin/tenants`
- **Headers**: `Authorization: Bearer <super_admin_token>`

#### Request Body Payload
```json
{
  "company_name": "Sunrise Travel Agency",
  "subdomain": "sunrisetravel",
  "plan_id": 1,
  "billing_cycle": "monthly",
  "addon_user_seats": 3,
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
      "addon_user_seats": 3,
      "subscription_status": "active",
      "billing_cycle": "monthly",
      "subscription_starts_at": "2026-08-25T12:00:00Z",
      "subscription_ends_at": "2026-09-25T12:00:00Z",
      "database_type": "shared",
      "subscription_plan": {
        "id": 1,
        "name": "Starter Plan",
        "base_user_seats": 5
      }
    },
    "total_allowed_seats": 8,
    "tenant_admin": {
      "id": 42,
      "name": "Rajesh Kumar",
      "email": "admin@sunrisetravel.com"
    }
  }
}
```

---

### 3.2 List All Subscribers
- **URL**: `GET /api/v1/admin/tenants`
- **Headers**: `Authorization: Bearer <super_admin_token>`

#### Query Parameters
- `page` (integer, optional)
- `per_page` (integer, default: 15)

---

### 3.3 Purchase / Update Add-on User Seats
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

## 4. User Seat Limit Enforcement (`POST /api/v1/users`) `[MODIFIED]`

### Behavior
When a Company Admin attempts to add a new staff user (`POST /api/v1/users`), the system calculates:
$$\text{Current Active Users} \ge \text{Total Allowed Seats} (\text{Base Seats} + \text{Add-on Seats})$$

### Error Response (422 Unprocessable Entity)
If capacity is full, the API returns:
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

#### Frontend Action Required
Catch `error_code === 'USER_SEAT_LIMIT_REACHED'` and display the **Seat Limit Exceeded Modal Prompt** encouraging the Company Admin to purchase add-on seats or upgrade.

---

## 5. Subscription Status & Feature Gating Errors `[NEW]`

### 5.1 Expired Subscription (`402 Payment Required`)
If the subscriber's subscription date has passed or status is suspended:

```json
{
  "success": false,
  "error_code": "SUBSCRIPTION_EXPIRED",
  "message": "Your company subscription has expired or been suspended. Please renew your subscription to access API endpoints."
}
```
#### Frontend Action Required
Redirect Company Admin to Billing & Renewal screen (`/settings/billing`).

---

### 5.2 Plan Feature Restricted (`403 Forbidden`)
If a staff member or company admin attempts to call an endpoint (e.g. `GET /api/v1/invoices` or `GET /api/v1/hotels`) not included in their subscription plan:

```json
{
  "success": false,
  "error_code": "PLAN_FEATURE_RESTRICTED",
  "message": "The 'finance' module is not enabled under your company's current 'Starter Plan'. Please upgrade your plan to access this feature."
}
```
#### Frontend Action Required
Display a Upgrade Banner overlay on locked module navigation items.
