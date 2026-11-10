# Big Live Marketplace — Operational Runbook

## 1. Background Workers & Queues
Big Live Marketplace uses Redis-backed asynchronous queue workers for:
- Image processing and thumbnail resizing.
- Push notification (FCM/APNs) delivery.
- Transactional email delivery.
- Payment webhook reconciliation.

### Supervisord Configuration (`/etc/supervisor/conf.d/biglive-worker.conf`)
```ini
[program:biglive-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/biglive/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/biglive-worker.log
stopwaitsecs=3600
```

---

## 2. Scheduled Tasks (Cron)
Add the following single entry to `crontab -e`:
```bash
* * * * * cd /var/www/biglive/backend && php artisan schedule:run >> /dev/null 2>&1
```
Tasks scheduled automatically:
- Expire active listings older than 30 days.
- Deactivate expired promotional placements (`featured_7d`, `bump_3d`).
- Prune revoked personal access tokens.
- Dispatch saved search alert digests.

---

## 3. Log Rotation & Monitoring
Logs are stored in `storage/logs/laravel.log`. Daily channel rotation is recommended:
```env
LOG_CHANNEL=daily
LOG_DAILY_DAYS=14
```
For centralized observability, configure Sentry or Datadog via standard logging channels.
