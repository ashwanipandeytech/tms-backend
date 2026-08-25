# Backend Improvements & Issue Tracker (From Frontend Team)

This document tracks current issues, missing endpoints, and feature requests blocking frontend development. 

## 🐛 Bugs & API Issues

| Date | Issue / Endpoint | Description / Technical Impact | Status |
| :--- | :--- | :--- | :--- |

| **2026-08-25** | `GET /api/v1/roles` | **New Bug:** Returns 500 Internal Server Error when retrieving roles (even after creating roles via POST). Exception: `Column not found: 1054 Unknown column 'permissions' in 'SELECT'`. The `RoleController@index` method is incorrectly selecting `permissions` directly from the `roles` table. Needs to be loaded via Eloquent relationships (e.g. `Role::with('permissions')`). | 🔴 Pending |

---

