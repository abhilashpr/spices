# OTP Email Verification Setup Guide

## 1. Create Users Table

Run the SQL script to create the users table:
- Open in browser: `http://localhost/online-sp/database/create_users_table.php`
- Or run via terminal: `php database/create_users_table.php`

## 2. Configure Gmail SMTP

### Step 1: Create .env file
Create a `.env` file in the root directory with your Gmail credentials:

```env
# Gmail SMTP Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME=Wynvalley

# Application Settings
APP_NAME=Wynvalley
APP_URL=http://localhost/online-sp

# Session & Security
SESSION_LIFETIME=7200
TOKEN_LIFETIME=86400
```

### Step 2: Get Gmail App Password

1. Go to your Google Account: https://myaccount.google.com/
2. Navigate to **Security** → **2-Step Verification** (enable if not already)
3. Go to **App passwords**: https://myaccount.google.com/apppasswords
4. Select **Mail** and **Other (Custom name)**
5. Enter "Wynvalley" as the name
6. Click **Generate**
7. Copy the 16-character password (no spaces)
8. Paste it in `.env` as `SMTP_PASSWORD`

### Step 3: Install PHPMailer (Recommended)

For better email delivery, install PHPMailer via Composer:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/online-sp
composer require phpmailer/phpmailer
```

If you don't have Composer, the system will fall back to PHP's `mail()` function, but Gmail SMTP requires PHPMailer.

## 3. Database Schema

The users table includes:
- `id` - Auto increment primary key
- `firstname` - User's first name
- `lastname` - User's last name
- `email` - Unique email address
- `password` - Hashed password (nullable for Gmail login)
- `status` - Enum: 'active', 'inactive', 'pending'
- `login_type` - Enum: 'web', 'gmail'
- `gmail_id` - Gmail ID (nullable)
- `otp` - 6-digit OTP code
- `otp_expires_at` - OTP expiration time
- `email_verified` - Boolean flag
- `auth_token` - Remember me token
- `token_expires_at` - Token expiration
- `created_at` - Timestamp
- `updated_at` - Timestamp

## 4. Registration Flow

1. User fills registration form (firstname, lastname, email, password)
2. System creates user with status 'pending'
3. System generates 6-digit OTP
4. System sends OTP via email (HTML template)
5. User enters OTP on verification page
6. System verifies OTP and activates account
7. User is automatically logged in

## 5. Features

- ✅ OTP email with beautiful HTML template
- ✅ 10-minute OTP expiration
- ✅ Resend OTP functionality (60-second cooldown)
- ✅ Auto-login after verification
- ✅ Remember me token support
- ✅ Gmail login support (ready for future implementation)
- ✅ Email verification status tracking

## 6. Routes

- `/register` - Registration page
- `/verify-otp?email=xxx` - OTP verification page
- `/resend-otp` - Resend OTP (POST request)

## 7. Testing

1. Register a new account
2. Check your email for OTP
3. Enter OTP on verification page
4. You should be automatically logged in

## Troubleshooting

### Email not sending?
- Check `.env` file exists and has correct values
- Verify Gmail App Password is correct
- Check PHP error logs
- Install PHPMailer for better SMTP support

### OTP not working?
- Check database connection
- Verify users table exists
- Check OTP expiration time (10 minutes)

### Database errors?
- Run `database/create_users_table.php` to create the table
- Check database credentials in `app/config/database.php`

