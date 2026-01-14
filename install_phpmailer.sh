#!/bin/bash
# Install PHPMailer via Composer

cd "$(dirname "$0")"

echo "Installing PHPMailer..."
echo ""

# Check if composer exists
if command -v composer &> /dev/null; then
    echo "✅ Composer found"
    composer require phpmailer/phpmailer
    echo ""
    echo "✅ Installation complete!"
    echo "You can now test email sending."
else
    echo "❌ Composer not found!"
    echo ""
    echo "Please install Composer first:"
    echo "1. Visit: https://getcomposer.org/download/"
    echo "2. Or run: curl -sS https://getcomposer.org/installer | php"
    echo "3. Then run: php composer.phar require phpmailer/phpmailer"
fi

