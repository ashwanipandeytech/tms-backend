# UI/UX Screen Design Specification — SaaS Subscription ERP System

## Overview for UI/UX Designer

This document provides exact screen layouts, components, wireframe guidelines, modal behaviors, and user flows required to design the **SaaS Multi-Tenant Subscription ERP System**.

The application has two distinct user panel interfaces:
1. **Super Admin Platform Panel** (Used by Super Admin to manage subscription plans, onboard subscribers, and set up companies).
2. **Subscriber / Tenant Admin Panel** (Used by Company Admins to view subscription status, manage seats, and create staff users).

---

## Part 1: Super Admin Platform Panel (3 Core Screens)

### Screen 1.1: Subscription Plans Management (`/admin/plans`)

#### Purpose
Allows Super Admin to view, create, and edit subscription tiers (Starter, Professional, Enterprise).

#### Key Components & Layout
1. **Header Bar**:
   - Title: "Subscription Plans & Pricing Tiers"
   - Primary Action Button: `+ Create New Plan`
2. **Plan Cards Grid (3 Cards Side-by-Side)**:
   - **Card Title**: Plan Name (e.g. `Starter Plan`, `Professional Plan`, `Enterprise Plan`).
   - **Price Tag**: `$49 / month` or `₹3,999 / month`.
   - **Base Included Seats**: `5 Base Included Seats`.
   - **Add-on Seat Rate**: `+$5 / additional seat / month`.
   - **Database Model Badge**: `Shared Database` or `Dedicated Database`.
   - **Module Access Checklist**:
     - ✅ Leads & Follow-ups
     - ✅ Bookings Management
     - ❌ Tour Packages & Itineraries (Greyed out for Starter)
     - ❌ Finance & Invoices (Greyed out for Professional)
   - **Card Actions**: `Edit Plan`, `Toggle Active/Inactive`.

#### Modal: "Create / Edit Plan Modal"
- **Form Inputs**:
  - Plan Name (Text Field)
  - Monthly Price (Currency Input)
  - Base Included User Seats (Number Counter, e.g. `5`)
  - Additional Seat Unit Price (Currency Input, e.g. `$5`)
  - Database Option Dropdown (`Shared DB`, `Dedicated DB`)
  - Module Checkbox Grid (`[x] Leads`, `[x] Bookings`, `[ ] Packages`, `[ ] Hotels`, `[ ] Finance`)

---

### Screen 1.2: Tenant Onboarding & Company Management (`/admin/companies`)

#### Purpose
Allows Super Admin to onboard a new subscriber company, select their plan, add extra user seats, and generate company credentials.

#### Key Components & Layout
1. **Header Summary KPIs**:
   - Total Subscribers: `142`
   - Active Subscriptions: `138`
   - Monthly Recurring Revenue (MRR): `$14,250`
   - Action Button: `+ Setup New Company`
2. **Subscribers Data Table**:
   - **Columns**: `Company Name`, `Subdomain`, `Plan Tier`, `Seat Usage` (e.g. `7 / 10 Seats`), `Database Type`, `Status` (Badges: `Active`, `Expired`, `Grace Period`), `Renewal Date`, `Actions` (`Manage Seats`, `Edit`, `Suspend`).

#### Multi-Step Modal: "Setup New Company Account"

```
[ Step 1: Company Profile ] ──► [ Step 2: Plan & Add-on Seats ] ──► [ Step 3: Admin Credentials ]
```

- **Step 1: Company Profile**:
  - Company Name (Text)
  - Subdomain Prefix (e.g. `sunrisetravel.demohandler.in`)
  - Primary Contact Person Name, Phone Number
- **Step 2: Plan & Add-on Seats**:
  - Plan Tier Selection Cards (`Starter`, `Professional`, `Enterprise`)
  - Add-on Seats Stepper Counter: `[ - ]  3 Add-on Seats  [ + ]`
  - Live Calculation Box:
    - Base Seats: 5 Users
    - Add-on Seats: 3 Users
    - **Total Capacity: 8 Users**
    - **Total Monthly Billed: $49 + (3 × $5) = $64 / month**
- **Step 3: Admin Credentials**:
  - Tenant Admin Name & Email
  - Temporary Password & Option to send Welcome Credentials Email.

---

### Screen 1.3: Company Role & Permission Matrix (`/admin/companies/{id}/permissions`)

#### Purpose
Allows Super Admin to customize roles and permission overrides for a specific subscriber company.

#### Key Components & Layout
1. **Header Banner**: `Sunrise Travel Agency` (`Professional Plan`)
2. **Permission Checkbox Matrix Table**:
   - Rows: System Modules (`Leads`, `Bookings`, `Inventory`, `Finance`, `Reports`).
   - Columns: Company Roles (`Manager`, `Sales Executive`, `Operation Team`, `Accounts`).
   - Cells: Toggle Switches / Checkboxes to enable or disable specific actions (`View`, `Create`, `Edit`, `Delete`, `Export`).

---

## Part 2: Subscriber / Tenant Admin Panel (2 Core Screens)

### Screen 2.1: Subscriber Billing & Seat Usage Widget (`/settings/billing`)

#### Purpose
Gives the Company Admin full visibility into their active plan, renewal date, user seat consumption, and ability to purchase add-on seats.

#### Key Components & Layout
1. **Subscription Status Card**:
   - **Active Plan Badge**: `Professional Plan` (`Active`)
   - **Renewal Info**: `Renews on Sep 25, 2026 (In 31 Days)`
   - **Included Modules Pill Tags**: `Leads`, `Packages`, `Inventory`, `Bookings`
2. **User Seat Progress Widget**:
   - Progress Bar: `7 of 10 Seats Used` (70% Filled Bar)
   - Breakdown: `5 Base Included Seats + 5 Add-on Purchased Seats`
   - Action Button: `+ Buy Add-on Seats`

#### Modal: "Purchase Add-on User Seats"
- Stepper Input: `[ - ]  2 Extra Seats  [ + ]`
- Price Calculation: `2 Seats × $5/mo = +$10/month added to your renewal invoice`
- Button: `Confirm & Upgrade Seats`

---

### Screen 2.2: Staff User Directory & Seat Restriction Notice (`/users`)

#### Purpose
Company Admin manages internal staff accounts while staying within their total user seat limit.

#### Key Components & Layout
1. **Header**:
   - Title: "Staff Directory"
   - Seat Counter Pill: `Seats: 8 / 8 Used` (Highlighted in Warning Yellow/Red when full)
   - Primary Action Button: `+ Add Staff Member`
2. **Staff Members Table**:
   - Columns: `Staff Name`, `Email`, `Phone`, `Assigned Role`, `Status`, `Actions`.

#### Modal / Toast: "Seat Limit Reached Warning"
If the Company Admin clicks `+ Add Staff Member` when all seats are consumed (`8 / 8 Used`), display an intuitive Upgrade Modal:
- **Title**: `User Seat Limit Reached`
- **Body**: *"You are currently using all 8 available user seats under your subscription plan."*
- **Action Buttons**:
  - `Buy Add-on Seats ($5/user)`
  - `Upgrade Subscription Plan`
  - `Cancel`

---

## UI Color Palette & Design Tokens

- **Primary Brand Color**: Deep Indigo / Slate (`#4F46E5` / `#312E81`)
- **Success / Active Status**: Emerald Green (`#10B981` / `#D1FAE5`)
- **Warning / Seat Limit Exceeded**: Amber Gold / Crimson (`#F59E0B` / `#EF4444`)
- **Locked / Gated Feature**: Cool Grey with Lock Icon (`#9CA3AF`)
- **Typography**: Inter / Outfit (Clean Modern Sans-Serif)

---

## Summary Checklist for UI Designer

- [ ] Design **Super Admin Plan Cards** (Shared DB vs Dedicated DB badge, base seats, module checklist).
- [ ] Design **3-Step Company Setup Modal** (Profile $\rightarrow$ Plan & Add-on Seats counter $\rightarrow$ Admin credentials).
- [ ] Design **Subscriber Billing Dashboard Card** (Seat consumption progress bar e.g. `7 of 10 Seats Used`).
- [ ] Design **Seat Limit Reached Modal Prompt** (Prompting Company Admin to purchase add-on seats when adding staff).
