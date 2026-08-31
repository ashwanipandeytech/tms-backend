# Safar Travel CRM & SaaS ERP — Complete REST API Documentation

**Base URL**: `https://apisafarsystem.demohandler.in/public/api/v1`  
**Content-Type**: `application/json`  
**Accept**: `application/json`  

---

## 1. Authentication & Session

### 1.1 User Login (`POST /api/v1/login`)
- **URL**: `{{baseUrl}}/login`
- **Method**: `POST`
- **Authentication**: Public
- **Note**: `role_type` parameter is **optional**. The backend automatically detects the user's role and company context based on credentials.

#### Headers
| Header | Type | Description |
|---|---|---|
| Content-Type | String | Required. `application/json` |
| Accept | String | Required. `application/json` |

#### Request Body Payload
```json
{
  "email": "travel@demohandler.in",
  "password": "Admin@123"
}
```

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Super Admin",
      "email": "travel@demohandler.in",
      "phone": "9999999999",
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

### 1.2 Get Current User Details (`GET /api/v1/me`)
- **URL**: `{{baseUrl}}/me`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Current user details",
  "data": {
    "id": 1,
    "company_id": 1,
    "company_name": "Safar Musafir CRM",
    "name": "Super Admin",
    "email": "travel@demohandler.in",
    "phone": "9999999999",
    "role": {
      "id": 1,
      "name": "Super Admin",
      "permissions": ["leads.view", "leads.create", "bookings.view"]
    },
    "company": {
      "id": 1,
      "name": "Safar Musafir CRM",
      "subdomain": "safarmusafir",
      "subscription_status": "active",
      "total_allowed_seats": 999
    }
  }
}
```

---

## 2. Subscription Plans Management (SaaS)

### 2.1 List All Subscription Plans (`GET /api/v1/plans`)
- **URL**: `{{baseUrl}}/plans`
- **Method**: `GET`
- **Authentication**: **Public & Optional Bearer Token**

---

## 3. Tenant Onboarding & Super Admin Management APIs (`/api/v1/admin/*`)

### 3.1 Register Company Subscriber Account (`POST /api/v1/admin/tenants`)
- **URL**: `{{baseUrl}}/admin/tenants`
- **Method**: `POST`
- **Authentication**: **Public & Optional Bearer Token**

---

### 3.2 List All Registered Companies & Statistics (`GET /api/v1/admin/companies`) `[NEW]`
Super Admin dashboard list returning company statistics, subscription status, days until expiration, total employee counts, and nested staff details.

- **URL**: `{{baseUrl}}/admin/companies?page=1&per_page=15`
- **Method**: `GET`
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
- **URL**: `{{baseUrl}}/admin/tenants/{id}/addon-seats`
- **Method**: `PUT`
- **Headers**: `Authorization: Bearer <super_admin_token>`

---

### 3.4 Bulk Clear / Reset Tenant Data (`DELETE /api/v1/admin/reset`) `[NEW]`
Clears/resets tenant test data (leads, bookings, packages, inventory, custom roles, staff) while **strictly preserving the primary Super Admin account**.

- **URL**: `{{baseUrl}}/admin/reset`
- **Method**: `DELETE`
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

## 4. Roles & Permissions Management (RBAC)

### 4.1 List Module Permissions Matrix (`GET /api/v1/permissions`)
- **URL**: `{{baseUrl}}/permissions`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`

---

### 4.2 List Tenant Roles (`GET /api/v1/roles`)
- **URL**: `{{baseUrl}}/roles`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Note**: The `Super Admin` role is automatically filtered out from this list so it cannot be modified or assigned to staff users.

---

## 5. Staff & User Management

### 5.1 List Users (`GET /api/v1/users`)
- **URL**: `{{baseUrl}}/users?page=1&per_page=15`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`
- **Note**: Super Admin user accounts are automatically hidden from regular non-SuperAdmin callers.

---

## 6. Global Error Codes Reference

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
