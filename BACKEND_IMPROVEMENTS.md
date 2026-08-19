# Backend Improvements & Issue Tracker (From Frontend Team)

This document tracks current issues, missing endpoints, and feature requests blocking frontend development. 

## 🐛 Bugs & API Issues

| Date | Issue / Endpoint | Description / Technical Impact | Status |
| :--- | :--- | :--- | :--- |
| **2026-08-19** | `GET /api/v1/customers` | **Error:** Returns `500 Internal Server Error`.<br><br>**Why we need it:** The frontend is attempting to fetch a list of customers to populate the `customer_id` selection dropdown in the "Create Booking" form (`POST /bookings`).<br><br>**Impact:** Without this working, users are completely blocked from creating new bookings because they cannot select a customer.<br><br>**Action Required:** Please check the Laravel `storage/logs/laravel.log` for the exact exception stack trace. Ensure the route exists, the controller method handles the database query correctly, and standard paginated JSON is returned. | 🔴 New |
| **2026-08-19** | `GET /api/v1/users` | **Error:** Returns `500 Internal Server Error`.<br><br>**Why we need it:** The frontend needs to fetch a list of staff (Sales Executives, Admins) to populate the `assigned_to` field when creating or updating a Lead (`POST /leads`). It is also necessary for Team Management screens.<br><br>**Impact:** We cannot assign leads to specific sales executives, breaking the lead distribution workflow.<br><br>**Action Required:** Investigate the 500 error in the logs, ensure the User model and route are correctly wired, and return a JSON list of users (ideally with their roles). | 🔴 New |

---

## ✨ Action Items for Backend Team

- [ ] Check server logs and fix the `500 Internal Server Error` on both `/api/v1/customers` and `/api/v1/users`.
- [ ] Ensure both endpoints return standard JSON data matching the structure of other endpoints (e.g., `success`, `message`, `data`).
- [ ] Document these endpoints and their response structures in `API_DOCUMENTATION.md`.

*Note: Update the Status column to `🟡 Pending` when investigating, and `🟢 Resolved` once fixed and documented.*
