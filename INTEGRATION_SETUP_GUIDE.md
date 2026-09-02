# Multi-Channel Lead Webhook Setup & Integration Guide

## Overview for Developers

This guide provides **step-by-step instructions** for connecting external lead sources (**Meta Facebook/Instagram Ads**, **Google Ads**, **WhatsApp Business Cloud API**, **Website Forms**, and **CSV Uploads**) to the TMS CRM backend.

---

## 1. Understanding Environment Variables (`.env`)

Add the following keys to your server `.env` file:

```env
# Meta / Facebook & Instagram Ads Webhook & Graph API Keys
META_VERIFY_TOKEN="safarsystem_meta_token_2026"
META_APP_ID="YOUR_META_APP_ID"
META_APP_SECRET="YOUR_META_APP_SECRET"
META_ACCESS_TOKEN="YOUR_META_SYSTEM_USER_ACCESS_TOKEN"

# Google Ads Lead Form Webhook Keys
GOOGLE_WEBHOOK_KEY="safarsystem_google_key_2026"
GOOGLE_CLIENT_ID="YOUR_GOOGLE_CLIENT_ID"
GOOGLE_CLIENT_SECRET="YOUR_GOOGLE_CLIENT_SECRET"

# WhatsApp Business Cloud API Credentials
WHATSAPP_VERIFY_TOKEN="safarsystem_whatsapp_token_2026"
WHATSAPP_API_TOKEN="YOUR_WHATSAPP_PERMANENT_ACCESS_TOKEN"
WHATSAPP_PHONE_NUMBER_ID="YOUR_WHATSAPP_PHONE_NUMBER_ID"
WHATSAPP_BUSINESS_ACCOUNT_ID="YOUR_WHATSAPP_BUSINESS_ACCOUNT_ID"
```

### What is `META_VERIFY_TOKEN` (e.g. `safarsystem_meta_token_2026`)?
> **Key Concept**: `META_VERIFY_TOKEN` is a secret verification phrase **YOU (the developer) choose**.
> It acts like a shared password between Meta Developers App settings and your backend server.
> 
> 1. You type `safarsystem_meta_token_2026` into the Facebook Webhook settings box on Meta Portal.
> 2. When Meta verifies your URL, Meta sends `hub.verify_token=safarsystem_meta_token_2026` in a `GET` request.
> 3. Your backend checks if `received_token === env('META_VERIFY_TOKEN')`. If it matches, your backend returns the challenge code and Meta authorizes your webhook URL.

---

## 2. Step-by-Step Meta (Facebook & Instagram) Setup

### Step 2.1: Get Meta App Credentials
1. Log in to [Meta for Developers](https://developers.facebook.com/).
2. Click **My Apps** $\rightarrow$ **Create App** $\rightarrow$ Select **Business** type.
3. In App Dashboard $\rightarrow$ **Settings** $\rightarrow$ **Basic**:
   - Copy **App ID** $\rightarrow$ Set `META_APP_ID` in `.env`.
   - Copy **App Secret** $\rightarrow$ Set `META_APP_SECRET` in `.env`.
4. Go to **Business Manager** $\rightarrow$ **System Users** $\rightarrow$ Generate Permanent Access Token with `leads_retrieval` & `pages_manage_ads` permissions $\rightarrow$ Set `META_ACCESS_TOKEN` in `.env`.

### Step 2.2: Configure Webhook Callback in Meta App
1. In Meta App Dashboard, add the **Webhooks** product.
2. Select **Page** or **Leadgen** object from dropdown $\rightarrow$ Click **Subscribe to this object**.
3. Fill in the fields:
   - **Callback URL**: `https://yourdomain.com/api/v1/webhooks/leads/meta`
   - **Verify Token**: `safarsystem_meta_token_2026` (must match `META_VERIFY_TOKEN` in `.env`)
4. Click **Verify and Save**. The backend handles the `GET` handshake automatically and returns HTTP `200`.
5. Under Subscriptions, enable **`leadgen`**.

### Step 2.3: Test Meta Webhook with Testing Tool
1. Open official [Facebook Lead Ads Testing Tool](https://developers.facebook.com/tools/lead-ads-testing/).
2. Select your **Facebook Page** and **Lead Form**.
3. Click **Create Lead** (creates a dummy lead submission).
4. Click **Track Status** / **Send Webhook**.
5. Check backend database (`leads` table) — lead appears with `campaign_source: "Meta Facebook/Instagram Ads"` and `status: "NEW_LEAD"`.

---

## 3. Step-by-Step Google Ads Lead Form Setup

### Step 3.1: Configure Webhook in Google Ads Manager
1. Log in to [Google Ads Manager](https://ads.google.com/).
2. Navigate to **Assets** $\rightarrow$ **Lead Form Extensions**.
3. Create or Edit a Lead Form extension.
4. Scroll to **Export leads from Google Ads** section $\rightarrow$ Select **Other lead integration options**.
5. Fill in the fields:
   - **Webhook URL**: `https://yourdomain.com/api/v1/webhooks/leads/google`
   - **Key**: `safarsystem_google_key_2026` (matches `GOOGLE_WEBHOOK_KEY` in `.env`)
6. Click **Send Test Data**.
7. Confirm HTTP `201` response with `campaign_source: "Google Search Ads"`.

---

## 4. Step-by-Step WhatsApp Business Cloud API Setup

### Step 4.1: Obtain WhatsApp Credentials
1. In [Meta Developers Portal](https://developers.facebook.com/), add the **WhatsApp** product to your app.
2. Under **WhatsApp** $\rightarrow$ **API Setup**:
   - Copy **Phone Number ID** $\rightarrow$ Set `WHATSAPP_PHONE_NUMBER_ID` in `.env`.
   - Copy **WhatsApp Business Account ID** $\rightarrow$ Set `WHATSAPP_BUSINESS_ACCOUNT_ID` in `.env`.
   - Copy **Access Token** $\rightarrow$ Set `WHATSAPP_API_TOKEN` in `.env`.

### Step 4.2: Configure WhatsApp Webhook
1. Under **WhatsApp** $\rightarrow$ **Configuration**:
   - **Callback URL**: `https://yourdomain.com/api/v1/webhooks/leads/whatsapp`
   - **Verify Token**: `safarsystem_whatsapp_token_2026` (matches `WHATSAPP_VERIFY_TOKEN` in `.env`)
2. Click **Verify and Save**.
3. Under Webhook fields, subscribe to **`messages`**.

---

## 5. Step-by-Step Website Forms Integration

To capture leads from external landing pages, popup forms, or custom websites:

- **Endpoint**: `POST https://yourdomain.com/api/v1/webhooks/leads/website`
- **Method**: `POST`
- **Content-Type**: `application/json`
- **Payload Example**:
  ```json
  {
    "full_name": "Amit Patel",
    "phone": "9876543213",
    "email": "amit@example.com",
    "destination": "Kerala",
    "travel_date": "2026-10-15",
    "pax_adults": 2,
    "pax_children": 1,
    "budget": 45000,
    "campaign_source": "Website Landing Page"
  }
  ```

---

## 6. CSV Lead Import & Export Workflow

1. **Download Sample Template**: `GET /api/v1/leads/sample-csv`
   - Streams `leads_sample_template.csv` containing pre-formatted headers (`Name`, `Phone`, `Email`, `Destination`, `Budget`, `Notes`).
2. **Bulk Upload CSV**: `POST /api/v1/leads/import` (`multipart/form-data` with `file` field).
   - Skips duplicate phone numbers within the tenant company automatically.
3. **Export Leads to CSV**: `GET /api/v1/leads/export-csv` (Supports optional filters `search` & `status`).
