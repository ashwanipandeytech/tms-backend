# TMS Backend REST API Documentation

**Base URL**: `https://apisafarsystem.demohandler.in/public/api/v1`  
**Content-Type**: `application/json`  
**Accept**: `application/json`  

---

## 1. Authentication

### 1.1 User Login `[UPDATED]`

**URL**: `{{baseUrl}}/login`  
**Method**: `POST`  
**Content-Type**: `application/json`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Content-Type | String | Required. Must be set to `application/json`. |
| Accept | String | Required. Must be set to `application/json`. |

#### Request Payload (Example Super Admin Login)
```json
{
  "email": "travel@demohandler.in",
  "password": "Admin@123",
  "role_type": "Super Admin"
}
```

#### Pre-seeded Staging Credentials for Testing
| Role Name | Email Address | Password | Role ID |
|---|---|---|---|
| Super Admin | `travel@demohandler.in` | `Admin@123` | 1 |
| Manager | `manager@demohandler.in` | `Manager@123` | 2 |
| Sales Executive | `sales@demohandler.in` | `Sales@123` | 3 |
| Operation Team | `ops@demohandler.in` | `Ops@123` | 4 |
| Accounts | `accounts@demohandler.in` | `Accounts@123` | 5 |

---

## 2. Subscription Plans Management API (`/api/v1/plans`) `[NEW]`

### 2.1 List All Subscription Plans (Public & Authenticated)
- **URL**: `{{baseUrl}}/plans`
- **Method**: `GET`
- **Authentication**: **Public & Optional Bearer Token**
  - *Public Call*: Returns all active subscription plans.
  - *Authenticated Call* (`Authorization: Bearer <token>`): Includes `"is_current_plan": true` on the subscriber's active plan.

#### Response Structure (Success)
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

### 3.1 Register Company Account (Public Website & Super Admin)
- **URL**: `{{baseUrl}}/admin/tenants`
- **Method**: `POST`
- **Authentication**: **Public & Optional Bearer Token**

#### Request Payload
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

### 4.1 List Module Permissions Matrix (`GET /api/v1/permissions`)
- **URL**: `{{baseUrl}}/permissions`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`

#### Response Structure (Success)
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
    }
  ]
}
```

---

### 4.2 Create Custom Role (`POST /api/v1/roles`)
- **URL**: `{{baseUrl}}/roles`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`

#### Request Payload
```json
{
  "name": "Senior Sales Specialist",
  "description": "Custom role for senior sales reps",
  "permissions": [1, 2, 3, 5]
}
```

---

### 4.3 Update Custom Role (`PUT /api/v1/roles/{id}`)
- **URL**: `{{baseUrl}}/roles/{id}`
- **Method**: `PUT`
- **Headers**: `Authorization: Bearer <token>`

#### Request Payload
```json
{
  "name": "Senior Sales Specialist",
  "description": "Updated role description",
  "permissions": [1, 2, 3, 5, 6]
}
```

---

### 4.4 Create Staff User & Assign Role (`POST /api/v1/users`)
- **URL**: `{{baseUrl}}/users`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`

#### Request Payload
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

## 5. Response Error Codes Reference `[UPDATED]`

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
