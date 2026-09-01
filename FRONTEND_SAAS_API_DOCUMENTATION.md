# Frontend API Integration Documentation — SaaS Subscription ERP System

## Overview for Frontend Engineering Team

This document details **only** the new and modified API endpoints, error codes, payload structures, and header requirements introduced for the **SaaS Multi-Tenant Subscription ERP System**.

---

## 1. Authentication & Tenant Session (`POST /api/v1/login`) `[UPDATED]`

### Endpoints Details
- **URL**: `POST /api/v1/login`
- **Authentication**: Public (No Token Required)
- **Note**: `role_type` parameter is **optional**. The backend automatically detects the user's role and tenant company from their credentials.

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
      },
      "created_by": null,
      "created_by_type": null
    },
    "token": "1|sanctum_bearer_token_string_here"
  }
}
```

---

## 2. Super Admin Tenant Workspace Switching (`X-Tenant-ID` Header) `[NEW]`

When a Super Admin selects a specific tenant from the `/select-tenant` panel, the frontend should attach an **`X-Tenant-ID`** HTTP header to all outgoing API requests.

### Request Header Syntax
```http
Authorization: Bearer <super_admin_token>
X-Tenant-ID: 105
```

### Effect on Backend
- Automatically scopes all queries (`GET /leads`, `GET /bookings`, `GET /users`, etc.) to company ID `105`.
- Automatically sets `company_id = 105` on all new record creations (`POST /leads`, `POST /packages`, etc.).

---

## 3. Tenant Data Reset APIs (`DELETE /api/v1/admin/reset`) `[UPDATED]`

Clears/resets tenant test data (leads, bookings, packages, inventory, custom roles, staff) while **strictly preserving the primary Super Admin account**.

### 3.1 Single Tenant Data Reset Payload
- **URL**: `DELETE /api/v1/admin/reset`
- **Headers**: `Authorization: Bearer <token>`

```json
{
  "id": 105
}
```

### 3.2 Bulk Clear All Tenants Data Payload
- **URL**: `DELETE /api/v1/admin/reset`
- **Headers**: `Authorization: Bearer <token>`

```json
{
  "clear_all": true
}
```

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "All tenant data reset successfully across all companies. Primary Super Admin account preserved.",
  "data": {
    "clear_all": true,
    "company_id": null,
    "preserved_admin": "travel@demohandler.in",
    "status": "reset_completed"
  }
}
```

---

## 4. User Creator Tracking (`created_by` & `created_by_type`) `[NEW]`

When fetching or creating users (`GET /api/v1/users`, `POST /api/v1/users`), the `UserResource` includes creator details:

```json
{
  "id": 43,
  "name": "Amit Verma",
  "email": "amit@sunrisetravel.com",
  "role": {
    "id": 3,
    "name": "Sales Executive"
  },
  "created_by": {
    "id": 42,
    "name": "Rajesh Kumar",
    "email": "admin@sunrisetravel.com",
    "created_by_type": "tenant_admin"
  },
  "created_by_type": "tenant_admin"
}
```
