# Backend Improvements & Issue Tracker (From Frontend Team)

This document tracks current issues, missing endpoints, and feature requests blocking frontend development. 

## 🐛 Bugs & API Issues

| Date | Issue / Endpoint | Description / Technical Impact | Status |
| :--- | :--- | :--- | :--- |
| **2026-08-19** | `GET /api/v1/customers` | **Fixed:** Resolved 500 internal error caused by empty columns array parameter in pagination method. Created `CustomerResource` for clean JSON responses. | 🟢 Resolved |
| **2026-08-19** | `GET /api/v1/users` | **Fixed:** Resolved 500 internal error caused by parameter mismatch in `getPaginated()`. Enabled relation loading for `role` and permissions. | 🟢 Resolved |
| **2026-08-25** | `GET /api/v1/roles` | **New Bug:** Returns 500 Internal Server Error when retrieving roles (even after creating roles via POST). Exception: `Column not found: 1054 Unknown column 'permissions' in 'SELECT'`. The `RoleController@index` method is incorrectly selecting `permissions` directly from the `roles` table. Needs to be loaded via Eloquent relationships (e.g. `Role::with('permissions')`). | 🔴 Pending |

---

## ✨ Missing Features / Endpoints Required

For the **User Management** panel, the frontend requires the following CRUD operations which are now fully implemented and verified:

### 1. Add User
- **Endpoint**: `POST /api/v1/users`
- **Status**: 🟢 Resolved (Supports `name`, `email`, `phone`, `password`, `password_confirmation`, `role_id`, `status`)

### 2. Edit User
- **Endpoint**: `PUT /api/v1/users/{id}`
- **Status**: 🟢 Resolved (Password is optional when editing user profile)

### 3. Delete User
- **Endpoint**: `DELETE /api/v1/users/{id}`
- **Status**: 🟢 Resolved

---

## ✨ Action Items for Backend Team

- [x] Check server logs and fix the `500 Internal Server Error` on both `GET /api/v1/customers` and `GET /api/v1/users`.
- [x] Implement the `POST`, `PUT`, and `DELETE` endpoints for User Management utilizing the payloads above.
- [x] Ensure all endpoints return standard JSON data matching the structure of other endpoints (e.g., `success`, `message`, `data`).
- [x] Document these endpoints and their response structures in `API_DOCUMENTATION.md`.
- [ ] Fix the `500 Internal Server Error` on `GET /api/v1/roles` by correcting the database query so it doesn't query a non-existent `permissions` column. Also document `GET /api/v1/roles` in `API_DOCUMENTATION.md`.
