# Safar Travel CRM & SaaS ERP — Complete REST API Documentation

**Base URL**: `https://apisafarsystem.demohandler.in/public/api/v1`  
**Content-Type**: `application/json`  
**Accept**: `application/json`  

---

## 1. Authentication

### 1.1 User Login (`POST /api/v1/login`)
- **URL**: `{{baseUrl}}/login`
- **Method**: `POST`
- **Authentication**: Public

#### Request Body Payload
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
    "token": "1|sanctum_bearer_token_string"
  }
}
```

---

### 1.2 Get Logged-In User Profile (`GET /api/v1/me`)
- **URL**: `{{baseUrl}}/me`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`

---

### 1.3 Logout (`POST /api/v1/logout`)
- **URL**: `{{baseUrl}}/logout`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`

---

## 2. Subscription Plans Management (SaaS)

### 2.1 List All Subscription Plans (`GET /api/v1/plans`)
- **URL**: `{{baseUrl}}/plans`
- **Method**: `GET`
- **Authentication**: **Public & Optional Bearer Token**
  - *Public Call*: Returns all active subscription plans.
  - *Authenticated Call* (`Authorization: Bearer <token>`): Includes `"is_current_plan": true` on the active plan.

#### Response Structure (200 OK)
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

### 2.2 Create Subscription Plan (`POST /api/v1/plans`)
- **URL**: `{{baseUrl}}/plans`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <super_admin_token>`

---

## 3. Tenant Onboarding & Seat Management (SaaS)

### 3.1 Register Company Subscriber Account (`POST /api/v1/admin/tenants`)
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
  "addon_user_seats": 2,
  "database_type": "shared",
  "admin_name": "Rajesh Kumar",
  "admin_email": "admin@sunrisetravel.com",
  "admin_phone": "9811122334",
  "initial_password": "Password@123"
}
```

---

### 3.2 List All Subscribers (`GET /api/v1/admin/tenants`)
- **URL**: `{{baseUrl}}/admin/tenants?page=1&per_page=15`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <super_admin_token>`

---

### 3.3 Purchase / Update Add-on User Seats (`PUT /api/v1/admin/tenants/{id}/addon-seats`)
- **URL**: `{{baseUrl}}/admin/tenants/{id}/addon-seats`
- **Method**: `PUT`
- **Headers**: `Authorization: Bearer <super_admin_token>`

```json
{
  "addon_user_seats": 5
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

---

### 4.3 Create Custom Role with Permissions (`POST /api/v1/roles`)
- **URL**: `{{baseUrl}}/roles`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`

```json
{
  "name": "Senior Sales Specialist",
  "description": "Custom role for senior sales reps",
  "permissions": [1, 2, 3, 5]
}
```

---

### 4.4 Update Role & Sync Permissions (`PUT /api/v1/roles/{id}`)
- **URL**: `{{baseUrl}}/roles/{id}`
- **Method**: `PUT`
- **Headers**: `Authorization: Bearer <token>`

---

## 5. Staff & User Management (CRUD)

### 5.1 List Users (`GET /api/v1/users`)
- **URL**: `{{baseUrl}}/users?page=1&per_page=15`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`

---

### 5.2 Add Staff User (`POST /api/v1/users`)
- **URL**: `{{baseUrl}}/users`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`

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

## 6. Lead Management & Webhooks

### 6.1 List Leads (`GET /api/v1/leads`)
- **URL**: `{{baseUrl}}/leads?page=1&per_page=15&search=John&status=new`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`

---

### 6.2 Create Lead (`POST /api/v1/leads`)
- **URL**: `{{baseUrl}}/leads`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer <token>`

```json
{
  "name": "Rahul Sharma",
  "email": "rahul.sharma@example.com",
  "phone": "9876543210",
  "source_id": 1,
  "destination": "Himachal Pradesh",
  "travel_date": "2026-09-15",
  "pax_adults": 2,
  "pax_children": 1,
  "budget": 55000.00,
  "status": "new"
}
```

---

### 6.3 Assign Lead to Sales Executive (`PUT /api/v1/leads/{id}/assign`)
- **URL**: `{{baseUrl}}/leads/{id}/assign`
- **Method**: `PUT`
- **Headers**: `Authorization: Bearer <token>`

```json
{
  "assigned_to": 3
}
```

---

### 6.4 Lead Webhook Ingestion
- **Meta Facebook Ads**: `POST {{baseUrl}}/webhooks/leads/meta`
- **Website Form**: `POST {{baseUrl}}/webhooks/leads/website`

---

## 7. Follow-ups Management

- **List Follow-ups**: `GET {{baseUrl}}/follow-ups`
- **Create Follow-up**: `POST {{baseUrl}}/follow-ups`

---

## 8. Tour Packages & Itineraries

- **List Packages**: `GET {{baseUrl}}/packages`
- **Create Package**: `POST {{baseUrl}}/packages`

---

## 9. Inventory Management

- **Hotels**: `GET {{baseUrl}}/hotels` | `POST {{baseUrl}}/hotels`
- **Resorts**: `GET {{baseUrl}}/resorts` | `POST {{baseUrl}}/resorts`
- **Villas**: `GET {{baseUrl}}/villas` | `POST {{baseUrl}}/villas`

---

## 10. Cabs & Vendors Management

- **Cab Vendors**: `GET {{baseUrl}}/vendors` | `POST {{baseUrl}}/vendors`
- **Vehicles**: `GET {{baseUrl}}/vehicles` | `POST {{baseUrl}}/vehicles`
- **Cab Bookings**: `GET {{baseUrl}}/cab-bookings` | `POST {{baseUrl}}/cab-bookings`

---

## 11. Bookings Management & Operations Handoff

### 11.1 List Bookings (`GET /api/v1/bookings`)
- **URL**: `{{baseUrl}}/bookings?status=confirmed`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer <token>`

---

### 11.2 Assign Operations Staff (`PUT /api/v1/bookings/{id}/assign-operations`)
- **URL**: `{{baseUrl}}/bookings/{id}/assign-operations`
- **Method**: `PUT`
- **Headers**: `Authorization: Bearer <token>`

```json
{
  "operations_id": 4
}
```

---

## 12. Quotation Management

- **List Quotations**: `GET {{baseUrl}}/quotations`
- **Create Quotation**: `POST {{baseUrl}}/quotations`

---

## 13. Finance Management (Invoices, Payments, Expenses)

- **List Invoices**: `GET {{baseUrl}}/invoices` | `POST {{baseUrl}}/invoices`
- **Record Payment**: `POST {{baseUrl}}/payments`
- **Expenses**: `GET {{baseUrl}}/expenses` | `POST {{baseUrl}}/expenses`

---

## 14. Customer Directory

- **List Customers**: `GET {{baseUrl}}/customers`
- **Create Customer**: `POST {{baseUrl}}/customers`

---

## 15. Reports & Dashboard Metrics

- **Get Dashboard Metrics**: `GET {{baseUrl}}/dashboard`
- **Leads by Source Report**: `GET {{baseUrl}}/reports/leads-by-source`
- **Sales by Staff Report**: `GET {{baseUrl}}/reports/sales-by-staff`
- **Monthly Revenue Report**: `GET {{baseUrl}}/reports/monthly-revenue`

---

## 16. Error Codes & HTTP Status Reference

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
