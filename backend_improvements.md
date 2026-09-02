# Backend Architectural Improvements & Multi-Channel Integration Specification

## Executive Summary
This document serves as the **authoritative architectural specification and contract** shared between both backend and frontend (`tms-frontend`) development teams. It details all backend architectural refactorings, security standards, status configurations, and multi-channel lead ingestion capabilities (Meta Ads, Google Ads, WhatsApp Business Cloud, Website Forms, and CSV imports).

---

## 1. Multi-Channel Lead Ingestion Architecture

Leads are captured in real-time from multiple marketing channels into the centralized database (`leads` table) via high-performance webhook endpoints and bulk CSV utilities.

```
                    ┌─────────────────────────────────────────┐
                    │            Lead Data Sources            │
                    └──────────────────┬──────────────────────┘
                                       │
      ┌────────────────┬───────────────┼───────────────┬────────────────┐
      │                │               │               │                │
┌─────▼───────┐ ┌──────▼──────┐ ┌──────▼──────┐ ┌──────▼──────┐  ┌──────▼──────┐
│  Meta Ads   │ │ Google Ads  │ │  WhatsApp   │ │Website Forms│  │ CSV Import  │
│ (FB & Insta)│ │ (Search)    │ │ Business    │ │  & Popups   │  │  & Manual   │
└─────┬───────┘ └──────┬──────┘ └──────┬──────┘ └──────┬──────┘  └──────┬──────┘
      │                │               │               │                │
      ▼                ▼               ▼               ▼                ▼
/webhooks/    /webhooks/      /webhooks/      /webhooks/       /leads/import
 leads/meta    leads/google    leads/whatsapp  leads/website     /leads (POST)
      │                │               │               │                │
      └────────────────┴───────────────┼───────────────┴────────────────┘
                                       │
                                       ▼
                    ┌─────────────────────────────────────────┐
                    │      LeadService & Data Validation      │
                    │        (Tenant Isolation & Scope)       │
                    └──────────────────┬──────────────────────┘
                                       │
                                       ▼
                    ┌─────────────────────────────────────────┐
                    │         Database (`leads` table)        │
                    └─────────────────────────────────────────┘
```

### 1.1 Meta Ads (Facebook & Instagram Lead Forms)
- **Endpoint**: `POST /api/v1/webhooks/leads/meta`
- **Authentication**: Public Webhook Entry Point
- **Payload Schema**:
  ```json
  {
    "name": "Rahul Sharma",
    "phone": "9876543210",
    "email": "rahul.sharma@example.com",
    "destination": "Kashmir",
    "campaign_source": "Meta Facebook Ads - Summer Campaign",
    "notes": "Interested in 5D/4N Kashmir package"
  }
  ```
- **Database Assignment**: `campaign_source` defaults to `"Meta Facebook/Instagram Ads"`. `status` is automatically initialized to `"NEW_LEAD"`.

---

### 1.2 Google Ads (Lead Form Extensions)
- **Endpoint**: `POST /api/v1/webhooks/leads/google`
- **Authentication**: Public Webhook Entry Point (Google Webhook Key verified via `google_key`)
- **Payload Schema**:
  ```json
  {
    "full_name": "Vikram Singh",
    "phone": "9876543211",
    "email": "vikram@example.com",
    "destination": "Ladakh",
    "campaign_source": "Google Search Ads - Bike Trip",
    "google_key": "YOUR_GOOGLE_SECRET_KEY"
  }
  ```
- **Field Normalization**: Accepts `full_name` or `name`. `campaign_source` defaults to `"Google Search Ads"`.

---

### 1.3 WhatsApp Business Cloud API Integration
- **Endpoint**: `POST /api/v1/webhooks/leads/whatsapp`
- **Authentication**: Public Webhook Entry Point
- **Payload Schema**:
  ```json
  {
    "phone": "919876543212",
    "name": "Ananya Gupta",
    "message": "Hi, I saw your Goa resort package on WhatsApp. Please send details.",
    "destination": "Goa"
  }
  ```
- **Auto-Formatting**: If `name` is omitted, the backend auto-generates `"WhatsApp Lead (XXXX)"` using the last 4 digits of the phone number. `message` content is stored in `notes`. `campaign_source` defaults to `"WhatsApp Direct"`.

---

### 1.4 Website Contact & Enquiry Forms
- **Endpoint**: `POST /api/v1/webhooks/leads/website`
- **Authentication**: Public Webhook Entry Point
- **Payload Schema**:
  ```json
  {
    "full_name": "Amit Patel",
    "phone": "9876543213",
    "email": "amit@example.com",
    "destination": "Kerala",
    "travel_date": "2026-10-15",
    "pax_adults": 2,
    "pax_children": 1,
    "budget": 45000,
    "campaign_source": "Website Popup Enquiry"
  }
  ```

---

### 1.5 CSV Bulk Lead Import & Sample File Download
- **Download Sample CSV Template**: `GET /api/v1/leads/sample-csv`
  - Streams a pre-formatted template file (`leads_sample_template.csv`) with headers: `Name`, `Phone`, `Email`, `Destination`, `Budget`, `Notes`.
- **Import CSV**: `POST /api/v1/leads/import` (`multipart/form-data`)
  - Parses uploaded CSV files, automatically skipping duplicate phone numbers within the tenant company.
  - Returns `imported_count` and `skipped_count`.
- **Export Leads to CSV**: `GET /api/v1/leads/export-csv` (Supports optional filters `search` & `status`).

---

## 2. System Status Initialization API (`GET /api/v1/config/statuses`)

To ensure the frontend (`tms-frontend`) can render filter dropdowns and status badges without hardcoding strings, the backend provides a single initialization endpoint:

- **Endpoint**: `GET /api/v1/config/statuses`
- **Authentication**: Public / Optional Bearer Token
- **Payload Structure**:
  ```json
  {
    "success": true,
    "message": "System status configurations retrieved successfully",
    "data": {
      "leads": [
        { "key": "NEW_LEAD", "label": "New Lead" },
        { "key": "ATTEMPTED_CONTACT", "label": "Attempted Contact" },
        { "key": "CONNECTED", "label": "Connected" },
        { "key": "FOLLOW_UP", "label": "Follow Up" },
        { "key": "INTERESTED", "label": "Interested" },
        { "key": "QUOTATION_SENT", "label": "Quotation Sent" },
        { "key": "NEGOTIATION", "label": "Negotiation" },
        { "key": "BOOKING_CONFIRMED", "label": "Booking Confirmed" },
        { "key": "TOUR_COMPLETED", "label": "Tour Completed" },
        { "key": "NOT_INTERESTED", "label": "Not Interested" },
        { "key": "LOST_LEAD", "label": "Lost Lead" },
        { "key": "CANCELLED", "label": "Cancelled" }
      ],
      "bookings": [
        { "key": "pending", "label": "Pending" },
        { "key": "confirmed", "label": "Confirmed" },
        { "key": "completed", "label": "Completed" },
        { "key": "cancelled", "label": "Cancelled" }
      ],
      "invoices": [
        { "key": "unpaid", "label": "Unpaid" },
        { "key": "partial", "label": "Partially Paid" },
        { "key": "paid", "label": "Paid" }
      ],
      "followups": [
        { "key": "pending", "label": "Pending" },
        { "key": "done", "label": "Done" },
        { "key": "missed", "label": "Missed" }
      ],
      "vehicles": [
        { "key": "available", "label": "Available" },
        { "key": "booked", "label": "Booked" },
        { "key": "maintenance", "label": "Maintenance" }
      ],
      "quotations": [
        { "key": "draft", "label": "Draft" },
        { "key": "sent", "label": "Sent" },
        { "key": "accepted", "label": "Accepted" },
        { "key": "rejected", "label": "Rejected" }
      ],
      "customers": [
        { "key": "active", "label": "Active" },
        { "key": "inactive", "label": "Inactive" }
      ]
    }
  }
  ```

---

## 3. Clean Architecture Standards & Thin Controllers

All 24 API controllers under `app/Http/Controllers/Api/V1/` have been refactored into **Thin Controllers**.

1. **Form Requests (`app/Http/Requests/*`)**: 25+ dedicated validation classes handle boundary authorization and strict validation.
2. **DTO Layer (`app/DTOs/*`)**: Strongly typed data structures pass input between HTTP boundaries and domain services.
3. **Service Layer (`app/Services/*`)**: Domain logic, `DB::transaction` execution, and query scoping are isolated inside dedicated service classes.

---

## 4. Security, RBAC & Role Protection Standards

### 4.1 Platform-Wide Universal Roles
- **Universal Roles**: `Super Admin`, `Manager`, `Sales Executive`, `Operation Team`, `Accounts`.
- **Role Scoping**: Universal roles are global (`company_id = null`). Management of roles (`POST/PUT/DELETE /api/v1/roles`) is restricted strictly to Super Admin.

### 4.2 Duplicate Role Name Rejection
- Role creation/updating enforces case-insensitive singular and plural duplicate checks. Attempting to create `"Managers"` when `"Manager"` exists returns:
  ```json
  {
    "success": false,
    "message": "A role with a similar name ('Manager') already exists.",
    "error_code": "DUPLICATE_ROLE_NAME"
  }
  ```

### 4.3 Tenant Staff Isolation
- Super Admin accounts (`role_id: 1`) are excluded from tenant user listings (`GET /api/v1/users`). Tenant employee lists display only company staff.

---

## 5. Subscriptions & Tenant Isolation Standards

- **User Seat Limits**: Creating a new user when tenant allowed seats are exhausted returns HTTP `422` with `USER_SEAT_LIMIT_REACHED`.
- **Super Admin Workspace Switching (`X-Tenant-ID`)**: Attaching `X-Tenant-ID: <company_id>` allows Super Admin users to view and manage specific tenant workspace data.

---

## 6. Testing & Automated Verification

- **PHPUnit Feature Tests**: **15/15 feature tests passed cleanly (114 assertions)**.
- **Static Syntax Audit (`php -l`)**: 84/84 PHP files passed with 0 syntax errors.
- **Route & Config Caching (`route:cache`, `config:cache`)**: 100% clean compilation.
