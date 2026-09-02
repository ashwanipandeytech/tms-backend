# Frontend API Integration Documentation — SaaS Subscription ERP System

## Overview for Frontend Engineering Team

This document details **only** the new and modified API endpoints, error codes, payload structures, and header requirements introduced for the **SaaS Multi-Tenant Subscription ERP System**.

---

## 1. System Config Statuses Initialization Endpoint (`GET /api/v1/config/statuses`) `[NEW]`

### Endpoints Details
- **URL**: `GET /api/v1/config/statuses`
- **Authentication**: Public / Optional Bearer Token
- **Description**: Returns all system status enums grouped section-wise in a single call for frontend initialization.

### Success Response (200 OK)
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

## 2. Lead CSV Operations (Sample Download, Import & Export) `[NEW]`

### 2.1 Download Sample Leads CSV Template
- **URL**: `GET /api/v1/leads/sample-csv`
- **Authentication**: `Bearer <token>`
- **Response**: File Download (`Content-Type: text/csv`, filename: `leads_sample_template.csv`)

### 2.2 Import Leads via CSV Upload
- **URL**: `POST /api/v1/leads/import`
- **Authentication**: `Bearer <token>`
- **Content-Type**: `multipart/form-data`
- **Body Field**: `file` (CSV / TXT file, max 5MB)

### 2.3 Export Tenant Leads to CSV
- **URL**: `GET /api/v1/leads/export-csv`
- **Authentication**: `Bearer <token>`
- **Optional Query Params**: `search=Kashmir`, `status=NEW_LEAD`
