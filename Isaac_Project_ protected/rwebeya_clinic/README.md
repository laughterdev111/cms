# Rwebeya Clinic Management System

A PHP 8.2+ and MySQL starter application with role-based access for Admin, Receptionist, and Doctor.

## Included
- Secure PHP session authentication and server-side role checks
- Email verification before an invited staff member can set a password and sign in
- Admin staff invitations and staff status management
- Receptionist patient registration and patient directory
- Doctor consultation queue and consultation notes
- Appointment scheduling list
- Patient profile / medical history view
- Responsive dashboard interface using Forest Green, Sage Green and Light Cream
- Light/dark theme switch (saved in the browser)
- Language selection intentionally not included yet

## Requirements
- PHP 8.2 or newer
- MySQL 8 / MariaDB 10.5+
- Apache with `mod_rewrite` (or PHP built-in server for local testing)
- PHP extensions: `pdo_mysql`, `mbstring`, `openssl`
- Composer (for PHPMailer email delivery)

## Setup (XAMPP on Ubuntu)
1. Copy this folder to `/opt/lampp/htdocs/rwebeya_clinic`.
2. Create a MySQL database named `rwebeya_clinic`.
3. Import `database/schema.sql` using phpMyAdmin. If you are updating an existing database from the previous ZIP, run `database/migrations/001_add_user_avatar.sql` once instead.
4. Copy `.env.example` to `.env` and edit database and SMTP settings.
5. In a terminal, open the project folder and run:
   `composer install`
6. Make `storage/` writable by the web-server user.
7. Create the first administrator from the project folder:
   `php scripts/create_admin.php "Clinic Administrator" admin@example.com`
   The command prints a one-time setup link. Open it, verify the email address, and set the admin password.
8. Open `http://localhost/rwebeya_clinic/public/`.

## Email configuration
Configure SMTP values in `.env` to send real verification emails. The project uses PHPMailer.
For local development only, if `APP_ENV=local` and SMTP is not configured, verification links are written to `storage/mail.log`. This is a development convenience, not real email verification.

## Important security notes
- Role authorization is enforced by PHP on every protected page and action. Hiding a menu item in the browser is not security.
- Do not use this starter project with real patient records or deploy it publicly without a security review, HTTPS, backups, access controls, audit logging, and compliance with applicable health-data laws and clinic policies.
- Use fictional test data while learning. This project is an educational starter, not a certified clinical system.
- Change the default configuration and use a dedicated database user; do not use MySQL root in production.


## Admin workspace update
The Admin sidebar contains Dashboard, Staff Management, Reports and System Settings only. Patient registration, appointment scheduling and clinical work remain in the Receptionist and Doctor workspaces; direct Admin access to those pages is denied. The Admin can review aggregate clinic counts through Reports. The header theme control switches between light and dark themes and stores the preference in the browser. The profile menu opens account details; the profile page supports name/email updates and JPG/PNG/WEBP photo uploads (up to 2 MB). Logging out always asks for confirmation, and forms marked for change tracking warn if unsaved edits may be lost.


## View the Admin interface immediately

Open `preview/admin-preview.html` in a web browser to preview the Admin Dashboard without starting Apache or MySQL. This standalone preview demonstrates the layout, navigation, theme switch, profile menu/photo preview, and logout confirmation. It uses sample figures and does not connect to the database. For the real PHP application, follow the XAMPP setup steps above and sign in through `public/login.php`.


## Additional HTML interface files
A new `public/interfaces/` folder contains standalone HTML interface layouts for:
- Admin dashboard, staff management, reports and system settings
- Receptionist dashboard, patient directory and appointments
- Doctor dashboard and consultation queue

These HTML files are interface companions. They link to the existing authenticated PHP modules, which remain the source of live database records, form validation, permissions and writes. For live operations, run the PHP application through a local PHP server (for example XAMPP) and sign in; do not open PHP files by double-clicking them. The HTML interface files can be opened directly for a visual preview.
