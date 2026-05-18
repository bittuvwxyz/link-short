# Core PHP URL Shortener

## Installation
1. Import `sql/schema.sql`.
2. Update DB and SMTP values in `config/config.php`.
3. Install PHPMailer:
   ```bash
   composer require phpmailer/phpmailer
   ```
4. Point web root to project and enable Apache mod_rewrite.
5. Access home: `/index.php` and admin `/admin/login.php`.

## Gmail SMTP Setup
- Enable 2FA on Gmail account.
- Create App Password from Google Account Security.
- Put app password in `$SMTP_PASS`.

## Default Admin
- Username: `admin`
- Password: `admin123`

## API Endpoints
- `POST /api/create.php` with `long_url`
- `GET /api/analytics.php?code=1000000`
- `GET /api/redirect.php?code=1000000`

## Notes
- Counter short code: `code = 999999 + id`.
- Includes URL expiry, password protection, CSV export, Chart.js analytics, QR code, dark mode.
