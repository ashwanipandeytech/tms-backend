# Safar Musafir Travel CRM — REST API Documentation

> **Version**: `1.0.0`  
> **Last Updated**: `2026-08-18`  
> **Target Audience**: Angular / Frontend Developers  
> **Base URL**: `http://127.0.0.1:8013/api/v1` (or production API domain)  
> **Format**: JSON (`Content-Type: application/json`, `Accept: application/json`)

---

## 📌 Change Tracking & Versioning Standard

To keep Postman and Angular contracts perfectly synchronized:
- `[NEW]`: Newly added API endpoint or payload field.
- `[UPDATED]`: Modified existing payload, query parameter, or response structure.
- `[DEPRECATED]`: Marked for removal in future versions.

Whenever any endpoint, parameter, or response schema is altered, both this document (`API_DOCUMENTATION.md`) and `crmtravel_api_postman_collection.json` are updated with the corresponding tag.

---

## 🔐 Angular Integration & Authentication Guide

### 1. Authentication Strategy
The API uses **Laravel Sanctum Bearer Tokens**.
1. Call `POST /api/v1/login` with email and password.
2. Save the returned `token` in `localStorage` or `sessionStorage`.
3. Include the token in the `Authorization` header for all subsequent API calls:
   ```http
   Authorization: Bearer <token>
   ```

### 2. Angular HttpInterceptor Implementation Example
```typescript
import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const token = localStorage.getItem('authToken');

    let clonedRequest = req.clone({
      setHeaders: {
        'Accept': 'application/json'
      }
    });

    if (token) {
      clonedRequest = clonedRequest.clone({
        setHeaders: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
    }

    return next.handle(clonedRequest);
  }
}
```

---

## 🏗 Standard Response Envelope & Error Handling

### Standard Success Response (200 OK / 201 Created)
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... }
}
```

### Standard Paginated Response (200 OK)
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
  }
}
```

### Standard Error Response (400 Bad Request / 401 Unauthorized / 422 Validation Error)
```json
{
  "success": false,
  "message": "Validation failed",
  "error_code": "VALIDATION_ERROR",
  "errors": {
    "email": ["The email field is required."],
    "phone": ["The phone field must be a valid phone number."]
  }
}
```

---

## 📚 Endpoint Reference

### 1. Authentication Endpoints

#### `POST /login` `[NEW]`
Authenticate user and receive Bearer Token.

- **Auth**: None (Public)
- **Request Body**:
  ```json
  {
    "email": "admin@safarmusafir.com",
    "password": "Admin@123"
  }
  ```
- **Response (200 OK)**:
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
          "permissions": ["leads.view", "leads.create", "bookings.view"]
        }
      },
      "token": "1|abcdef123456..."
    }
  }
  ```

#### `GET /me` `[NEW]`
Get logged-in user profile & permissions.
- **Auth**: Bearer Token
- **Response (200 OK)**: Returns user object with role & permissions.

#### `POST /logout` `[NEW]`
Revoke current access token.
- **Auth**: Bearer Token
- **Response (200 OK)**: `{"success": true, "message": "Successfully logged out"}`

---

### 2. Dashboard & Analytics Endpoints

#### `GET /dashboard` `[NEW]`
Get real-time dashboard KPIs, lead funnel, and upcoming departures.
- **Auth**: Bearer Token
- **Response (200 OK)**:
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
        "revenue": 450000,
        "pending_pay": 120000
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
          "package": {"id": 1, "name": "Kashmir Super Tour"}
        }
      ]
    }
  }
  ```

#### `GET /reports/leads-by-source` `[NEW]`
- **Response (200 OK)**: `[{"name": "Website", "total": 45}, {"name": "WhatsApp", "total": 30}]`

#### `GET /reports/sales-by-staff` `[NEW]`
- **Response (200 OK)**: `[{"name": "Super Admin", "bookings_count": 12, "revenue": "350000.00"}]`

#### `GET /reports/monthly-revenue` `[NEW]`
- **Response (200 OK)**: `[{"ym": "2026-08", "total": "450000.00"}]`

---

### 3. Lead Management (`/leads`)

#### `GET /leads` `[NEW]`
List leads with pagination, search, and status filter.
- **Auth**: Bearer Token
- **Query Params**:
  - `page` (int, default: 1)
  - `per_page` (int, default: 15)
  - `search` (string, optional - searches name, phone, email, destination)
  - `status` (string, optional - `new`, `contacted`, `followup`, `interested`, `quotation_sent`, `negotiation`, `confirmed`, `lost`)
- **Response (200 OK)**: Paginated response array of Lead items.

#### `POST /leads` `[NEW]`
Create a new lead (automatically logs enquiry activity timeline).
- **Request Body**:
  ```json
  {
    "name": "Rahul Sharma",
    "phone": "9876543210",
    "email": "rahul@example.com",
    "source_id": 1,
    "destination": "Kashmir",
    "travel_date": "2026-09-15",
    "pax_adults": 2,
    "pax_children": 1,
    "budget": 50000.00,
    "status": "new",
    "assigned_to": 1,
    "notes": "Prefers 4-star hotel stay"
  }
  ```

#### `GET /leads/{id}` `[NEW]`
Get single lead details with relations (`source`, `assignedUser`, `activities`, `followUps`).

#### `PUT /leads/{id}` `[NEW]`
Update lead details (supports partial update).

#### `DELETE /leads/{id}` `[NEW]`
Delete lead record.

---

### 4. Bookings Management (`/bookings`)

#### `GET /bookings` `[NEW]`
- **Query Params**: `page`, `per_page`, `search`, `status` (`pending`, `confirmed`, `cancelled`, `completed`)

#### `POST /bookings` `[NEW]`
- **Request Body**:
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

---

### 5. Payments Management (`/payments`)

#### `POST /payments` `[NEW]`
Record payment for a booking (automatically updates booking `paid_amount` and `due_amount`).
- **Request Body**:
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

---

### 6. Inventory Endpoints

#### Packages: `GET, POST, GET/{id}, PUT/{id}, DELETE /{id} /packages` `[NEW]`
#### Hotels: `GET, POST, GET/{id}, PUT/{id}, DELETE /{id} /hotels` `[NEW]`
#### Resorts: `GET, POST, GET/{id}, PUT/{id}, DELETE /{id} /resorts` `[NEW]`
#### Villas: `GET, POST, GET/{id}, PUT/{id}, DELETE /{id} /villas` `[NEW]`
#### Cabs & Vendors: `GET, POST /vendors`, `GET, POST /vehicles`, `GET, POST /cab-bookings` `[NEW]`

---

### 7. Finance & Admin Endpoints

#### Invoices: `GET, POST, GET/{id}, PUT/{id}, DELETE /{id} /invoices` `[NEW]`
#### Expenses: `GET, POST, GET/{id}, PUT/{id}, DELETE /{id} /expenses` `[NEW]`
#### Staff Users: `GET, POST, GET/{id}, PUT/{id}, DELETE /{id} /users` `[NEW]`
#### Roles & Permissions: `GET, POST, GET/{id}, PUT/{id}, DELETE /{id} /roles` `[NEW]`
