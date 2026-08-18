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

#### Request Payload
```json
{
  "email": "admin@safarmusafir.com",
  "password": "Admin@123"
}
```

#### Request Parameters Description
| Parameter | Type | Required | Description |
|---|---|---|---|
| email | String | Yes | User account email address. |
| password | String | Yes | User account password. |

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

#### Response Parameters Description
| Parameter | Type | Description |
|---|---|---|
| success | Boolean | Returns true if user authentication succeeded. |
| message | String | System feedback message (`Login successful`). |
| data.user | Object | The authenticated user details object with assigned role and permissions. |
| data.token | String | Sanctum Bearer token for authenticating subsequent request headers. |

#### Response Structure (Error / Validation Failure)
```json
{
  "message": "Invalid email credentials or inactive account.",
  "errors": {
    "email": [
      "Invalid email credentials or inactive account."
    ]
  }
}
```

---

### 1.2 Get Authenticated User Details `[NEW]`

**URL**: `{{baseUrl}}/me`  
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
  "message": "User details retrieved",
  "data": {
    "id": 1,
    "name": "Super Admin",
    "email": "admin@safarmusafir.com",
    "role": {
      "id": 1,
      "name": "Super Admin",
      "permissions": ["leads.view", "leads.create"]
    }
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

#### Response Parameters Description
| Parameter | Type | Description |
|---|---|---|
| success | Boolean | Returns true if dashboard data was successfully calculated. |
| message | String | System feedback message. |
| data.kpis | Object | Summary metric values (`total_leads`, `confirmed`, `revenue`, `pending_pay`). |
| data.funnel | Object | Stage breakdown of active lead funnel. |
| data.upcoming_departures | Array | List of upcoming confirmed travel bookings. |

---

## 3. Lead Management

### 3.1 List Leads `[NEW]`

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

### 3.2 Create Lead `[NEW]`

**URL**: `{{baseUrl}}/leads`  
**Method**: `POST`  
**Content-Type**: `application/json`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Authorization | String | Required. `Bearer <token>` |
| Content-Type | String | Required. Must be set to `application/json`. |
| Accept | String | Required. Must be set to `application/json`. |

#### Request Payload
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
  "status": "new",
  "assigned_to": 1,
  "notes": "Prefers luxury hotel with mountain view"
}
```

#### Request Parameters Description
| Parameter | Type | Required | Description |
|---|---|---|---|
| name | String | Yes | Lead full name. |
| phone | String | Yes | Primary contact phone number. |
| email | String | No | Lead email address. |
| source_id | Integer | No | Lead source ID. |
| destination | String | No | Travel destination city or state. |
| travel_date | String | No | Expected travel date (`YYYY-MM-DD`). |
| pax_adults | Integer | No | Number of adult travellers. |
| pax_children | Integer | No | Number of child travellers. |
| budget | Number | No | Estimated travel budget amount. |
| status | String | No | Lead status (`new`, `contacted`, `interested`, `confirmed`). |
| assigned_to | Integer | No | User ID of assigned sales staff. |
| notes | String | No | Internal remarks or requirements. |

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Lead created successfully",
  "data": {
    "id": 1,
    "name": "Rahul Sharma",
    "email": "rahul.sharma@example.com",
    "phone": "9876543210",
    "destination": "Himachal Pradesh",
    "travel_date": "2026-09-15",
    "pax_adults": 2,
    "pax_children": 1,
    "budget": 55000.00,
    "status": "new",
    "assigned_to": 1,
    "notes": "Prefers luxury hotel with mountain view",
    "created_at": "2026-08-18T10:00:00+00:00"
  }
}
```

---

## 4. Bookings Management

### 4.1 Create Booking `[NEW]`

**URL**: `{{baseUrl}}/bookings`  
**Method**: `POST`  
**Content-Type**: `application/json`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Authorization | String | Required. `Bearer <token>` |
| Content-Type | String | Required. Must be set to `application/json`. |

#### Request Payload
```json
{
  "lead_id": 1,
  "customer_id": 1,
  "package_id": 1,
  "travel_date": "2026-10-01",
  "total_amount": 75000.00,
  "paid_amount": 25000.00,
  "status": "confirmed"
}
```

#### Request Parameters Description
| Parameter | Type | Required | Description |
|---|---|---|---|
| lead_id | Integer | No | Associated lead ID. |
| customer_id | Integer | No | Associated customer ID. |
| package_id | Integer | No | Booked tour package ID. |
| travel_date | String | No | Departure travel date (`YYYY-MM-DD`). |
| total_amount | Number | Yes | Total booking package cost. |
| paid_amount | Number | No | Total amount paid to date. |
| status | String | No | Booking status (`pending`, `confirmed`, `cancelled`). |

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Booking created successfully",
  "data": {
    "id": 1,
    "booking_no": "BK-66C1E2A",
    "total_amount": 75000.00,
    "paid_amount": 25000.00,
    "due_amount": 50000.00,
    "status": "confirmed",
    "created_at": "2026-08-18T10:00:00+00:00"
  }
}
```

---

## 5. Payments Management

### 5.1 Record Payment `[NEW]`

**URL**: `{{baseUrl}}/payments`  
**Method**: `POST`  
**Content-Type**: `application/json`  

#### Headers
| Header | Type | Description |
|---|---|---|
| Authorization | String | Required. `Bearer <token>` |
| Content-Type | String | Required. Must be set to `application/json`. |

#### Request Payload
```json
{
  "booking_id": 1,
  "amount": 25000.00,
  "payment_type": "advance",
  "payment_mode": "upi",
  "txn_reference": "TXN987654321",
  "paid_at": "2026-08-18 12:00:00"
}
```

#### Request Parameters Description
| Parameter | Type | Required | Description |
|---|---|---|---|
| booking_id | Integer | Yes | Associated booking ID. |
| amount | Number | Yes | Transaction payment amount. |
| payment_type | String | No | Payment type (`advance`, `part_payment`, `full_payment`). |
| payment_mode | String | No | Payment mode (`cash`, `upi`, `bank_transfer`, `card`). |
| txn_reference | String | No | Bank transaction reference or UTR number. |
| paid_at | String | No | Timestamp when payment was received. |

#### Response Structure (Success)
```json
{
  "success": true,
  "message": "Payment recorded successfully",
  "data": {
    "id": 1,
    "booking_id": 1,
    "amount": 25000.00,
    "payment_type": "advance",
    "payment_mode": "upi",
    "txn_reference": "TXN987654321",
    "created_at": "2026-08-18T10:00:00+00:00"
  }
}
```

---

## 6. Response Error Codes Reference

| Error Code | HTTP Status | Detail Description |
|---|---|---|
| `UNAUTHENTICATED` | 401 | Missing or invalid Sanctum Authorization Bearer Token header. |
| `VALIDATION_ERROR` | 422 | Required fields missing or failed data validation checks. |
| `RESOURCE_NOT_FOUND` | 404 | The requested record ID does not exist in the system. |
| `FORBIDDEN` | 403 | User role lacks permission for the requested action. |
