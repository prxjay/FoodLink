# FoodLink – Community Food Sharing (PHP + MySQL)

Secured **Top 5 position out of 45+ teams** in **IEEE ComSoc’s Connectron 36-hour Hackathon** by collaborating with a **4-member team** to build *FoodLink* — a web platform that connects people with surplus food to those who can use it.

Users can post and claim food from a single account, with a clean, responsive UI and email notifications.

## Features
- Single account for posting and claiming food
- Session-based authentication; stays logged in until logout
- SweetAlert2 popups for signup/login/post/claim flows
- PHPMailer (Gmail App Password) for claim and contact emails
- Unified dashboard: Available Food, Your Foods, My Claimed Food
- Search bar in Available Food
- Mobile hamburger navigation up to 1000px

## Tech Stack
- Frontend: HTML, CSS, JavaScript
- Backend: PHP (mysqli)
- Email: PHPMailer via Gmail SMTP (App Password)
- Database: MySQL/MariaDB
- Local server: XAMPP (Apache + MySQL)

## Getting Started (Local)
1) Place the folder here:
C:\xampp\htdocs\php-practice


2) Database (phpMyAdmin)
- Create database `sample`
- Import your SQL dump (e.g., `sample (1).sql`) from phpMyAdmin → Import

3) Configure DB in `connection.php`
```php
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "sample";
$port = 4307; // your MySQL port in XAMPP
```

4) PHPMailer secrets
- Copy `mail_secrets.php.example` to `mail_secrets.php`
- Edit with your Gmail + 16-character App Password:
```php
define('SMTP_USERNAME_SECRET', 'your_gmail@gmail.com');
define('SMTP_PASSWORD_SECRET', 'your_app_password');
```
- `mail_secrets.php` is already ignored by Git

5) Composer (if vendor isn’t present)
C:\xampp\php\php.exe C:\composer\composer.phar install

6) Run
http://localhost/php-practice/index.html


## Key Files
- `index.html` – Homepage
- `signin.php`, `signup.php` – Auth (signup uses OTP email)
- `verify_otp.php` – Verifies OTP and creates the account
- `unified_dashboard.php` – Post, view, and claim food
- `profile.php` – Profile and your lists
- `send_email.php` – Claim email via PHPMailer
- `contact_send.php` – Contact form email via PHPMailer
- `mail_secrets.php.example` – Sample secrets file (copy to `mail_secrets.php`)
- `connection.php` – MySQL connection settings

## Security
- Never commit `mail_secrets.php` (already in `.gitignore`)
- If a secret is ever pushed, immediately revoke/rotate the Gmail App Password

## Learn More
To learn more about the tools used in this project, check out:

- [PHP Documentation](https://www.php.net/docs.php) – Core PHP reference  
- [MySQL Documentation](https://dev.mysql.com/doc/) – Database setup & queries  
- [PHPMailer Documentation](https://github.com/PHPMailer/PHPMailer) – Email sending via SMTP  
- [SweetAlert2 Documentation](https://sweetalert2.github.io/) – Popups for better UX  
- [XAMPP Guide](https://www.apachefriends.org/docs/) – Local development server  

## Demo
Live site: [https://food-link-sepia.vercel.app/](https://food-link-sepia.vercel.app/)

## Screenshots

### Homepage
<img width="1896" height="997" alt="Image" src="https://github.com/user-attachments/assets/1002d792-f257-4683-8410-ce8467c2a528" />

### Dashboard
<img width="1892" height="993" alt="Image" src="https://github.com/user-attachments/assets/c233cc17-31da-4ec4-9722-89951fa49212" />

## License
MIT © FoodLink
