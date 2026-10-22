# ceidnotes

A public, read-only notes archive for a university department. Notes are organised as semesters →
lessons → nested directories → files. Anyone can browse the tree and download files; there is no
login. The files themselves live in object storage behind Laravel's cloud disk.

## Local setup

The project runs on Laravel Sail, so the host needs only Docker.

1. Install the Composer dependencies once, without PHP on the host:

   ```bash
   docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
       laravelsail/php84-composer:latest composer install --ignore-platform-reqs
   ```

2. Create `.env` and set the Sail values. Docker Compose reads the `DB_*` variables when it creates the
   MySQL container, so set them before the first `sail up`.

   ```bash
   cp .env.example .env
   ```

   ```dotenv
   APP_URL=http://localhost

   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=ceidnotes
   DB_USERNAME=sail
   DB_PASSWORD=password

   REDIS_HOST=redis
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis

   MAIL_MAILER=smtp
   MAIL_HOST=mailpit
   MAIL_PORT=1025

   FILESYSTEM_CLOUD=s3
   AWS_ACCESS_KEY_ID=sail
   AWS_SECRET_ACCESS_KEY=password
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=local
   AWS_ENDPOINT=http://rustfs:9000
   AWS_USE_PATH_STYLE_ENDPOINT=true
   ```

3. Start the containers, generate a key, migrate and seed a small sample tree:

   ```bash
   vendor/bin/sail up -d
   vendor/bin/sail artisan key:generate
   vendor/bin/sail artisan migrate --seed
   ```

4. Create the storage bucket once. Open the RustFS console (`http://localhost:9001` by default), sign
   in with `sail` / `password`, and create a bucket named after `AWS_BUCKET` (`local`). Any S3 client
   pointed at the RustFS port works too. The bucket lives in the `sail-rustfs` volume, so this is a
   one-off.

5. Build the frontend and open the app at `APP_URL`:

   ```bash
   vendor/bin/sail npm install
   vendor/bin/sail npm run dev
   ```

### Host ports

Sail publishes these ports on the host. Each one has a variable, so set it in `.env` when the default
is already taken on your machine.

| Service | Default | Variable |
|---|---|---|
| App (HTTP) | 80 | `APP_PORT` |
| Vite dev server | 5173 | `VITE_PORT` |
| MySQL | 3306 | `FORWARD_DB_PORT` |
| Redis | 6379 | `FORWARD_REDIS_PORT` |
| Mailpit SMTP | 1025 | `FORWARD_MAILPIT_PORT` |
| Mailpit dashboard | 8025 | `FORWARD_MAILPIT_DASHBOARD_PORT` |
| RustFS (S3 API) | 9000 | `FORWARD_RUSTFS_PORT` |
| RustFS console | 9001 | `FORWARD_RUSTFS_CONSOLE_PORT` |

## Production

- **PHP:** the lock file needs PHP 8.4 or newer; the target is PHP 8.5 with the `phpredis` and `zip` extensions.
- **Services:** MySQL 8.4 and Redis (cache, sessions and the queue). The daily backup also needs the MySQL
  client's `mysqldump` on the server.
- **Scheduler:** run `php artisan schedule:run` every minute from cron. It runs the daily backup
  clean, run and monitor, in production only.
- **Queue:** no worker is needed.
- **Environment values that matter:**
  - `APP_ENV=production`, `APP_DEBUG=false`, and the existing `APP_KEY` when moving servers;
  - `DB_*` for MySQL, `REDIS_HOST`/`REDIS_PASSWORD`/`REDIS_PORT`, and
    `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`;
  - `FILESYSTEM_CLOUD=azure` with `AZURE_STORAGE_ACCOUNT_NAME`, `AZURE_STORAGE_ACCOUNT_KEY` and
    `AZURE_STORAGE_CONTAINER`;
  - `MAIL_*` for a real transport, and `BACKUP_NOTIFICATION_EMAIL` for backup failure mail.
- **Deploy:**

  ```bash
  composer install --no-dev --optimize-autoloader
  npm ci && npm run build
  php artisan migrate --force
  php artisan optimize
  ```
