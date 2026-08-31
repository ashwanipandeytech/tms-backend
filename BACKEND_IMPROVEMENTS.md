# Backend Improvements & Issue Tracker (From Frontend Team)

This document tracks current issues, missing endpoints, and feature requests blocking frontend development. 

## 🐛 Bugs & API Issues

| Date | Issue / Endpoint | Description / Technical Impact | Status |
| :--- | :--- | :--- | :--- |
|      |                  | *(No active issues)*           |        |

---

## ✨ Action Items for Backend Team

- [ ] **Refactor Login Endpoint (`POST /api/v1/login`)**: The frontend team is planning to remove the "Login Type" (role) dropdown from the login UI. Please update the authentication logic so that the `role_type` parameter is no longer required in the JSON payload. The backend should automatically determine the user's role (Tenant, Super Admin, Staff, etc.) based on the authenticated email address. Once this backend change is deployed, the frontend will remove the dropdown and the `role_type` payload field.
- [ ] **Create Bulk Clear API (e.g. `DELETE /api/v1/admin/reset`)**: The frontend team requires a new endpoint to clear/reset tenant data. This API should bulk delete all staff `users` and custom `roles` associated with the tenant, **strictly excluding the main Super Admin account and its default role** so the tenant owner doesn't lose access.
