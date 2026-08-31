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
- [x] **Create Bulk Clear API (`DELETE /api/v1/admin/reset`)**: Endpoint implemented to clear/reset tenant resource data and staff while strictly preserving the primary Super Admin user account.
