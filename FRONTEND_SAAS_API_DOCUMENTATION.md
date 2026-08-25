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

## 3. Tenant Onboarding API (`/api/v1/admin/tenants`) `[NEW]`

### 3.1 Onboard / Register New Company Account
- **URL**: `POST /api/v1/admin/tenants`
- **Authentication**: **Public & Optional Bearer Token**
- **Automatic Default Role Seeding**: Upon onboarding, the backend automatically seeds default tenant roles (`Manager`, `Sales Executive`, `Operation Team`, `Accounts`) specifically for that company ID.

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

---

## 4. Tenant Role & Permission Management APIs `[NEW / UPDATED]`

### 4.1 List All Module Permissions Matrix (`GET /api/v1/permissions`)
Used by frontend UI to render checkbox matrices when creating or editing custom roles.

- **URL**: `GET /api/v1/permissions`
- **Headers**: `Authorization: Bearer <token>`

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Permissions retrieved successfully",
  "data": [
    {
      "module": "leads",
      "permissions": [
        { "id": 1, "action": "view", "description": "View Leads" },
        { "id": 2, "action": "create", "description": "Create Leads" },
        { "id": 3, "action": "edit", "description": "Edit Leads" },
        { "id": 4, "action": "delete", "description": "Delete Leads" }
      ]
    },
    {
      "module": "bookings",
      "permissions": [
        { "id": 5, "action": "view", "description": "View Bookings" },
        { "id": 6, "action": "create", "description": "Create Bookings" }
      ]
    }
  ]
}
```

---

### 4.2 Create Custom Role with Permissions (`POST /api/v1/roles`)
Allows Company Admin to create custom roles and assign specific permission IDs.

- **URL**: `POST /api/v1/roles`
- **Headers**: `Authorization: Bearer <token>`

#### Request Body Payload
```json
{
  "name": "Senior Sales Specialist",
  "description": "Custom role for senior sales reps",
  "permissions": [1, 2, 3, 5]
}
```

---

### 4.3 Update Role Permissions (`PUT /api/v1/roles/{id}`)
- **URL**: `PUT /api/v1/roles/{id}`
- **Headers**: `Authorization: Bearer <token>`

#### Request Body Payload
```json
{
  "name": "Senior Sales Specialist",
  "description": "Updated role description",
  "permissions": [1, 2, 3, 5, 6]
}
```

---

### 4.4 Create Staff User & Assign Role (`POST /api/v1/users`)
Company Admin registers internal staff members and assigns them a tenant `role_id`.

- **URL**: `POST /api/v1/users`
- **Headers**: `Authorization: Bearer <token>`

#### Request Body Payload
```json
{
  "name": "Amit Verma",
  "email": "amit@sunrisetravel.com",
  "phone": "9876543210",
  "role_id": 3,
  "password": "Password@123",
  "password_confirmation": "Password@123",
  "status": "active"
}
```

---

## 5. Error Code Reference

| Error Code | HTTP Status | Detail Description |
|---|---|---|
| `UNAUTHENTICATED` | 401 | Missing or invalid Sanctum Authorization Bearer Token header. |
| `FORBIDDEN` | 403 | User role lacks permission for the requested resource. |
| `PLAN_FEATURE_RESTRICTED` | 403 | Module feature not enabled under current subscription plan tier. |
| `SUBSCRIPTION_EXPIRED` | 402 | Company subscription has expired or been suspended. |
| `DEMO_PLAN_LIMIT_REACHED` | 422 | Free Trial plan is restricted to 1 entry per resource module. |
| `USER_SEAT_LIMIT_REACHED` | 422 | Active staff user count exceeds total allowed plan seats. |
| `VALIDATION_ERROR` | 422 | Required fields missing or failed data validation checks. |
| `RESOURCE_NOT_FOUND` | 404 | The requested record ID does not exist in the system. |
