# Backend Improvements & Issue Tracker (From Frontend Team)

This document tracks current issues, missing endpoints, and feature requests blocking frontend development. 

## 🐛 Bugs & API Issues

| Date | Issue / Endpoint | Description / Technical Impact | Status |
| :--- | :--- | :--- | :--- |
|      |                  | *(No active issues)*           |        |

---

## ✨ Action Items for Backend Team (Completed)

- [x] **Universal Roles & Permissions Management**: `Role` model extends Eloquent `Model` directly without `TenantScopedTrait`, making roles and permissions universal and shared across all tenants. Only Super Admin is authorized to create/edit/delete roles (`POST /roles`, `PUT /roles/{id}`, `DELETE /roles/{id}`). `DELETE /api/v1/admin/reset` strictly preserves all roles and permissions during tenant data resets.
- [x] **Refactor Login Endpoint (`POST /api/v1/login`)**: `role_type` is completely optional. Backend automatically detects role and company context based on `email` and `password`.
- [x] **Include Permissions in Login Response**: `user.role.permissions` relation is eager-loaded and returned as an array of permission strings (e.g. `["leads.view", "leads.create"]`) in `POST /api/v1/login` and `GET /api/v1/me`.
- [x] **Include Company Name in Login Response**: `user.company` object and `user.company_name` string are serialized in `UserResource` during login and profile fetch.
- [x] **Create Super Admin Company List API (`GET /api/v1/admin/companies`)**: Endpoint implemented returning paginated companies with status, subscription details (days remaining, expiring soon flag), total employee counts, and nested employee details.
- [x] **Exclude Super Admin from Roles List (`GET /api/v1/roles`)**: Backend filters out `Super Admin` role from standard roles list responses.
- [x] **Hide Super Admin from User List (`GET /api/v1/users`)**: Backend filters out Super Admin accounts (`role_id` 1) from user list when called by non-SuperAdmin users.
- [x] **Create Bulk Clear & Single Tenant Reset API (`DELETE /api/v1/admin/reset`)**: Endpoint implemented to clear/reset tenant resource data and staff while strictly preserving the primary Super Admin user account and universal roles. Accepts `{ "id": 105 }` for single tenant reset or `{ "clear_all": true }` to wipe test data across all tenants.
- [x] **Super Admin Tenant Workspace Switching (`X-Tenant-ID`)**: When `X-Tenant-ID` header is passed by Super Admin, all tenant-specific queries and creations (leads, bookings, packages, inventory, finance) are automatically scoped to that company ID. Global resources (`GET /roles`, `GET /permissions`, `GET /admin/companies`, `GET /plans`) explicitly ignore `X-Tenant-ID`. `config/cors.php` updated with `X-Tenant-ID` in `allowed_headers`.
- [x] **User Creator Tracking (`created_by` & `created_by_type`)**: Database migration added `created_by` and `created_by_type` columns (`'super_admin'`, `'tenant_admin'`, `'staff'`). `UserResource` now serializes `created_by` details and creator role type.

---

## 🛠️ API Specification & Technical Changes Summary

### 1. Login & Profile Endpoints (`POST /api/v1/login`, `GET /api/v1/me`)
- **Payload**: `role_type` parameter is optional.
- **Response**: `user` object includes `company_name`, `company` object (`id`, `name`, `subdomain`, `subscription_status`, `total_allowed_seats`), and `role.permissions` array of strings.

### 2. Tenant Reset API (`DELETE /api/v1/admin/reset`)
- **Single Tenant Reset Payload**: `{ "id": 105 }` or `{ "tenant_id": 105 }`
- **Bulk Clear All Payload**: `{ "clear_all": true }`
- **Response**: `{ "success": true, "data": { "clear_all": true/false, "company_id": 105, "status": "reset_completed" } }`
- **Preservation Policy**: Primary Super Admin user account and all Universal Roles & Permissions are **never** deleted.

### 3. Super Admin Workspace Switching (`X-Tenant-ID` Header)
- **Header**: `X-Tenant-ID: 105`
- **CORS**: `config/cors.php` configured to allow `X-Tenant-ID` header.
- **Tenant APIs**: Scopes model queries (`BelongsToTenantTrait` & `TenantScopedTrait`) and creation logic to `company_id = 105`.
- **Global APIs Exclusion**: `GET /api/v1/roles`, `GET /api/v1/permissions`, `GET /api/v1/plans`, and `GET /api/v1/admin/companies` ignore `X-Tenant-ID` header and return global system data.

### 4. Global Universal Roles & Permissions Architecture
- **Universal Models**: `Role` model extends `Illuminate\Database\Eloquent\Model` directly without `TenantScopedTrait`. Roles are shared across all tenants.
- **Management Authorization**: Only Super Admin (`isSuperAdmin() === true`) is authorized to perform `POST /api/v1/roles`, `PUT /api/v1/roles/{id}`, and `DELETE /api/v1/roles/{id}`. Attempting role management as a non-SuperAdmin returns HTTP `403 Forbidden` (`FORBIDDEN`).

### 5. User Creator Tracking (`created_by` & `created_by_type`)
- **Columns**: `created_by` (unsigned bigint FK to `users.id`), `created_by_type` (string: `'super_admin'`, `'tenant_admin'`).
- **Response**: `UserResource` returns `created_by` object containing creator's `id`, `name`, `email`, and `created_by_type`.
