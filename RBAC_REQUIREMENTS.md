# Backend API & Database Requirements
**Project:** Travel CRM Role-Based Access Control (RBAC) & Workflow Update

This document outlines the required changes to the backend APIs and database to support the new CRM workflow (Lead Sources → Leads → Admin Assignment → Sales → Operations → Completion). 

## 1. Database Schema Changes

### 1.1 Bookings Table
- Add an `operations_id` column to the `bookings` table. This must be a foreign key referencing the `id` on the `users` table. 
- **Purpose:** Tracks which Operations team member is handling the fulfillment of the confirmed booking.

### 1.2 Leads Table
- Ensure the `assigned_to` column (foreign key to `users`) is functioning correctly. (It appears to already exist).
- Add a `source_tracking` or `campaign_source` column to explicitly track where Meta/Website leads originated (optional but recommended for reporting).

## 2. Roles & Permissions Matrix

The backend must seed or configure the following roles and enforce access to these specific panels/modules.

| Role | Responsibility | Modules & Access Level |
| :--- | :--- | :--- |
| **Super Admin / Manager** | Full System Access | **All Modules**: `view_all`, `create`, `edit_all`, `delete`, `import`, `assign` |
| **Sales Executive** | Works on assigned leads and quotes | **Leads**: `view_assigned`, `edit_assigned`<br>**Quotations**: `view_own`, `create`, `edit_own`<br>**Follow-ups**: `view_own`, `create`, `edit_own`<br>**Inventory**: `view_all` (Read-only)<br>**Bookings**: `view_own` (Read-only for their confirmed leads) |
| **Operation Team** | Post-sales fulfillment, vendor management | **Bookings**: `view_assigned`, `edit_assigned`<br>**Inventory (Hotels/Resorts)**: `view_all`, `create`, `edit`<br>**Cabs**: `view_all`, `create`, `edit`<br>**Invoices**: `view_assigned` (Read-only) |
| **Accounts** | Financial management | **Finance (Invoices/Expenses/Payments)**: `view_all`, `create`, `edit`<br>**Bookings**: `view_all` (Read-only) |

## 3. API Updates for Authorization (RBAC)

The APIs currently return all records for all authenticated users. They must be scoped to the user's role according to the matrix above.

### 2.1 Lead API (`/api/v1/leads`)
- **Admin**: Can view, edit, and delete all leads.
- **Sales**: 
  - `GET /api/v1/leads`: Modify the query to append `->where('assigned_to', auth()->id())`.
  - `PUT /api/v1/leads/{id}`: Add an authorization check to ensure the Lead's `assigned_to` matches `auth()->id()`. Returns `403 Forbidden` otherwise.
- **Operations / Accounts**: Should return `403 Forbidden` for all Lead endpoints.

### 2.2 Booking API (`/api/v1/bookings`)
- **Admin**: Can view, edit, and delete all bookings.
- **Sales**:
  - `GET /api/v1/bookings`: Scoped to return only bookings originating from their assigned leads, OR read-only access to all bookings. (Confirm exact read requirement).
- **Operations**:
  - `GET /api/v1/bookings`: Scoped to `->where('operations_id', auth()->id())`.
  - `PUT /api/v1/bookings/{id}`: Can only edit bookings where `operations_id == auth()->id()`.

### 2.3 Quotations & Follow-ups API
- **Sales**: Scoped to `auth()->id()` so sales reps only see their own follow-ups and quotes.

### 2.4 Auth/Login API (`/api/v1/login`)
- **Frontend Expectation:** The frontend will now send `role_type` in the login payload (e.g., `admin`, `sales`, `operations`).
- **Backend Task:** Validate that the authenticating user actually belongs to the provided `role_type` or `role_id` before issuing the token. If they select "Admin" but are a "Sales" user in the DB, return a `401 Unauthorized`.

## 3. New API Endpoints Required

### 3.1 Lead Assignment Endpoints
- `PUT /api/v1/leads/{id}/assign`
  - **Payload:** `{ "assigned_to": 5 }`
  - **Auth:** Admin only.

### 3.2 Operations Handoff Endpoint
- `PUT /api/v1/bookings/{id}/assign-operations`
  - **Payload:** `{ "operations_id": 8 }`
  - **Auth:** Admin (and optionally Sales).

### 3.3 Lead Ingestion Webhooks
- `POST /api/v1/webhooks/leads/meta`
- `POST /api/v1/webhooks/leads/website`
  - **Purpose:** Publicly accessible endpoints (or secured via static API token) to ingest leads directly from marketing platforms.

### 3.4 CSV Lead Import
- `POST /api/v1/leads/import`
  - **Payload:** `multipart/form-data` with a CSV file.
  - **Logic:** Parse CSV, check for duplicate emails/phones, bulk insert, and return import status. Auth: Admin only.

### 3.5 User Management Endpoints
- `POST /api/v1/users`
  - **Payload:** `{ "name": "...", "email": "...", "password": "...", "role_id": 3 }`
  - **Auth:** Super Admin / Manager only.
  - **Logic:** Validate data, hash password, ensure the assigned `role_id` exists, and create the user.
- `PUT /api/v1/users/{id}`
  - **Payload:** `{ "name": "...", "email": "...", "role_id": 3, "status": "active" }`
  - **Auth:** Super Admin / Manager only.

## 4. Reports & Analytics API

Update the `ReportController` to provide role-based performance metrics:
- **Sales Performance Metrics:** Group by `assigned_to`. Metrics: Total Leads Assigned, Contacted, Conversion Rate, Quotations Sent.
- **Operations Performance Metrics:** Group by `operations_id`. Metrics: Active Bookings, Completed Bookings, Average Turnaround Time.
- Ensure these endpoints accept filtering by `date_range`, `user_id`, and `source`.

---

## 5. Frontend Implementation Context

For the backend team's reference, the following changes have already been implemented in the frontend application today to support this new workflow:

### 5.1 Route Guards & Security
- **Role Guard Interceptor (`role.guard.ts`)**: The Angular router now inspects the user's role before allowing access to any module. If an unauthorized role attempts to access a protected route (e.g., a Sales agent trying to navigate to `/invoices`), they are automatically blocked and redirected.

### 5.2 Dynamic UI & Navigation
- **Conditional Sidebar Layout**: The main navigation menu dynamically hides or shows entire sections based on the logged-in user's role. For example, the "Finance" and "Admin" sections are completely hidden from Sales and Operations staff.
- **Restricted Actions**: In panels like Leads, critical action buttons such as "Delete Lead", "Import CSV", and the "Assign To" dropdown are only visible or enabled for the `Super Admin` and `Manager` roles.

### 5.3 Login Enhancements
- **Role Dropdown**: The login screen now requires the user to select their Role (Admin, Sales, Ops, Accounts) before submitting the form. The frontend sends this `role_type` along with the email and password, which the backend is expected to validate (see Section 2.4).

### 5.4 Admin User Management
- **User Creation Form**: A new interface has been built in the `/users` panel allowing Admins to create new staff accounts and assign them to specific roles directly from the CRM.
