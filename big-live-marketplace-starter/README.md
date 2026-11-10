# Big Live Marketplace — Starter Slice
This archive is an **integration starter**, not a runnable full project and not a finished OLX clone.

## Backend setup
1. Install PHP 8.3+, Composer, MySQL 8+, Node.js and a Laravel 12+ compatible environment.
2. `composer create-project laravel/laravel backend-app` then `cd backend-app`.
3. `composer require laravel/sanctum` and `php artisan install:api` if routes/api.php is not enabled; inspect resulting routes before replacing them.
4. Copy the `backend/` folder contents from this archive into `backend-app/`, merging directories. Back up any existing routes first.
5. Set your DB connection and APP_KEY in `.env`; never commit `.env`.
6. `php artisan migrate` then `php artisan serve`.
7. To seed a visible listing, create a user and insert a listing with `status=active` using a local dev seeder/Tinker. New listings created through POST remain drafts, intentionally not public until moderation is implemented.
8. GET `http://127.0.0.1:8000/api/v1/listings` should return an empty JSON data array until an active listing exists.

## Mobile setup
1. `flutter create marketplace_mobile` then `cd marketplace_mobile`.
2. `flutter pub add http`.
3. Replace `lib/main.dart` with `mobile/lib/main.dart` from this archive.
4. For Android emulator: `flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000`.
5. For iOS simulator/physical device use a reachable HTTPS API endpoint and proper network permissions. Android emulator HTTP is development-only; do not disable TLS protections in production.

## Known intentional gaps
- No login/registration UI or token handling; POST /listings requires an authenticated Sanctum user.
- Taxonomy FK constraints, media, moderation approval, search escaping tests, policies, chat, admin, payment and notifications are not yet implemented.
- `price_minor` assumes currency exponent 2 in demo display; replace with ISO 4217 exponent-aware formatting.
- API pagination response is Laravel's native cursor paginator; normalize to documented envelope in later phases.
- No claims of successful runtime tests; this archive was prepared as source scaffolding.
