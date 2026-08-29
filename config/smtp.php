<?php
/**
 * CashSecond - SMTP Configuration
 * Enter your SMTP Host, Username and App Password below.
 *
 * For Gmail:
 *   - host: 'smtp.gmail.com'
 *   - port: 587 (TLS) or 465 (SSL)
 *   - encryption: 'tls'
 *   - username: 'wholesalehouse2016@gmail.com'
 *   - password: 'YOUR_16_DIGIT_GMAIL_APP_PASSWORD' (generate at myaccount.google.com/apppasswords)
 *
 * For Hostinger / cPanel / Custom Domain:
 *   - host: 'smtp.hostinger.com' / 'mail.yourdomain.com'
 *   - port: 465 or 587
 *   - username: 'info@yourdomain.com'
 *   - password: 'your_email_password'
 */

return [
    // Enable or disable SMTP
    'enabled'      => true,

    // SMTP Server Settings
    'host'         => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
    'port'         => (int)(getenv('SMTP_PORT') ?: 587),
    'encryption'   => getenv('SMTP_ENCRYPTION') ?: 'tls', // 'tls', 'ssl', or 'none'

    // Credentials
    'username'     => getenv('SMTP_USERNAME') ?: 'wholesalehouse2016@gmail.com',
    'password'     => getenv('SMTP_PASSWORD') ?: '', // Paste 16-character Gmail App Password or Webmail Password here

    // Sender details
    'from_email'   => getenv('SMTP_FROM_EMAIL') ?: 'wholesalehouse2016@gmail.com',
    'from_name'    => getenv('SMTP_FROM_NAME') ?: 'CashSecond Lead Desk',

    // Destination email
    'recipient'    => 'wholesalehouse2016@gmail.com',

    // Socket timeout in seconds
    'timeout'      => 15,
];
