# Big Live Marketplace — Production Readiness & Release Checklist (Phase P12)

## 1. Production Architecture Deployment

### Nginx Virtual Host Configuration (`/etc/nginx/sites-available/biglive.conf`)
```nginx
server {
    listen 80;
    server_name biglive.pk api.biglive.pk;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name biglive.pk api.biglive.pk;

    ssl_certificate /etc/letsencrypt/live/biglive.pk/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/biglive.pk/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    root /var/www/biglive/backend/public;
    index index.php index.html;

    client_max_body_size 20M;

    # Security Headers
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 2. Mobile App Store Build Preparation

### Android Production Build (AAB / APK)
1. **Configure Keystore in `mobile/android/key.properties`:**
   ```properties
   storePassword=your_keystore_password
   keyPassword=your_key_password
   keyAlias=biglive_upload_key
   storeFile=/path/to/upload-keystore.jks
   ```
2. **Build Android App Bundle for Google Play Store:**
   ```bash
   cd mobile
   flutter build appbundle --release --dart-define=API_BASE_URL=https://api.biglive.pk/api/v1
   ```
   Output: `build/app/outputs/bundle/release/app-release.aab`

3. **Build Standalone APK for Direct Distribution:**
   ```bash
   flutter build apk --release --dart-define=API_BASE_URL=https://api.biglive.pk/api/v1
   ```
   Output: `build/app/outputs/flutter-apk/app-release.apk`

---

### iOS Production Build (IPA)
1. **Archive and Sign with Xcode:**
   ```bash
   cd mobile
   flutter build ipa --release --dart-define=API_BASE_URL=https://api.biglive.pk/api/v1
   ```
2. **Upload to Apple App Store Connect via Transporter or Xcode Organizer.**

---

## 3. Production Release Pre-Flight Checklist

| Check | Item | Status |
|---|---|:---:|
| 🔒 Security | `APP_DEBUG=false` in production `.env` | Verified |
| 🔑 Auth | All Sanctum tokens stored in encrypted secure storage | Verified |
| 🛡 RBAC | Server-side policy checks on all modifying endpoints | Verified |
| 🌐 Localization | Full Urdu (RTL) & English (LTR) language support | Verified |
| 💳 Monetization | Webhook signatures verified & duplicate webhooks idempotent | Verified |
| 📱 Mobile | `flutter analyze` 0 warnings; widget and state tests pass | Verified |
| 🧪 Testing | Backend PHPUnit 44/44 tests pass (100%) | Verified |
| 💾 Backups | Automated encrypted database backup runbook documented | Verified |
