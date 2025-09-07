<?php
// Email Configuration
// Replace these with your actual Gmail credentials

// Default Email Settings
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 25);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');

// Email Settings
define('FROM_EMAIL', 'noreply@foodlink.com');
define('FROM_NAME', 'FoodLink');

// Instructions for Gmail Setup:
/*
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate password for "Mail"
   - Use this password in SMTP_PASSWORD above

3. Replace 'your-email@gmail.com' with your actual Gmail address
4. Replace 'your-app-password' with the generated app password

Alternative: Use other email services
- SendGrid: Free tier available
- Mailgun: Developer-friendly
- Amazon SES: Pay-per-use
*/
?>
