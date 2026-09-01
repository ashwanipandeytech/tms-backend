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

## 2. Super Admin Tenant Workspace Switching (`X-Tenant-ID` Header) `[UPDATED]`

When a Super Admin selects a specific tenant from the `/select-tenant` panel, the frontend should attach an **`X-Tenant-ID`** HTTP header to all outgoing API requests.

### Request Header Syntax
```http
Authorization: Bearer <super_admin_token>
X-Tenant-ID: 105
```

### Effect on Backend & Global Exclusions
- **Tenant-Specific APIs** (`/leads`, `/bookings`, `/packages`, `/inventory`, `/finance`, `/users`): Scopes all queries and creations to `company_id = 105`.
- **Global Resource APIs** (`/roles`, `/permissions`, `/plans`, `/admin/companies`): Explicitly **ignore** the `X-Tenant-ID` header and return global platform resources.

---

## 3. Universal Roles & Permissions Management `[NEW]`

- **Global Availability**: Roles and Permissions are universal and shared across all tenants.
- **Super Admin Only**: Creating, updating, or deleting roles (`POST /api/v1/roles`, `PUT /api/v1/roles/{id}`, `DELETE /api/v1/roles/{id}`) is strictly restricted to Super Admin. Non-SuperAdmin requests return `403 Forbidden` (`FORBIDDEN`).
- **Reset Preservation**: Performing tenant resets (`DELETE /api/v1/admin/reset`) **never** deletes or modifies roles or permissions.

---

## 4. Tenant Data Reset APIs (`DELETE /api/v1/admin/reset`) `[UPDATED]`

### 4.1 Single Tenant Reset Payload
```json
{
  "id": 105
}
```

### 4.2 Bulk Clear All Tenants Payload
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
