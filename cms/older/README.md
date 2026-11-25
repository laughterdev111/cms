# Rwebeya Clinic Management System

A private, custom Clinic Management System built for local healthcare operations.

## Stack
- **Frontend:** HTML5, CSS3, Vanilla JavaScript (Dynamic DOM rendering and fetch API)
- **Backend:** PHP (PDO prepared statements)
- **Database:** MySQL

## Database Setup (`rwebeya_clinic`)
Run this SQL in your MySQL server (via phpMyAdmin or terminal):

```sql
CREATE DATABASE IF NOT EXISTS rwebeya_clinic;
USE rwebeya_clinic;

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    dob DATE,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    insurance VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## How to Run
1. Place all files inside your local server directory (e.g., `htdocs/rwebeya_clinic/` in XAMPP).
2. Start Apache and MySQL in XAMPP / MAMP.
3. Open `http://localhost/rwebeya_clinic/login.html` in your web browser.
