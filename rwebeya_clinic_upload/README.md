# Rwebeya Clinic Management System

This package keeps the user interface in HTML, CSS and JavaScript. PHP is used only for small backend API endpoints; MySQL stores application records.

## Folder structure
- `public/` — website pages, styles and browser JavaScript
- `backend/api/` — PHP implementation kept outside the public web root
- `public/api/` — public entry point that forwards requests to the private backend
- `backend/config.example.php` — database configuration template
- `database/rwebeya_clinic.sql` — starter database schema

## Important
1. This is an upload-ready starter foundation based on the pages discussed in the project history. It is not a recovered copy of the exact earlier ZIP or original dashboard designs.
2. Do not upload real patient information, passwords, or `backend/config.php` to GitHub.
3. The login endpoint expects users to have verified email addresses and securely hashed passwords. Email verification delivery and staff-registration endpoints still need to be configured.
4. Test with fictional data only. This free/student deployment is not suitable for confidential real patient records.

## Deploy
- Upload `public/` as the website document root and keep `backend/` one directory above it if HOKAS allows that layout.
- The `public/api/login.php` entry point forwards requests to the private backend implementation.
- Create a MySQL database and import `database/rwebeya_clinic.sql`.
- Copy `backend/config.example.php` to `backend/config.php` and fill in the database credentials privately on the server.
- Update the fetch URL in `public/js/app.js` if the backend is deployed at a different path.
- Confirm PHP version and PDO MySQL extension are enabled.
