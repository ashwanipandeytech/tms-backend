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
      "email": "admin@safarmusafir.com",
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

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Dashboard metrics retrieved successfully",
  "data": {
    "kpis": {
      "total_leads": 120,
      "new_enquiries": 15,
      "followups_today": 8,
      "confirmed": 24,
      "revenue": 450000.00,
      "pending_pay": 120000.00
    },
    "funnel": {
      "new": 15,
      "contacted": 30,
      "interested": 25,
      "confirmed": 24
    },
    "upcoming_departures": [
      {
        "id": 1,
        "booking_no": "BK-66C1E2A",
        "travel_date": "2026-09-01",
        "package": {
          "id": 1,
          "name": "Kashmir Super Tour"
        }
      }
    ]
  }
}
```

---

## 3. Lead Management & RBAC Workflows

### 3.1 List Leads `[UPDATED]`

**URL**: `{{baseUrl}}/leads`  
**Method**: `GET`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Authorization | String | Required. `Bearer <token>` |
| Accept | String | Required. `application/json` |

#### Query Parameters
| Parameter | Type | Required | Description |
|---|---|---|---|
| page | Integer | No | Page number for pagination (Default: `1`). |
| per_page | Integer | No | Number of records per page (Default: `15`). |
| search | String | No | Search string matching name, email, phone, or destination. |
| status | String | No | Filter by lead status (`new`, `contacted`, `interested`, `confirmed`, `lost`). |

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Leads retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Rahul Sharma",
      "email": "rahul@example.com",
      "phone": "9876543210",
      "destination": "Himachal Pradesh",
      "travel_date": "2026-09-15",
      "pax_adults": 2,
      "pax_children": 1,
      "budget": 55000.00,
      "status": "new",
      "assigned_to": 1,
      "campaign_source": "Meta Facebook Ads",
      "notes": "Prefers luxury hotel with mountain view",
      "created_at": "2026-08-18T10:00:00+00:00"
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

### 3.2 Assign Lead to Sales Executive `[NEW]`

**URL**: `{{baseUrl}}/leads/{id}/assign`  
**Method**: `PUT`  
**Content-Type**: `application/json`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Authorization | String | Required. `Bearer <token>` |
| Content-Type | String | Required. `application/json` |

#### Request Payload
```json
{
  "assigned_to": 5
}
```

#### Request Parameters Description
| Parameter | Type | Required | Description |
|---|---|---|---|
| assigned_to | Integer | Yes | User ID of the assigned Sales Executive. |

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Lead assigned successfully",
  "data": {
    "id": 1,
    "name": "Rahul Sharma",
    "assigned_to": 5,
    "created_at": "2026-08-18T10:00:00+00:00"
  }
}
```

---

### 3.3 CSV Lead Bulk Import `[NEW]`

**URL**: `{{baseUrl}}/leads/import`  
**Method**: `POST`  
**Content-Type**: `multipart/form-data`  

#### Request Payload
- **file**: CSV File (`name, phone, email, destination`)

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "CSV Import completed: 45 leads imported, 3 duplicate leads skipped",
  "data": {
    "imported_count": 45,
    "skipped_count": 3
  }
}
```

---

### 3.4 Ingest Lead via Meta Webhook `[NEW]`

**URL**: `{{baseUrl}}/webhooks/leads/meta`  
**Method**: `POST`  
**Content-Type**: `application/json`  

#### Request Payload
```json
{
  "name": "Ankit Kumar",
  "phone": "9812345678",
  "email": "ankit@example.com",
  "destination": "Goa",
  "campaign_source": "Meta Facebook Ads"
}
```

---

## 4. Bookings Management & Operations Handoff

### 4.1 Assign Operations Staff to Booking `[NEW]`

**URL**: `{{baseUrl}}/bookings/{id}/assign-operations`  
**Method**: `PUT`  
**Content-Type**: `application/json`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Authorization | String | Required. `Bearer <token>` |
| Content-Type | String | Required. `application/json` |

#### Request Payload
```json
{
  "operations_id": 8
}
```

#### Request Parameters Description
| Parameter | Type | Required | Description |
|---|---|---|---|
| operations_id | Integer | Yes | User ID of assigned Operations fulfillment staff. |

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Operations fulfillment assigned successfully",
  "data": {
    "id": 1,
    "booking_no": "BK-66C1E2A",
    "operations_id": 8,
    "status": "confirmed"
  }
}
```

---

## 5. Response Error Codes Reference

| Error Code | HTTP Status | Detail Description |
|---|---|---|
| `UNAUTHENTICATED` | 401 | Missing or invalid Sanctum Authorization Bearer Token header. |
| `FORBIDDEN` | 403 | User role lacks permission for the requested resource (e.g. Sales staff accessing unassigned lead). |
| `VALIDATION_ERROR` | 422 | Required fields missing or failed data validation checks. |
| `RESOURCE_NOT_FOUND` | 404 | The requested record ID does not exist in the system. |
