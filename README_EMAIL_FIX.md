# Email Not Sending - Fix Required

## Problem
The logs show that emails are being sent via PHP's `mail()` function, which **does NOT support Gmail SMTP authentication**. This means emails will never be delivered even though the function returns "Success".

## Solution: Install PHPMailer

PHPMailer is required for Gmail SMTP to work properly.

### Option 1: Install via Composer (Recommended)

1. **Install Composer** (if not already installed):
   ```bash
   curl -sS https://getcomposer.org/installer | php
   ```

2. **Install PHPMailer**:
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/online-sp
   php composer.phar require phpmailer/phpmailer
   ```

   OR if composer is globally installed:
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/online-sp
   composer require phpmailer/phpmailer
   ```

3. **Or use the install script**:
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/online-sp
   ./install_phpmailer.sh
   ```

### Option 2: Manual Installation

If you don't have Composer:

1. Download PHPMailer:
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/online-sp
   mkdir -p vendor/phpmailer
   cd vendor/phpmailer
   git clone https://github.com/PHPMailer/PHPMailer.git phpmailer
   ```

2. Create autoloader:
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/online-sp
   ```

   Create `vendor/autoload.php` with:
   ```php
   <?php
   require_once __DIR__ . '/phpmailer/phpmailer/src/PHPMailer.php';
   require_once __DIR__ . '/phpmailer/phpmailer/src/SMTP.php';
   require_once __DIR__ . '/phpmailer/phpmailer/src/Exception.php';
   ```

### Option 3: Use Install Script via Browser

Visit: `http://localhost/online-sp/install_phpmailer.php`

## Verify Installation

After installing PHPMailer, test it:

1. **Test Email Script**:
   ```
   http://localhost/online-sp/test_email.php?email=your-email@gmail.com
   ```

2. **Check Logs**:
   The logs should now show:
   ```
   Using PHPMailer to send email to: your-email@gmail.com
   Email sent successfully!
   ```

## Configure .env File

Make sure your `.env` file has the correct Gmail credentials:

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME=Wynvalley
```

**Important**: Use Gmail App Password, not your regular password!

## Get Gmail App Password

1. Go to: https://myaccount.google.com/
2. Security → 2-Step Verification (enable if not already)
3. App passwords: https://myaccount.google.com/apppasswords
4. Select "Mail" and generate password
5. Use the 16-character password in `.env` file

## After Installation

Once PHPMailer is installed, the registration will work properly and emails will be sent successfully!

Test again by registering a new account.

