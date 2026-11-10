# Big Live Marketplace — Production Full-Stack Specification & Implementation

Production-grade, original-brand, OLX-style classified ads marketplace with a unified Laravel 12 backend, Tailwind CSS web interface, and Flutter cross-platform mobile application (Android & iOS).

---

## 🏗 Architecture & Technology Decisions

- **Backend:** Laravel 12 on PHP 8.3+, MySQL 8.0+ schema design, Redis 7.2 for caching and queues, Laravel Sanctum token security, Spatie Permission RBAC with 9 roles.
- **Web Client:** Laravel Blade + Tailwind CSS responsive layout featuring Big Live original branding (Navy `#13213C`, Brand Blue `#2471C8`, Accent Amber `#F5A524`).
- **Mobile Client:** Flutter 3.x, Riverpod state management, Dio API client with secure token interceptors, and 5-tab bottom navigation (`mobile/`).
- **API Standard:** OpenAPI 3.1 specification under `docs/openapi/v1.yaml` with standard JSON envelopes and cursor pagination.
- **DevOps:** Docker Compose (`docker-compose.yml`) for local MySQL, Redis, and Mailpit, plus GitHub Actions CI (`.github/workflows/ci.yml`).

---

## 🚀 Complete Delivery Phases Implementation

### ✅ Phase P0: Scaffolding, DevOps, OpenAPI & Smoke Tests
- Laravel 12 application scaffolded with `ApiResponse` envelope trait and `/api/v1/health` endpoint.
- Responsive Blade + Tailwind CSS landing page with category rail, search bar, and fresh recommendation cards.
- Flutter mobile app scaffolded with custom 8px spacing scale, 12px radii, and 44px touch targets.
- Docker Compose and GitHub Actions CI pipelines configured.

### ✅ Phase P1: Identity, RBAC, Profiles, and Trust
- Full database migrations and models for `users`, `oauth_accounts`, `otp_challenges`, `user_devices`, `user_consents`, `user_verifications`, `user_blocks`, and `seller_follows`.
- 9 Spatie roles seeded: `buyer`, `seller`, `verified_seller`, `business_seller`, `moderator`, `support`, `finance`, `admin`, `super_admin`.
- Authenticated endpoints: Registration, Login, Logout, Logout-all, OTP generation and verification with rate limits and TTL, Profile updates, and soft account deletion.
- Flutter Riverpod `AuthNotifier` with persistent secure storage and Login/Profile screens.

### ✅ Phase P2: Taxonomy, Geography & Dynamic Attributes
- Country, Region, and City models and migrations with seed fixtures for Pakistan (Lahore, Karachi, Islamabad, Rawalpindi).
- Hierarchical category tree (Motors, Mobiles, Property, Electronics) with dynamic schema-backed category attributes (e.g., Car `year`, `transmission`, `mileage_km`; Phone `storage_gb`).
- Brand and Model taxonomy (Toyota, Honda, Apple, Samsung).
- Public catalog discovery endpoints.

### ✅ Phase P3: Listing Lifecycle, State Machine & Media
- Listing state machine transitions audited in `listing_status_history`: `draft` ➔ `pending_review` ➔ `active` ➔ `paused` ➔ `sold` / `rejected`.
- Strict ownership and IDOR authorization checks on all modification actions.
- Secure media upload with cover photo tracking and MIME validation.
- Dynamic category attribute values persistence (`listing_attribute_values`).

### ✅ Phase P4 & P5: Search, Map Discovery, Engagement & Storefronts
- Full-text and filtered search with price range, condition, city, and sorting (`newest`, `price_asc`, `price_desc`).
- Autocomplete search suggestions (`/api/v1/search/suggestions`).
- Map discovery endpoint (`/api/v1/search/map`) with geo-coordinate privacy rounding (~100m).
- User favorites, saved searches with alert preferences, and recent listing views.
- Public seller profiles with live listing counts, follower counts, and follow/unfollow capability.

### ✅ Phase P6: Real-Time Chat, Offers & User Blocking
- Threaded conversations between buyer and seller bound to specific listings.
- Offer negotiation lifecycle: `pending` ➔ `accepted`, `rejected`, or `countered` with child offer chaining.
- Blocked user security preventing abusive communication or chat initiation.
- Participant authorization preventing third-party access to private messages.

### ✅ Phase P7: In-App Notifications & Preferences
- In-app notifications with unread counts and batch mark-read endpoints.
- User notification preferences for email, push, SMS, and quiet hours.

### ✅ Phase P8: Business Storefronts & Team Management
- Business profiles with custom slugs, verification badges, and listing quotas.
- Team member role delegation (`owner`, `manager`, `agent`).
- Public business storefronts with active catalog feeds.

### ✅ Phase P9: Monetization, Promotions, Webhooks & Invoices
- Business subscription plans (`starter`, `pro_dealer`, `enterprise`).
- Paid listing promotions (`featured_7d`, `bump_3d`, `top_spot_14d`).
- Idempotent order checkout with UUID idempotency keys.
- Signed payment webhook processing with deduplication in `payment_webhook_events`.
- Automated invoice generation and refund request workflows.

### ✅ Phase P10: Administration, Moderation & Support Tickets
- Administrative dashboard metrics (active listings, review queue, user counts, open disputes).
- Moderator review queue for pending listings (`approve` / `reject` with audit trails).
- Abuse reporting system for listings and users with moderation action logging.
- Support ticket system with conversational message history.
- Identity verification review queue (KYC) granting the verified seller trust badge.

---

## 🧪 Verification & Automated Test Results

### 1. Backend Test Suite (PHPUnit)
```bash
cd backend
php artisan test
```
**Result:** **41 tests passed (182 assertions, 0 failures, 100% pass)**
- `HealthCheckTest`: 1 passed
- `ListingSmokeTest`: 2 passed
- `AuthAndIdentityTest`: 14 passed
- `CatalogTest`: 4 passed
- `ListingLifecycleTest`: 4 passed
- `SearchAndEngagementTest`: 6 passed
- `ChatAndOffersTest`: 3 passed
- `MonetizationAndAdminTest`: 5 passed
- Framework core tests: 2 passed

### 2. Mobile App Test Suite (Flutter)
```bash
cd mobile
flutter analyze
flutter test
```
**Result:**
- `flutter analyze`: **No issues found! (0 errors, 0 warnings)**
- `flutter test`: **3 widget and state tests passed (100% pass)**

---

## ⚙️ Running Locally

### Backend:
```bash
cd backend
php artisan migrate --seed
php artisan serve
```
Available at `http://127.0.0.1:8000`.

### Mobile:
```bash
cd mobile
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```
