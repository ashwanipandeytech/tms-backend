# Backend Improvements & Issue Tracker (From Frontend Team)

This document tracks current issues, missing endpoints, and feature requests blocking frontend development. 

## 🐛 Bugs & API Issues

| Date | Issue / Endpoint | Description / Technical Impact | Status |
| :--- | :--- | :--- | :--- |
|      |                  | *(No active issues)*           |        |

---

## ✨ Action Items for Backend Team

- [x] **Refactor Login Endpoint (`POST /api/v1/login`)**: `role_type` is completely optional. Backend automatically detects role and company context based on `email` and `password`.
- [x] **Include Permissions in Login Response**: `user.role.permissions` relation is eager-loaded and returned as an array of permission strings (e.g. `["leads.view", "leads.create"]`) in `POST /api/v1/login` and `GET /api/v1/me`.
- [x] **Include Company Name in Login Response**: `user.company` object and `user.company_name` string are serialized in `UserResource` during login and profile fetch.
- [x] **Create Super Admin Company List API (`GET /api/v1/admin/companies`)**: Endpoint implemented returning paginated companies with status, subscription details (days remaining, expiring soon flag), total employee counts, and nested employee details.
- [x] **Exclude Super Admin from Roles List (`GET /api/v1/roles`)**: Backend filters out `Super Admin` role from standard roles list responses.
- [x] **Hide Super Admin from User List (`GET /api/v1/users`)**: Backend filters out Super Admin accounts (`role_id` 1) from user list when called by non-SuperAdmin users.
- [x] **Create Bulk Clear API (`DELETE /api/v1/admin/reset`)**: Endpoint implemented to clear/reset tenant resource data and staff while strictly preserving the primary Super Admin user account. **UPDATE:** The API has been modified to expect an `id` or `tenant_id` in the request payload so that the Super Admin can reset specific single tenants from the tenant selection screen. **LATEST:** If the frontend passes `clear_all: true` in the payload, the backend must ignore specific tenant scoping and execute a true bulk wipe across ALL tenants.
- [x] **Super Admin Data Scoping / Tenant Switching**: 
  - **Full Frontend Flow (For Context)**: 
    1. Super Admin logs into the application.
    2. The frontend route guard forcefully intercepts them and holds them at a dedicated **"Select Tenant"** panel (`/select-tenant`). They are completely blocked from accessing the Dashboard or any CRM modules.
    3. On this panel, they can hit **Bulk Clear**, which sends a `DELETE /api/v1/admin/reset` with `{ clear_all: true }`.
    4. They can also hit **Reset** on an individual tenant card, which sends a `DELETE /api/v1/admin/reset` with `{ id: <tenant_id> }`.
    5. To proceed, they must click **Enter Workspace** on a specific tenant card. The frontend then securely saves that tenant's ID into Local Storage as `activeTenantId` and allows them into the `/dashboard`.
    6. They can switch tenants at any time using a new **"Switch Tenant"** button in their sidebar, bringing them back to step 2.
  - **Header Injection**: While they are inside a tenant's workspace, the frontend automatically appends an **`X-Tenant-ID`** header to ALL outgoing HTTP requests via an interceptor.
  - **Action Required**: The backend should use this `X-Tenant-ID` header (when present and valid for the authenticated Super Admin) to seamlessly filter and scope all data queries and mutations (e.g., fetching leads, creating cabs) to that specific tenant. **Important Note:** You must update `config/cors.php` to include `X-Tenant-ID` in the `allowed_headers` array, otherwise the frontend interceptor triggers CORS errors.
