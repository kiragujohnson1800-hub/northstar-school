# School Management System

A PHP and MySQL starter application for student, instructor, parent, and administrator accounts. All four roles share one login form; the role stored in MySQL determines the dashboard after authentication.

## Requirements

- XAMPP with Apache, MySQL, and PHP 8.0 or later
- A modern browser with an internet connection for the Tailwind CSS CDN

## Run with XAMPP

1. Copy the `school_management_system` folder into XAMPP's `htdocs` directory.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`, import `database.sql`, or run its contents in the SQL tab.
4. Create the first administrator from a terminal with PHP CLI: run `php create-admin.php` from the project directory and follow the prompts. The password is hashed before it is stored. The script refuses web requests.
5. If your local MySQL credentials differ from the XAMPP defaults, set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` in the Apache/PHP environment. Defaults are `127.0.0.1`, `school_management`, `root`, and an empty password.
6. Visit `http://localhost/school_management_system/`.

Students can register themselves. Instructors and parents are created by an administrator.

## Included features

- Shared login page, password hashing, role-based redirects, and role-protected dashboards.
- Student self-registration; administrator-created instructor, parent, and student accounts.
- Administrator user modal for listing, creating, and removing non-admin accounts.
- PHP sessions, CSRF tokens, prepared statements, output escaping, and logout.

This is a training project, not a production-ready school information system. Use HTTPS, configure a dedicated database account, and add account recovery, audit logging, rate limiting, and a secure password-change flow before production use.
