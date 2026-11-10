# Big Live Marketplace — Database Backup & Disaster Recovery Runbook

## 1. Backup Strategy
- **RPO (Recovery Point Objective):** < 1 hour.
- **RTO (Recovery Time Objective):** < 15 minutes.
- **Schedule:** Full encrypted daily snapshot at 02:00 UTC + continuous binary log archiving.

### Automated Encrypted Backup Command
```bash
#!/usr/bin/env bash
set -euo pipefail

BACKUP_DIR="/var/backups/mysql"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
FILENAME="biglive_db_${TIMESTAMP}.sql.gz.enc"
PASSPHRASE_FILE="/etc/biglive/backup_passphrase"

mkdir -p "${BACKUP_DIR}"

mysqldump --single-transaction --quick --routines --triggers \
  -u "${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" \
  | gzip -c \
  | openssl enc -aes-256-cbc -salt -pbkdf2 -pass file:"${PASSPHRASE_FILE}" \
  > "${BACKUP_DIR}/${FILENAME}"

# Sync to off-site S3 bucket
aws s3 cp "${BACKUP_DIR}/${FILENAME}" "s3://${BACKUP_S3_BUCKET}/mysql/${FILENAME}"
```

---

## 2. Restore Procedure
In the event of database failure or corrupted state:
```bash
# 1. Download backup from off-site storage
aws s3 cp "s3://${BACKUP_S3_BUCKET}/mysql/${FILENAME}" /tmp/restore.sql.gz.enc

# 2. Decrypt and decompress
openssl enc -d -aes-256-cbc -pbkdf2 -pass file:/etc/biglive/backup_passphrase \
  -in /tmp/restore.sql.gz.enc \
  | gunzip -c \
  | mysql -u "${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}"

# 3. Clear application caches and run pending migrations
php /var/www/biglive/backend/artisan cache:clear
php /var/www/biglive/backend/artisan config:cache
php /var/www/biglive/backend/artisan route:cache
php /var/www/biglive/backend/artisan migrate --force
```
