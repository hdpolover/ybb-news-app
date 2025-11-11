# Database Import Instructions

This directory contains SQL dump files for the YBB CMS database.

## Files

- `ybb_cms_full_dump.sql` - Complete database dump with all tables and data (36 tables)
- `ybb_cms_schema.sql` - Schema-only dump (if needed)

## Import Instructions

### Using Laravel Sail (Recommended)

```bash
# Import the full database dump
./vendor/bin/sail mysql < database/ybb_cms_full_dump.sql

# Or use mysql command directly
./vendor/bin/sail exec mysql mysql -u sail -p'password' laravel < database/ybb_cms_full_dump.sql
```

### Using Direct MySQL Connection

```bash
# If you have MySQL client installed locally
mysql -h 127.0.0.1 -P 3306 -u sail -p'password' laravel < database/ybb_cms_full_dump.sql
```

### Using Docker Compose Directly

```bash
# Copy file into container and import
docker cp database/ybb_cms_full_dump.sql ybb-news-app-mysql-1:/tmp/
docker exec -i ybb-news-app-mysql-1 mysql -u sail -p'password' laravel < /tmp/ybb_cms_full_dump.sql
```

## What's Included

The full dump includes:

**Core Tables:**
- `tenants` - Multi-tenant configuration
- `users` - User accounts
- `user_tenants` - User-tenant relationships with roles
- `domains` - Tenant domain mappings

**Content Management:**
- `posts` - Articles, pages, news, guides
- `pt_program` - Scholarship/opportunity details
- `pt_job` - Job posting details
- `terms` - Categories, tags, etc.
- `term_post` - Post-term relationships
- `media` - File uploads and attachments
- `post_revisions` - Content version history
- `post_comments` - Editorial comments

**Marketing & Analytics:**
- `ads` - Advertisement management
- `ad_impressions` - Ad performance tracking
- `ad_clicks` - Click tracking
- `email_campaigns` - Newsletter campaigns
- `newsletter_subscriptions` - Subscriber list
- `subscriber_segments` - Segment management
- `segment_subscriber` - Segment membership
- `analytics_events` - User activity tracking

**SEO & Redirects:**
- `seo_landings` - Landing page optimization
- `redirects` - URL redirect management

**System:**
- `audit_logs` - Activity audit trail
- `migrations` - Laravel migrations history
- `cache`, `cache_locks` - Application cache
- `jobs`, `job_batches` - Queue system
- `failed_jobs` - Failed job tracking
- `sessions` - User sessions

## Notes

- The dump was created on: November 11, 2025
- Database: `laravel`
- MySQL Version: 8.0.32
- Character Set: utf8mb4
- Collation: utf8mb4_unicode_ci

## Recreating the Dump

To create a fresh dump:

```bash
# Full dump with data
./vendor/bin/sail exec mysql mysqldump -u sail -p'password' laravel > database/ybb_cms_full_dump.sql

# Schema only (no data)
./vendor/bin/sail exec mysql mysqldump -u sail -p'password' --no-data laravel > database/ybb_cms_schema.sql

# Specific tables only
./vendor/bin/sail exec mysql mysqldump -u sail -p'password' laravel tenants users posts > database/ybb_cms_partial.sql
```

## Troubleshooting

**Error: Access denied**
- Verify MySQL credentials in `.env` file
- Default credentials: username=`sail`, password=`password`

**Error: Database doesn't exist**
```bash
# Create database first
./vendor/bin/sail mysql -e "CREATE DATABASE IF NOT EXISTS laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Import takes too long**
- Use `--quick` flag: `mysqldump --quick ...`
- Disable foreign key checks during import:
```sql
SET FOREIGN_KEY_CHECKS=0;
SOURCE database/ybb_cms_full_dump.sql;
SET FOREIGN_KEY_CHECKS=1;
```
