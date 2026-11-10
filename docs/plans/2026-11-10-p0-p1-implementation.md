# Big Live Marketplace — Phase P0 & P1 Implementation Plan

> **For Claude / Agent:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Scaffold the production-grade foundation (P0) for Big Live Marketplace across backend (Laravel 12/PHP 8.3), mobile (Flutter), web (Blade/Tailwind), Docker, CI, and OpenAPI; then implement Phase P1 covering full authentication, Sanctum token management, RBAC with Spatie Permission, OTP challenge services, consent versioning, user profiles, verification workflows, account deletion/export, and automated tests.

**Architecture:** Layered modular Laravel backend (`backend/`) with strict Form Requests, Action classes, Policies, standard JSON envelopes, and SQLite in-memory test suite alongside MySQL production schemas. Flutter cross-platform client (`mobile/`) organized feature-first with Riverpod, go_router, Dio, and secure token storage. Full Docker Compose and GitHub Actions CI configuration.

**Tech Stack:** PHP 8.3, Laravel 12, MySQL 8.0, Redis 7.2, Laravel Sanctum, Spatie Laravel-Permission, Tailwind CSS, Blade, Flutter 3.x, Dart 3.x, Riverpod, Dio, PHPUnit.

---

## Phase P0: Scaffolding, Architecture, Docker, CI, and Core Foundations

### Task 1: Scaffolding the Laravel 12 Backend Project Structure
**Files:**
- Create: `backend/composer.json`
- Create: `backend/artisan`
- Create: `backend/bootstrap/app.php`
- Create: `backend/config/app.php`
- Create: `backend/config/database.php`
- Create: `backend/config/auth.php`
- Create: `backend/config/sanctum.php`
- Create: `backend/config/cors.php`
- Create: `backend/.env.example`
- Create: `backend/phpunit.xml`

**Step 1:** Run composer create-project or assemble the standard Laravel 12 skeleton with Sanctum and Spatie Permission dependencies.
**Step 2:** Configure SQLite `:memory:` for testing in `phpunit.xml` and MySQL 8 for development in `.env.example`.
**Step 3:** Verify `php artisan --version` executes cleanly without errors.

---

### Task 2: Standard API Response Envelope & Health Endpoint
**Files:**
- Create: `backend/app/Http/Responses/ApiResponse.php`
- Create: `backend/app/Http/Controllers/Api/HealthController.php`
- Create: `backend/routes/api.php`
- Test: `backend/tests/Feature/HealthCheckTest.php`

**Step 1: Write failing test**
```php
public function test_api_health_check_returns_success_envelope(): void
{
    $response = $this->getJson('/api/v1/health');
    $response->assertStatus(200)
             ->assertJson([
                 'status' => 'healthy',
                 'service' => 'Big Live Marketplace API',
                 'version' => '1.0.0'
             ]);
}
```
**Step 2: Run test to verify failure**
**Step 3: Implement `ApiResponse` trait/helper and `HealthController`**
**Step 4: Run test to verify pass**

---

### Task 3: Integrate Starter Listing Slice with Enhanced Architecture
**Files:**
- Create: `backend/app/Models/Listing.php`
- Create: `backend/app/Http/Controllers/Api/ListingController.php`
- Create: `backend/app/Http/Requests/CreateListingRequest.php`
- Create: `backend/database/migrations/2026_11_10_000001_create_listings_table.php`
- Test: `backend/tests/Feature/ListingSmokeTest.php`

**Step 1:** Integrate and adapt starter listing model, migration, Form Request validation, and cursor pagination.
**Step 2:** Run migration and feature tests under SQLite in-memory.

---

### Task 4: Scaffold Flutter Mobile Application
**Files:**
- Create: `mobile/pubspec.yaml`
- Create: `mobile/lib/main.dart`
- Create: `mobile/lib/core/network/api_client.dart`
- Create: `mobile/lib/core/theme/app_theme.dart`
- Create: `mobile/lib/core/routes/app_router.dart`
- Test: `mobile/test/widget_test.dart`

**Step 1:** Set up `pubspec.yaml` with `flutter_riverpod`, `go_router`, `dio`, `flutter_secure_storage`.
**Step 2:** Implement Big Live branding colors (#13213C Navy, #2471C8 Blue, #F5A524 Accent) and 8px grid design system.
**Step 3:** Implement Dio API client with environment-aware `API_BASE_URL` and auth token interceptor.
**Step 4:** Run `flutter test` to verify mobile baseline passes.

---

### Task 5: Web Foundation with Tailwind CSS & Responsive Layout
**Files:**
- Create: `backend/resources/views/layouts/app.blade.php`
- Create: `backend/resources/views/welcome.blade.php`
- Create: `backend/resources/css/app.css`
- Create: `backend/tailwind.config.js`
- Create: `backend/package.json`

**Step 1:** Configure Tailwind CSS with Big Live color palette and typography.
**Step 2:** Build accessible navigation bar, hero search placeholder, category rail skeleton, and footer.

---

### Task 6: Docker Compose, OpenAPI Specification & CI Pipelines
**Files:**
- Create: `docker-compose.yml`
- Create: `docs/openapi/v1.yaml`
- Create: `.github/workflows/ci.yml`
- Create: `README.md`

**Step 1:** Define Docker Compose with MySQL 8.0, Redis 7.2 Alpine, and Mailpit.
**Step 2:** Write OpenAPI 3.1 specification for `/api/v1` base endpoints.
**Step 3:** Configure GitHub Actions for PHP 8.3 tests and Flutter tests.

---

## Phase P1: Identity, RBAC, Authentication, Profiles, and Trust

### Task 7: Core Identity & Trust Database Migrations
**Files:**
- Create: `backend/database/migrations/2026_11_10_000010_create_users_table.php`
- Create: `backend/database/migrations/2026_11_10_000011_create_oauth_accounts_table.php`
- Create: `backend/database/migrations/2026_11_10_000012_create_otp_challenges_table.php`
- Create: `backend/database/migrations/2026_11_10_000013_create_user_devices_table.php`
- Create: `backend/database/migrations/2026_11_10_000014_create_user_consents_table.php`
- Create: `backend/database/migrations/2026_11_10_000015_create_user_verifications_table.php`
- Create: `backend/database/migrations/2026_11_10_000016_create_user_blocks_table.php`
- Create: `backend/database/migrations/2026_11_10_000017_create_seller_follows_table.php`
- Create: `backend/database/migrations/2026_11_10_000018_create_permission_tables.php`
- Test: `backend/tests/Feature/DatabaseMigrationTest.php`

**Step 1:** Write migrations matching Section 5 schema (all constraints, indexes, unique constraints, and soft deletes).
**Step 2:** Run `php artisan migrate` and verify zero errors on both SQLite and MySQL schemas.

---

### Task 8: User, Device, OTP, and Consent Models & Relationships
**Files:**
- Create: `backend/app/Models/User.php`
- Create: `backend/app/Models/UserDevice.php`
- Create: `backend/app/Models/OtpChallenge.php`
- Create: `backend/app/Models/UserConsent.php`
- Create: `backend/app/Models/UserVerification.php`
- Create: `backend/app/Models/UserBlock.php`
- Create: `backend/app/Models/SellerFollow.php`
- Create: `backend/database/seeders/RoleAndPermissionSeeder.php`
- Test: `backend/tests/Unit/UserModelTest.php`

**Step 1:** Implement models with `$fillable`, casts (dates, booleans, encrypted tokens), UUID auto-generation, and relationships.
**Step 2:** Implement `RoleAndPermissionSeeder` with Spatie roles: `buyer`, `seller`, `verified_seller`, `business_seller`, `moderator`, `support`, `finance`, `admin`, `super_admin`.
**Step 3:** Test role assignments and permissions in unit tests.

---

### Task 9: Authentication & OTP Services
**Files:**
- Create: `backend/app/Services/AuthService.php`
- Create: `backend/app/Services/OtpService.php`
- Create: `backend/app/Http/Requests/Auth/RegisterRequest.php`
- Create: `backend/app/Http/Requests/Auth/LoginRequest.php`
- Create: `backend/app/Http/Requests/Auth/SendOtpRequest.php`
- Create: `backend/app/Http/Requests/Auth/VerifyOtpRequest.php`
- Create: `backend/app/Http/Controllers/Api/AuthController.php`
- Test: `backend/tests/Feature/AuthTest.php`

**Step 1: Write failing feature tests**
- Test registration with email & password creates user and returns Sanctum token.
- Test duplicate email registration rejection.
- Test login with valid/invalid credentials.
- Test OTP creation with hashing, TTL, and max attempt enforcement.
- Test logout and token revocation (`/auth/logout`, `/auth/logout-all`).
**Step 2: Implement `OtpService` and `AuthService`**
**Step 3: Implement `AuthController`**
**Step 4: Run tests and ensure 100% pass**

---

### Task 10: User Profile, Consents, Devices & Account Deletion
**Files:**
- Create: `backend/app/Http/Controllers/Api/ProfileController.php`
- Create: `backend/app/Http/Controllers/Api/UserDeviceController.php`
- Create: `backend/app/Http/Controllers/Api/ConsentController.php`
- Create: `backend/app/Http/Controllers/Api/VerificationController.php`
- Create: `backend/app/Http/Requests/UpdateProfileRequest.php`
- Create: `backend/app/Http/Requests/StoreConsentRequest.php`
- Create: `backend/app/Http/Requests/SubmitVerificationRequest.php`
- Test: `backend/tests/Feature/ProfileAndSecurityTest.php`

**Step 1: Write failing tests**
- `GET /api/v1/me` returns authenticated profile with roles and verification badge status.
- `PATCH /api/v1/me` updates allowed fields.
- `DELETE /api/v1/me` performs soft delete and revokes tokens.
- `GET /api/v1/me/devices` lists registered user devices; `DELETE /api/v1/me/devices/{id}` revokes device.
- `POST /api/v1/me/consents` records terms/privacy consent version and timestamp.
- `POST /api/v1/me/verification` submits KYC verification request with private evidence storage.
**Step 2: Implement controllers, requests, and policies**
**Step 3: Run tests and ensure green**

---

### Task 11: Mobile Flutter Auth & State Integration
**Files:**
- Create: `mobile/lib/features/auth/models/user_model.dart`
- Create: `mobile/lib/features/auth/providers/auth_provider.dart`
- Create: `mobile/lib/features/auth/screens/login_screen.dart`
- Create: `mobile/lib/features/auth/screens/register_screen.dart`
- Create: `mobile/lib/features/auth/screens/otp_screen.dart`
- Create: `mobile/lib/features/profile/screens/profile_screen.dart`
- Test: `mobile/test/auth_provider_test.dart`

**Step 1:** Implement `User` and `AuthToken` models with JSON serialization.
**Step 2:** Implement Riverpod `AuthNotifier` backed by `flutter_secure_storage` and Dio API client.
**Step 3:** Implement mobile Login, Register, Phone OTP, and Profile screens with responsive design and Big Live design system.
**Step 4:** Run Flutter unit and widget tests.

---

### Task 12: Phase P0 & P1 Integration Verification & Documentation
**Files:**
- Modify: `docs/openapi/v1.yaml` (document all P1 endpoints)
- Modify: `README.md` (exact commands to run migrations, seeds, tests, web server, and Flutter app)
- Run: Complete backend PHPUnit test suite
- Run: Complete Flutter test suite
