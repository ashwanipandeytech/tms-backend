# Backend Improvements & Issue Tracker (From Frontend Team)

This document tracks current issues, missing endpoints, and feature requests blocking frontend development. 

## 🐛 Bugs & API Issues

| Date | Issue / Endpoint | Description / Technical Impact | Status |
| :--- | :--- | :--- | :--- |

| **2026-08-25** | `GET /api/v1/roles` | **Bug Fixed:** Loaded `permissions` via Eloquent relationship (`Role::with(['permissions'])`) instead of passing relation array as select columns. | 🟢 Resolved |

---

