# Big Live Marketplace

Production-grade, original-brand, OLX-style classified ads marketplace with a shared Laravel 12 backend, Tailwind CSS responsive web interface, and Flutter cross-platform mobile application (Android & iOS).

---

## 🏗 Architecture & Stack Overview

- **Backend:** Laravel 12, PHP 8.3+, MySQL 8.0+, Redis 7.2, Laravel Sanctum, Spatie Permission.
- **Web Client:** Laravel Blade + Tailwind CSS (responsive mobile-first layout with Big Live navy `#13213C`, brand blue `#2471C8`, and accent amber `#F5A524`).
- **Mobile Client:** Flutter 3.x, Riverpod state management, go_router, Dio API client, and flutter_secure_storage.
- **Contract:** OpenAPI 3.1 specification under `docs/openapi/v1.yaml`.
- **Infrastructure:** Docker Compose (`docker-compose.yml`) for MySQL, Redis, and Mailpit.

---

## 🚀 Current Implementation Status

### ✅ Phase P0: Core Scaffolding & Foundations
- **Backend:** Laravel 12 scaffolded with full standard response envelope (`ApiResponse`), health check (`GET /api/v1/health`), and starter listing endpoints with cursor pagination.
- **Web:** Responsive Blade + Tailwind CSS landing page (`/`) featuring category navigation, search bar, and fresh recommendations feed.
- **Mobile:** Flutter application (`mobile/`) configured with Big Live theme, 8px grid design system, Dio API client with auth interceptors, and 5-tab bottom navigation (Home, Categories, Sell, Chats, Profile).
- **CI & DevOps:** GitHub Actions workflow (`.github/workflows/ci.yml`), `docker-compose.yml`, and OpenAPI 3.1 specification.
- **Tests:** 100% automated test pass on both backend and Flutter mobile app.

### ✅ Phase P1: Identity, RBAC, Profiles, and Trust
- **Database Schema:** Full migrations for `users`, `oauth_accounts`, `otp_challenges`, `user_devices`, `user_consents`, `user_verifications`, `user_blocks`, `seller_follows`, and Spatie roles/permissions.
- **Authentication Endpoints:**
  - `POST /api/v1/auth/register` (Email + password + phone + terms consent + device registration)
  - `POST /api/v1/auth/login` (Email or phone authentication with personal access tokens)
  - `POST /api/v1/auth/logout` (Revoke current device token)
  - `POST /api/v1/auth/logout-all` (Revoke all tokens across all devices)
  - `POST /api/v1/auth/otp/send` & `POST /api/v1/auth/otp/verify` (Hashed OTPs with TTL, max attempt limits, and verified timestamps)
- **User & Security Management:**
  - `GET /api/v1/me`, `PATCH /api/v1/me`, `DELETE /api/v1/me` (Account soft deletion & session teardown per retention policies)
  - `GET /api/v1/me/devices`, `DELETE /api/v1/me/devices/{id}`
  - `POST /api/v1/me/consents`
  - `POST /api/v1/me/verification` (CNIC, Passport, Trade License verification workflows)
- **Mobile Client Auth:** Flutter `AuthNotifier` with Riverpod, secure storage, and integrated Profile screen.

---

## 🧪 Running Automated Tests

### Backend Test Suite (PHPUnit / SQLite in-memory)
```bash
cd backend
php artisan test
```

### Mobile Test Suite (Flutter Widget & Unit Tests)
```bash
cd mobile
flutter test
```

---

## 🛠 Local Development Setup

### 1. Backend Setup
```bash
cd backend
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
Backend API will be running at `http://127.0.0.1:8000`.

### 2. Mobile Setup
```bash
cd mobile
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

### 3. Docker Infrastructure (Optional)
```bash
docker compose up -d
```
Starts MySQL 8.0 (`localhost:3306`), Redis 7.2 (`localhost:6379`), and Mailpit Web UI (`http://localhost:8025`).

---

## 📋 Next Delivery Phase

- **Phase P2:** Taxonomy & Geography (Country / Region / City / Category / Dynamic Category Attributes models, migrations, seeds, admin CRUD, and public filter endpoints).
