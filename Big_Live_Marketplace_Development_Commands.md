# BIG LIVE MARKETPLACE — MASTER DEVELOPMENT SPECIFICATION
Version 1.0 | 2026-11-10 | Status: implementation blueprint

## 0. Mission and boundaries
Build a production-grade, original-brand, OLX-style classified ads marketplace named **Big Live Marketplace**, with a Laravel PHP + MySQL backend, Laravel Blade + Tailwind CSS responsive web app, Flutter Android/iOS app, and Filament-based admin panel. No copying OLX trademarks, visual assets, proprietary code, or copyrighted copy. Implement in working increments; do not claim an unbuilt feature works. All money in integer minor units, all times in UTC, configurable locales/currencies. Start with Pakistan support but model countries and currencies globally.

## 1. Technical decisions
- Backend: Laravel 12+ (choose latest supported stable version at implementation), PHP 8.3+, MySQL 8.0+, Laravel Sanctum personal-access tokens, policies, Form Requests, queues, scheduled tasks.
- Web: Laravel Blade + Livewire + Tailwind CSS, Vite, accessible mobile-first layouts.
- Mobile: Flutter stable, Dart, Riverpod, go_router, Dio, secure token storage, localization.
- Admin: Filament compatible with selected Laravel release; granular RBAC via Spatie Permission.
- Infra: Redis for cache/queues/rate limiting, S3-compatible object storage for photos, image processing workers, WebSockets via Laravel Reverb or compatible provider, FCM/APNs push, transactional email, configurable SMS/OTP provider, maps/geocoding provider, payment adapters per country.
- Search: MySQL FULLTEXT + indexed filters initially, optional Meilisearch/OpenSearch adapter at scale.
- API: /api/v1, JSON envelope, cursor pagination for feeds, idempotency keys for payment-sensitive operations, OpenAPI 3.1 documentation.
- DevOps: Docker Compose local, CI lint/test/static analysis/security scan, staging/production environments, rolling deployments, backups and observability.

## 2. Roles and permission model
Guest: browse public listings, search, view seller public profile.
Buyer: manage account, favorites, chats, offers, reports, notifications.
Seller: buyer permissions + create/edit/pause/renew listings, handle offers, seller analytics.
Verified seller: trust badge only after verification approval; no implicit admin rights.
Business seller: storefront, team members, inventory imports, subscriptions and invoices.
Moderator: review listing/report queues, remove prohibited content, issue warnings; no payment credential access.
Support: tickets, limited account diagnostics, audited actions.
Finance: payment/refund review, invoices, reconciliation; no unrestricted user edits.
Admin: manage categories, content, promotions, roles within scope.
Super admin: privileged configuration and permissions, MFA mandatory, audit all actions.
Enforce permissions on server via policies, gates and role/permission checks, not only UI visibility. Business staff must be tenant-scoped.

## 3. Feature modules and acceptance criteria
### Identity and trust
Email/password, Google OAuth, configurable phone OTP, password reset, session revocation, account deletion/export, device history, optional MFA, verification requests, seller badges, blocked users, consent/version records, age-appropriate account controls.
Acceptance: duplicate verified phone/email disallowed; OTP hashed with TTL and attempt limits; unverified users cannot bypass posting policy; deleted users' personal data handled per retention/legal requirements.
### Marketplace catalog
Hierarchical categories, category-specific required attributes (car make/model/year, property bedrooms/area, phone storage, etc.), listing condition, fixed/negotiable price, free/paid listings, city/region/country, geocoordinates with privacy rounding, shipping/pickup choices, multilingual titles/descriptions, tags, SEO URLs, listing expiration.
Acceptance: only approved active listings appear publicly; category attributes validated against schemas; private exact addresses never exposed by default.
### Listing lifecycle
Draft -> pending_review -> active -> paused/sold/expired/rejected/removed. Seller may edit with re-review rules. Multi-image upload, cover ordering, server-side media validation, image resizing and stripping EXIF/GPS, optional videos, scheduling, renewals, bulk upload for businesses, duplication detection.
Acceptance: ownership enforced; status changes audited; only allowed transitions; image limits configurable.
### Search and discovery
Full text, category, keyword suggestions, city/radius, price min/max, condition, seller type, verified sellers, attributes, newest/price/relevance sorting, saved searches, recommendations (non-sensitive signals), trending categories, recently viewed.
Acceptance: deterministic pagination, safe parameter validation, reasonable index-backed performance.
### Engagement
Favorites, follow seller, saved searches, share, recently viewed, report listing/seller, compare selected items, listing view analytics (deduplicated), contact reveal limits.
### Chat and offers
Conversation per buyer/seller/listing, text/images, delivery/read status, typing presence, offer/counteroffer/accept/reject/expire, attachments scanning, user blocking, message/report moderation, spam prevention. Real-time via authorized private channels and REST fallback.
Acceptance: users can only read their own conversations; blocked users cannot initiate new chats.
### Monetization
Featured placements, category bumps, home-page spots, seller packages, business subscriptions, promotion inventory, promo codes, checkout sessions, provider webhooks, invoices, taxes, refunds, payout ledger only if platform later handles escrow. Do NOT implement escrow or custody of user funds without jurisdiction-specific legal/compliance review.
Acceptance: webhook signatures verified, duplicate webhooks idempotent, server verifies amounts and currency, no payment success based on client callback alone.
### Notifications
In-app, email, push, optional SMS; configurable preferences, templates, quiet hours, unread counts, queue retries, opt-out for marketing.
### Business storefronts
Company profile, logo/banner, verification, branch locations, staff roles, catalog bulk import/export, listing quotas, subscription limits, performance reports.
### Moderation and safety
Prohibited items policies, scam warnings, user/listing/message reports, moderation queues, risk scores, rate limits, appeals, evidence retention, suspensions, audit trail. Protect user privacy and provide clear policy disclosures.
### Operations
CMS pages, banners, FAQ, support tickets, tax/fee config, currencies/exchange-display policy, region settings, feature flags, admin analytics, audit logs, data export/erasure workflows.

## 4. UI screen inventory (64 screens)
### Public and account (1–12)
1 Splash; 2 Onboarding; 3 Choose Country/Language; 4 Welcome; 5 Login; 6 Sign Up; 7 Phone OTP; 8 Forgot Password; 9 Reset Password; 10 Google Sign-In Callback; 11 Terms/Privacy Consent; 12 Account Verification.
### Discovery (13–26)
13 Home Feed; 14 All Categories; 15 Category Landing; 16 Search; 17 Search Suggestions; 18 Filter Sheet; 19 Search Results; 20 Map Results; 21 Listing Details; 22 Fullscreen Gallery; 23 Seller Public Profile; 24 Business Storefront; 25 Favorites; 26 Saved Searches.
### Seller (27–40)
27 Sell Start; 28 Choose Listing Category; 29 Listing Details Form; 30 Category Attributes; 31 Media Upload/Reorder; 32 Price & Negotiation; 33 Location Picker; 34 Listing Preview; 35 Publish Confirmation; 36 My Listings; 37 Edit Listing; 38 Listing Analytics; 39 Promote Listing; 40 Seller Dashboard.
### Communication (41–48)
41 Inbox; 42 Chat Thread; 43 Offer Composer; 44 Offer History; 45 Notifications; 46 Report Listing/User; 47 Blocked Users; 48 Help/Support Tickets.
### Account and billing (49–58)
49 My Profile; 50 Edit Profile; 51 Account Settings; 52 Security & Devices; 53 Verification Center; 54 Subscription Plans; 55 Checkout; 56 Payment History; 57 Invoice Details; 58 Business Team Management.
### Admin (59–64)
59 Admin Overview; 60 Listing Moderation; 61 User & Verification Management; 62 Reports/Disputes; 63 Categories/Attributes/CMS; 64 Payments/Promotions/Analytics.
Additional admin subpages are separate routes/components as necessary; mobile and web share flows but use platform-native navigation.

## 5. Database — MySQL 8 normalized schema
All tables: BIGINT UNSIGNED PK `id` unless noted, timestamps `created_at`, `updated_at`; use foreign keys, indexes, uniqueness constraints, soft deletion where appropriate. All user identifiers indexed. Avoid storing sensitive verification documents in public media.

### Core identities
- users: id, uuid UNIQUE, name, username UNIQUE NULL, email UNIQUE NULL, email_verified_at, phone_e164 UNIQUE NULL, phone_verified_at, password_hash NULL, avatar_media_id NULL, status ENUM(active,suspended,deleted), locale, preferred_currency CHAR(3), last_seen_at, deleted_at.
- oauth_accounts: id, user_id FK, provider, provider_subject, UNIQUE(provider,provider_subject).
- otp_challenges: id, user_id NULL, destination_hash, purpose, code_hash, expires_at, attempts, consumed_at, INDEX(destination_hash,purpose).
- user_devices: id, user_id, device_fingerprint_hash, platform, push_token_encrypted NULL, last_seen_at, revoked_at.
- user_consents: id, user_id, document_type, version, accepted_at, ip_hash.
- roles, permissions, model_has_roles, model_has_permissions, role_has_permissions: Spatie standard.
- user_verifications: id, user_id, type, status, evidence_private_path, reviewer_id NULL, reviewed_at NULL, rejection_reason NULL.
- user_blocks: blocker_id, blocked_id, created_at, UNIQUE(blocker_id,blocked_id).
- seller_follows: follower_id, seller_id, UNIQUE(follower_id,seller_id).

### Geographic and taxonomy
- countries: id, iso2 UNIQUE, name, default_currency, enabled.
- regions: id, country_id, name, code, INDEX(country_id,name).
- cities: id, region_id, name, slug, latitude, longitude, INDEX(region_id,slug).
- categories: id, parent_id NULL, slug UNIQUE, name, icon, sort_order, active, posting_fee_minor DEFAULT 0.
- category_attributes: id, category_id, code, label, data_type, required, filterable, options_json, validation_json, sort_order, UNIQUE(category_id,code).
- brands: id, category_id NULL, name, slug.
- models: id, brand_id, name, slug.

### Listings and discovery
- listings: id, uuid UNIQUE, seller_id, category_id, city_id, title VARCHAR(180), slug VARCHAR(220), description TEXT, price_minor BIGINT NULL, currency CHAR(3), negotiable BOOLEAN, condition_code, status, moderation_status, latitude DECIMAL(10,7) NULL, longitude DECIMAL(10,7) NULL, location_label, address_private TEXT NULL, published_at NULL, expires_at NULL, sold_at NULL, views_count DEFAULT 0, favorites_count DEFAULT 0, deleted_at. INDEX(status,published_at,id), INDEX(category_id,status,price_minor), INDEX(city_id,status,published_at), FULLTEXT(title,description).
- listing_attribute_values: id, listing_id, category_attribute_id, value_text NULL, value_number NULL, value_boolean NULL, value_json NULL, UNIQUE(listing_id,category_attribute_id), attribute-specific search indexes.
- media: id, owner_id, disk, path, mime_type, bytes, width, height, checksum, visibility, created_at.
- listing_media: listing_id, media_id, sort_order, is_cover, PRIMARY KEY(listing_id,media_id).
- listing_status_history: id, listing_id, actor_id NULL, from_status, to_status, reason, created_at.
- listing_views: id, listing_id, viewer_id NULL, visitor_hash NULL, viewed_on DATE, UNIQUE(listing_id,viewer_id,viewed_on) where supported; for anonymous use application dedup.
- favorites: user_id, listing_id, created_at, UNIQUE(user_id,listing_id).
- saved_searches: id, user_id, name, filters_json, alerts_enabled, last_notified_at.
- recent_views: user_id, listing_id, last_viewed_at, UNIQUE(user_id,listing_id).

### Messaging and negotiations
- conversations: id, listing_id, buyer_id, seller_id, last_message_at, status, UNIQUE(listing_id,buyer_id,seller_id).
- messages: id, conversation_id, sender_id, type, body TEXT NULL, media_id NULL, delivered_at NULL, read_at NULL, deleted_at NULL, INDEX(conversation_id,id).
- offers: id, conversation_id, listing_id, buyer_id, seller_id, amount_minor, currency, status, parent_offer_id NULL, expires_at NULL, responded_at NULL.
- conversation_participants: conversation_id, user_id, last_read_message_id NULL, muted_at NULL, archived_at NULL, PRIMARY KEY(conversation_id,user_id).

### Business and revenue
- businesses: id, owner_id, name, slug UNIQUE, description, logo_media_id NULL, banner_media_id NULL, verification_status, status.
- business_members: business_id, user_id, role, UNIQUE(business_id,user_id).
- plans: id, code UNIQUE, name, currency, price_minor, billing_period, listing_quota, benefits_json, active.
- subscriptions: id, business_id NULL, user_id, plan_id, provider, provider_subscription_id NULL, status, starts_at, ends_at, renews_at NULL.
- promotion_products: id, code UNIQUE, name, placement, duration_hours, currency, price_minor, active.
- promotions: id, listing_id, promotion_product_id, order_item_id, starts_at, ends_at, status.
- orders: id, user_id, currency, subtotal_minor, tax_minor, total_minor, status, idempotency_key UNIQUE.
- order_items: id, order_id, item_type, item_reference_id, quantity, unit_price_minor, total_minor.
- payments: id, order_id, provider, provider_payment_id NULL, amount_minor, currency, status, captured_at NULL, UNIQUE(provider,provider_payment_id).
- payment_webhook_events: id, provider, external_event_id, payload_hash, processed_at NULL, UNIQUE(provider,external_event_id).
- refunds: id, payment_id, provider_refund_id NULL, amount_minor, reason, status.
- invoices: id, order_id, invoice_number UNIQUE, issued_at, tax_breakdown_json, pdf_private_path NULL.
- coupons: id, code UNIQUE, discount_type, discount_value, max_redemptions, redeemed_count, starts_at, expires_at.

### Trust, support, platform
- reports: id, reporter_id, target_type, target_id, reason_code, details, status, assigned_moderator_id NULL, resolved_at NULL.
- moderation_actions: id, moderator_id, target_type, target_id, action, reason, metadata_json, created_at.
- appeals: id, report_id NULL, user_id, action_id NULL, statement, status, resolved_by NULL.
- notifications: id UUID PK, user_id, type, data_json, read_at NULL, created_at.
- notification_preferences: user_id PK, email_enabled, push_enabled, sms_enabled, marketing_enabled, quiet_hours_json.
- support_tickets: id, user_id, subject, status, priority, assigned_to NULL.
- support_messages: id, ticket_id, author_id, body, media_id NULL.
- cms_pages: id, slug UNIQUE, locale, title, body, published_at NULL, UNIQUE(slug,locale).
- banners: id, title, media_id, destination_url, placement, starts_at, ends_at, enabled.
- app_settings: key VARCHAR(190) PK, value_json, updated_by NULL.
- audit_logs: id, actor_id NULL, action, subject_type, subject_id, ip_hash, metadata_json, created_at.
- feature_flags: key PK, enabled, rollout_json.

Migration requirements: use ON DELETE RESTRICT for financial audit references; cascade only safe dependent data; seed countries/currencies, taxonomy and test fixtures; create proper CHECK constraints where MySQL supports; use transaction boundaries around state changes.

## 6. REST API contract (v1)
Standard errors: `{ "message": "...", "code": "VALIDATION_ERROR", "errors": {"field":["..."]}, "request_id":"..." }` with correct HTTP status. Successful collections: `{ "data":[], "meta": {"next_cursor":null} }`.

### Auth
POST /auth/register; POST /auth/login; POST /auth/logout; POST /auth/logout-all; POST /auth/refresh-otp; POST /auth/verify-phone; POST /auth/forgot-password; POST /auth/reset-password; GET /auth/google/redirect; GET /auth/google/callback; GET /me; PATCH /me; DELETE /me; GET /me/devices; DELETE /me/devices/{id}; POST /me/consents.
### Catalog
GET /countries; GET /regions?country_id=; GET /cities?region_id=; GET /categories; GET /categories/{slug}; GET /categories/{id}/attributes; GET /brands; GET /models?brand_id=.
### Listings
GET /listings; GET /listings/{id}; POST /listings; PATCH /listings/{id}; DELETE /listings/{id}; POST /listings/{id}/submit; POST /listings/{id}/pause; POST /listings/{id}/resume; POST /listings/{id}/mark-sold; POST /listings/{id}/renew; GET /me/listings; GET /me/listings/{id}/analytics; POST /media/presign; POST /listings/{id}/media; DELETE /listings/{id}/media/{mediaId}; PUT /listings/{id}/media/order.
### Discovery
GET /search/suggestions; GET /search; GET /search/map; GET /trending; GET /saved-searches; POST /saved-searches; PATCH /saved-searches/{id}; DELETE /saved-searches/{id}; GET /favorites; POST /favorites/{listingId}; DELETE /favorites/{listingId}; GET /recent-views.
### Chat and offers
GET /conversations; POST /conversations; GET /conversations/{id}; GET /conversations/{id}/messages; POST /conversations/{id}/messages; POST /conversations/{id}/read; POST /conversations/{id}/mute; GET /conversations/{id}/offers; POST /conversations/{id}/offers; POST /offers/{id}/accept; POST /offers/{id}/reject; POST /offers/{id}/counter.
### Social, safety, support
GET /sellers/{id}; POST /sellers/{id}/follow; DELETE /sellers/{id}/follow; POST /users/{id}/block; DELETE /users/{id}/block; POST /reports; GET /support/tickets; POST /support/tickets; GET /support/tickets/{id}; POST /support/tickets/{id}/messages; GET /notifications; POST /notifications/{id}/read; PATCH /notification-preferences.
### Billing and businesses
GET /plans; POST /subscriptions/checkout; GET /subscriptions/me; GET /promotion-products; POST /promotions/checkout; GET /orders; GET /orders/{id}; GET /invoices/{id}; POST /payments/{provider}/webhook (signature verification, unauthenticated provider endpoint); POST /refund-requests; POST /businesses; GET /businesses/{slug}; PATCH /businesses/{id}; GET /businesses/{id}/members; POST /businesses/{id}/members; DELETE /businesses/{id}/members/{userId}; POST /businesses/{id}/bulk-import.
### Admin (RBAC + audit)
GET /admin/dashboard; GET /admin/users; PATCH /admin/users/{id}/status; GET /admin/verifications; POST /admin/verifications/{id}/decision; GET /admin/listings/review; POST /admin/listings/{id}/approve; POST /admin/listings/{id}/reject; GET /admin/reports; POST /admin/reports/{id}/resolve; CRUD /admin/categories; CRUD /admin/category-attributes; CRUD /admin/plans; CRUD /admin/promotion-products; GET /admin/payments; POST /admin/refunds/{id}/approve; CRUD /admin/cms; GET /admin/audit-logs; GET /admin/analytics; GET/PATCH /admin/settings.
Document request bodies, responses, permissions, pagination and errors for each in generated OpenAPI; keep docs synchronized with tests.

## 7. Web and mobile design system
Brand: Big Live Marketplace, original navy (#13213C), bright blue (#2471C8), warm accent (#F5A524), white backgrounds, accessible contrast, 8px spacing scale, 12px card radii, 44px minimum tap targets, skeleton loading and empty/error states. Responsive web: top nav/search, category rail, listing card grids, detail two-column layout, chat split view, seller dashboard. Mobile: bottom tabs Home, Categories, Sell, Chats, Profile; search bar and filter bottom sheet; optimistic favorites only with rollback. Use translated copy, RTL layouts for Urdu/Arabic, WCAG 2.2 AA target. Admin: data tables, audit history, filterable queues, no public indexing.

## 8. State machines
Listing: draft -> pending_review -> active OR rejected; active -> paused/sold/expired/removed; paused -> pending_review/active according to policy; rejected -> draft after edit; expired -> pending_review on renewal; sold/removed terminal except admin appeal workflow. Every transition policy-checked and recorded.
Offer: pending -> accepted/rejected/countered/expired/cancelled; countered creates new child offer; accepting does not itself transfer money or ownership.
Order: pending -> awaiting_payment -> paid/failed/cancelled; paid -> partially_refunded/refunded; webhook drives paid state. Subscription: trialing/active/past_due/cancelled/expired.
Report: open -> triaged -> investigating -> resolved/dismissed -> appealed -> appeal_resolved.

## 9. Security and privacy requirements
Sanctum tokens in encrypted mobile secure storage; secure HttpOnly SameSite cookies for web; CSRF protection; strict CORS allowlist; password hashing Argon2id/bcrypt; enforce email/phone uniqueness; OTP throttling and expiration; Google OAuth state/nonce; per-route authorization and IDOR tests; signed temporary media URLs; malware/media MIME inspection; strip EXIF; SQL parameterization; escape rendered user content; CSP, HSTS and TLS; server-side file-size/dimension limits; rate-limit login, posting, search, chats, contact reveals and uploads; block spam and duplicate ads; protect payment webhook endpoints; secrets only via environment/secret manager, never commit keys; MFA for privileged admins; append-only security audit; encrypted backups, restore tests; retention and deletion policies; consent versioning; privacy disclosures; security monitoring and incident response. Follow OWASP ASVS and API Security Top 10. Region-specific legal review for consumer, privacy, taxes and restricted goods before international launch.

## 10. Testing and release gates
Backend: PHPUnit/Pest unit, feature and policy tests; migration tests; factories and seeders; API schema contract tests; payment webhook replay tests; queue failure tests. Web: Playwright E2E login -> post -> moderate -> search -> chat -> offer; accessibility scans. Flutter: unit/widget/integration tests on Android and iOS, offline and deep-link tests. Performance: p95 API latency target <400ms for common cached reads under agreed load; Core Web Vitals LCP <=2.5s on target devices; crash-free mobile sessions target >=99.5%. Security: dependency audits, static analysis, SAST, authz/IDOR tests, penetration test before production. Backups: daily encrypted DB snapshots, documented RPO/RTO, quarterly restore drills. No launch until critical/high findings resolved, payment reconciliation passes and privacy terms are published.

## 11. Delivery roadmap (12 phases)
P0 Repo/architecture/CI/Docker/design system; P1 Auth/consent/roles; P2 taxonomy/geography; P3 listings/media/workflows; P4 search/filter/maps; P5 favorites/seller profiles; P6 chat/offers; P7 notifications; P8 business accounts; P9 promotions/payments; P10 admin/moderation/support; P11 hardening/performance/i18n; P12 staging/UAT/production release. Every phase includes migrations, APIs, web pages, Flutter screens, automated tests, seed data, documentation and acceptance demo. Use feature flags for incomplete capabilities.

## 12. COPY-PASTE MASTER AI CODING AGENT COMMAND
You are the lead engineer for Big Live Marketplace. Read this entire requirements file before coding. Build a production-grade original OLX-like classifieds marketplace for web, Android and iOS using Laravel PHP, MySQL, Tailwind CSS/Blade/Livewire, Flutter, Sanctum, Redis, queues and a Filament admin panel. Follow the 64-screen inventory, normalized schema, v1 API contract, RBAC, state machines, moderation, payments and security rules in this document. Start by inspecting the repository and creating a concise implementation plan. Then implement P0 and P1 fully, with runnable code, migrations, seeders, tests, Docker Compose, CI and README. Execute available tests and fix failures. Report exactly what was implemented, how to run it, what was tested and what remains. Continue phase by phase only when preceding acceptance criteria pass. Do not use placeholder APIs, fabricated integrations or hard-coded secrets. Keep business logic in services/actions, use policies and Form Requests, and keep Flutter and web API contracts aligned. Never claim the full app is finished until all phases pass.

### Phase-by-phase agent prompts
**P0**: Scaffold Laravel API + Blade/Tailwind web, Flutter app, Docker Compose MySQL/Redis, CI, OpenAPI, dev seeders, env.example, architecture docs and smoke tests. Ensure local boot works.
**P1**: Implement users, OAuth/OTP interfaces, Sanctum, profiles, consent versioning, RBAC, policy tests, verification and account deletion/export endpoints. Use mocked SMS/email providers in tests.
**P2**: Implement country/region/city/category/attribute models, migrations, seed data, admin category CRUD, public endpoints and validation tests.
**P3**: Implement listing CRUD, draft/review/active/sold state machine, category attributes, secure image upload/processing, moderation hooks, seller pages and mobile listing wizard. Test IDOR and transitions.
**P4**: Implement indexed search, filters, sorting, cursor pagination, location/map browsing, saved searches and performance benchmarks.
**P5**: Implement favorites, follows, public seller/business profiles, analytics and engagement screens with authorization.
**P6**: Implement authorized real-time conversations, media messages, offers/counters, blocking, moderation and E2E chat tests.
**P7**: Implement FCM/APNs integration interfaces, email/in-app notifications, preferences, templates, retries and test doubles.
**P8**: Implement business storefronts, staff roles, listing quotas, bulk import/export and tenant-scoped permissions.
**P9**: Implement packages, checkout provider adapters, promotions, signed idempotent webhooks, invoices, refunds and reconciliation tests. Use sandbox payment credentials only.
**P10**: Implement Filament dashboards, review queues, reports, support, CMS, analytics, audit logs and MFA for admins.
**P11**: Complete Urdu/English localization and RTL, accessibility, performance tuning, security reviews, backups and incident runbooks.
**P12**: Run staging/UAT regression, load/security tests, deployment and rollback rehearsals, store builds, legal/compliance signoff and production readiness checklist.

## 13. Definition of done
Feature is done only if: (1) migration and rollback work, (2) API endpoint documented, (3) authorization enforced, (4) web and Flutter UI work or explicitly marked out of scope for that phase, (5) unit/feature/E2E tests pass, (6) loading/empty/error states handled, (7) localization hooks included, (8) telemetry/logging added, (9) secrets and PII protected, (10) deployment and manual QA steps documented.

## 14. Starter implementation scope
The accompanying starter archive contains a Laravel-ready route/controller/model/migration slice for public listing browsing and authenticated listing creation plus a minimal Flutter listing feed sample and setup instructions. It is **not** the complete application, does not yet include authentication screens, payment processing or moderation UI, and must be integrated into a freshly scaffolded Laravel and Flutter project.
