# Backend Improvements & Issue Tracker (From Frontend Team)

This document tracks current issues, missing endpoints, and feature requests blocking frontend development. 

## 🐛 Bugs & API Issues

| Date | Issue / Endpoint | Description / Technical Impact | Status |
| :--- | :--- | :--- | :--- |
| **2026-08-19** | `GET /api/v1/customers` | **Error:** Returns `500 Internal Server Error`.<br><br>**Why we need it:** The frontend is attempting to fetch a list of customers to populate the `customer_id` selection dropdown in the "Create Booking" form (`POST /bookings`).<br><br>**Impact:** Without this working, users are completely blocked from creating new bookings because they cannot select a customer.<br><br>**Action Required:** Please check the Laravel `storage/logs/laravel.log` for the exact exception stack trace. Ensure the route exists, the controller method handles the database query correctly, and standard paginated JSON is returned. | 🔴 New |
| **2026-08-19** | `GET /api/v1/users` | **Error:** Returns `500 Internal Server Error`.<br><br>**Why we need it:** The frontend needs to fetch a list of staff (Sales Executives, Admins) to populate the `assigned_to` field when creating or updating a Lead (`POST /leads`). It is also necessary for Team Management screens.<br><br>**Impact:** We cannot assign leads to specific sales executives, breaking the lead distribution workflow.<br><br>**Action Required:** Investigate the 500 error in the logs, ensure the User model and route are correctly wired, and return a JSON list of users (ideally with their roles). | 🔴 New |

---

## ✨ Missing Features / Endpoints Required

For the **User Management** panel, the frontend requires the following CRUD operations which are currently missing. Here are the expected payloads for User Add, Edit, and Delete:

### 1. Add User
- **Endpoint**: `POST /api/v1/users`
- **Expected Request Payload**:
  ```json
  {
    "name": "Jane Doe",
    "email": "jane@demohandler.in",
    "phone": "9876543210",
    "password": "Password@123",
    "password_confirmation": "Password@123",
    "role_id": 3,
    "status": "active"
  }
  ```

### 2. Edit User
- **Endpoint**: `PUT /api/v1/users/{id}`
- **Expected Request Payload**:
  *(Note: Password should be optional when editing)*
  ```json
  {
    "name": "Jane Doe Updated",
    "email": "jane@demohandler.in",
    "phone": "9876543211",
    "role_id": 3,
    "status": "inactive" 
  }
  ```

### 3. Delete User
- **Endpoint**: `DELETE /api/v1/users/{id}`
- **Expected Request Payload**: *(None required, just the ID path parameter)*

---

## ✨ Action Items for Backend Team

- [ ] Check server logs and fix the `500 Internal Server Error` on both `GET /api/v1/customers` and `GET /api/v1/users`.
- [ ] Implement the `POST`, `PUT`, and `DELETE` endpoints for User Management utilizing the payloads above.
- [ ] Ensure all endpoints return standard JSON data matching the structure of other endpoints (e.g., `success`, `message`, `data`).
- [ ] Document these endpoints and their response structures in `API_DOCUMENTATION.md`.

*Note: Update the Status column to `🟡 Pending` when investigating, and `🟢 Resolved` once fixed and documented.*
