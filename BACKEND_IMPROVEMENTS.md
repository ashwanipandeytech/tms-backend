# Backend Improvements & Issue Tracker (From Frontend Team)

This document tracks current issues, missing endpoints, and feature requests blocking frontend development. 

## 🐛 Bugs & API Issues

| Date | Issue / Endpoint | Description / Technical Impact | Status |
| :--- | :--- | :--- | :--- |
|      |                  | *(No active issues)*           |        |

---

## ✨ Action Items for Backend Team

- [ ] **Refactor Login Endpoint (`POST /api/v1/login`)**: The frontend team is planning to remove the "Login Type" (role) dropdown from the login UI. Please update the authentication logic so that the `role_type` parameter is no longer required in the JSON payload. The backend should automatically determine the user's role (Tenant, Super Admin, Staff, etc.) based on the authenticated email address. Once this backend change is deployed, the frontend will remove the dropdown and the `role_type` payload field.
- [ ] **Include Permissions in Login Response**: When returning the authenticated `user` object in the `/api/v1/login` response, please ensure the `permissions` relationship is loaded on the user's `role` (i.e. `role.permissions` should not be an empty array). The frontend needs these permissions immediately upon login to build the UI appropriately.
- [ ] **Include Company Name in Login Response**: Since the CRM is multi-tenant, please ensure the `company` relationship (or at least the `company_name`) is loaded and returned inside the `user` object during the `/api/v1/login` response, so the frontend can display the specific company's name in the dashboard header for staff members.
- [ ] **Create Super Admin Company List API (e.g. `GET /api/v1/admin/companies`)**: The Super Admin needs a dashboard/list view of all registered companies. Please create an endpoint that returns a paginated list of companies along with aggregated statistics for each company, specifically:
  - Account status (active/inactive)
  - Subscription details (especially if about to expire)
  - Registration date (to identify newly added companies)
  - Total number of employees (`users` count)
- [ ] **Exclude Super Admin from Roles List (`GET /api/v1/roles`)**: When fetching the list of roles, the backend should filter out and hide the "Super Admin" role from the response. The Super Admin is the owner of the CRM and this role should not be editable, assignable to regular staff, or visible in the standard roles list.
- [ ] **Hide Super Admin from User List (`GET /api/v1/users`)**: When returning the list of staff users, if the currently authenticated user is *not* a Super Admin, the backend must filter out the Super Admin account from the response. This prevents regular staff members from seeing or attempting to modify the owner's account.
- [ ] **Create Bulk Clear API (e.g. `DELETE /api/v1/admin/reset`)**: The frontend team requires a new endpoint to clear/reset tenant data. This API should bulk delete all staff `users` and custom `roles` associated with the tenant, **strictly excluding the main Super Admin account and its default role** so the tenant owner doesn't lose access.
