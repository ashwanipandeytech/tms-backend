# TMS Backend REST API Documentation

**Base URL**: `https://apisafarsystem.demohandler.in/public/api/v1`  
**Content-Type**: `application/json`  
**Accept**: `application/json`  

---

## 1. Authentication

### 1.1 User Login `[NEW]`

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

#### Request Parameters Description
| Parameter | Type | Required | Description |
|---|---|---|---|
| email | String | Yes | User account email address. |
| password | String | Yes | User account password. |
| role_type | String | No | Optional role selection name (`Super Admin`, `Sales Executive`, `Operation Team`, `Accounts`). |
| role_id | Integer | No | Optional assigned role ID. |

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "company_id": null,
      "name": "Super Admin",
      "email": "travel@demohandler.in",
      "phone": "9999999999",
      "avatar": null,
      "status": "active",
      "role": {
        "id": 1,
        "name": "Super Admin",
        "permissions": [
          "leads.view",
          "leads.create",
          "leads.edit",
          "leads.delete",
          "bookings.view"
        ]
      }
    },
    "token": "1|dAWlrrKKKOGw8j1pezVlSH1pFgy96cXRG0afFhgN5c62c9df"
  }
}
```

---

## 2. Dashboard & Analytics

### 2.1 Get Dashboard Metrics `[NEW]`

**URL**: `{{baseUrl}}/dashboard`  
**Method**: `GET`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Authorization | String | Required. `Bearer <token>` |
| Accept | String | Required. `application/json` |

---

## 3. Lead Management & RBAC Workflows

### 3.1 List Leads `[UPDATED]`

**URL**: `{{baseUrl}}/leads`  
**Method**: `GET`  

---

## 4. Bookings & Customer Management

### 4.1 List Customers `[FIXED]`

**URL**: `{{baseUrl}}/customers`  
**Method**: `GET`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Authorization | String | Required. `Bearer <token>` |
| Accept | String | Required. `application/json` |

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Customers retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Vikram Malhotra",
      "email": "vikram.m@example.com",
      "phone": "9876500001",
      "city": "Delhi",
      "status": "active",
      "created_at": "2026-08-19T14:48:00+00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1,
    "from": 1,
    "to": 1
  }
}
```

---

## 5. User & Staff Management (CRUD)

### 5.1 List Users `[FIXED]`

**URL**: `{{baseUrl}}/users`  
**Method**: `GET`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Authorization | String | Required. `Bearer <token>` |
| Accept | String | Required. `application/json` |

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Users retrieved successfully",
  "data": [
    {
      "id": 1,
      "company_id": null,
      "name": "Super Admin",
      "email": "travel@demohandler.in",
      "phone": "9999999999",
      "status": "active",
      "role": {
        "id": 1,
        "name": "Super Admin"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 5
  }
}
```

---

### 5.2 Add User `[NEW]`

**URL**: `{{baseUrl}}/users`  
**Method**: `POST`  
**Content-Type**: `application/json`  

#### Request Payload
```json
{
  "name": "Jane Doe",
  "email": "jane@demohandler.in",
  "phone": "9876543210",
  "password": "Password@123",
  "password_confirmation": "Password@123",
  "role_id": 3,
  "status": "active"
}
```

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 6,
    "name": "Jane Doe",
    "email": "jane@demohandler.in",
    "phone": "9876543210",
    "status": "active",
    "role": {
      "id": 3,
      "name": "Sales Executive"
    }
  }
}
```

---

### 5.3 Edit User `[NEW]`

**URL**: `{{baseUrl}}/users/{id}`  
**Method**: `PUT`  
**Content-Type**: `application/json`  

#### Request Payload *(Password is optional)*
```json
{
  "name": "Jane Doe Updated",
  "email": "jane@demohandler.in",
  "phone": "9876543211",
  "role_id": 3,
  "status": "inactive"
}
```

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 6,
    "name": "Jane Doe Updated",
    "email": "jane@demohandler.in",
    "phone": "9876543211",
    "status": "inactive"
  }
}
```

---

### 5.4 Delete User `[NEW]`

**URL**: `{{baseUrl}}/users/{id}`  
**Method**: `DELETE`  

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "User deleted successfully",
  "data": null
}
```

---

## 6. Response Error Codes Reference

| Error Code | HTTP Status | Detail Description |
|---|---|---|
| `UNAUTHENTICATED` | 401 | Missing or invalid Sanctum Authorization Bearer Token header. |
| `FORBIDDEN` | 403 | User role lacks permission for the requested resource. |
| `VALIDATION_ERROR` | 422 | Required fields missing or failed data validation checks. |
| `RESOURCE_NOT_FOUND` | 404 | The requested record ID does not exist in the system. |
