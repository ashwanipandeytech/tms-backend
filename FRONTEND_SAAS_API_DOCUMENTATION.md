# Frontend API Integration Documentation — SaaS Subscription ERP System

## Overview for Frontend Engineering Team

This document details **only** the new and modified API endpoints, error codes, payload structures, and header requirements introduced for the **SaaS Multi-Tenant Subscription ERP System**.

---

## 1. Authentication & Tenant Session (`POST /api/v1/login`) `[UPDATED]`

### Endpoints Details
- **URL**: `POST /api/v1/login`
- **Authentication**: Public (No Token Required)
- **Note**: `role_type` parameter is now **optional**. The backend automatically detects the user's role and tenant company from their credentials.

### Request Body Payload
```json
{
  "email": "travel@demohandler.in",
  "password": "Admin@123"
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
      "company_name": "Safar Musafir CRM",
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

---

## 3. Tenant Onboarding & Super Admin Management APIs (`/api/v1/admin/*`) `[NEW]`

### 3.1 Onboard / Register New Company Account
- **URL**: `POST /api/v1/admin/tenants`
- **Authentication**: **Public & Optional Bearer Token**

---

### 3.2 List All Registered Companies & Statistics (`GET /api/v1/admin/companies`) `[NEW]`
Dashboard list for Super Admin providing company statistics, subscription days remaining, employee counts, and nested staff details.

- **URL**: `GET /api/v1/admin/companies?page=1&per_page=15`
- **Headers**: `Authorization: Bearer <super_admin_token>`

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Company subscribers and statistics retrieved successfully",
  "data": [
    {
      "id": 105,
      "company_name": "Sunrise Travel Agency",
      "subdomain": "sunrisetravel",
      "status": "active",
      "created_at": "2026-08-25T12:00:00Z",
      "subscription": {
        "plan_name": "Starter Plan",
        "status": "active",
        "starts_at": "2026-08-25T12:00:00Z",
        "ends_at": "2026-09-25T12:00:00Z",
        "days_remaining": 31,
        "is_expiring_soon": false
      },
      "total_employees": 3,
      "total_allowed_seats": 5,
      "employees": [
        {
          "id": 42,
          "name": "Rajesh Kumar",
          "email": "admin@sunrisetravel.com",
          "status": "active",
          "role_name": "Manager"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

---

### 3.3 Purchase / Update Add-on User Seats (`PUT /api/v1/admin/tenants/{id}/addon-seats`)
- **URL**: `PUT /api/v1/admin/tenants/{id}/addon-seats`
- **Headers**: `Authorization: Bearer <super_admin_token>`

---

### 3.4 Bulk Reset Tenant Data (`DELETE /api/v1/admin/reset`) `[NEW]`
Clears/resets test data (leads, bookings, packages, inventory, custom roles, staff) while **strictly preserving the primary Super Admin account**.

- **URL**: `DELETE /api/v1/admin/reset`
- **Headers**: `Authorization: Bearer <token>`

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Tenant data reset successfully. Super Admin account preserved.",
  "data": {
    "company_id": 105,
    "preserved_admin": "admin@sunrisetravel.com",
    "status": "reset_completed"
  }
}
```

---

## 4. Tenant Role & Permission Management APIs `[UPDATED]`

- `GET /api/v1/roles` automatically filters out the `Super Admin` role.
- `GET /api/v1/users` automatically filters out Super Admin accounts when called by non-SuperAdmin users.
