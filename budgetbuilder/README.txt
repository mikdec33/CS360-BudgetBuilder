BudgetBuilder (Bootstrap UI) - Full project

Installation steps (exact):

1. Copy the project folder into your XAMPP htdocs:
   C:\xampp\htdocs\budgetbuilder

2. Import the database:
   - Open http://localhost/phpmyadmin
   - Import the file database.sql (this creates DB, tables, and sample data & admin)

   OR let the app auto-create DB on first run (config.php attempts to create the DB).

3. Install Composer dependencies (for Excel export):
   - Download & install Composer: https://getcomposer.org/
   - Open Command Prompt and run:
     cd C:\xampp\htdocs\budgetbuilder
     composer require phpoffice/phpspreadsheet

4. Start Apache & MySQL in XAMPP Control Panel.

5. Login:
   - username: admin
   - password: admin123

6. Use Admin -> Manage pages to populate faculty, students, tuition, travel, rates.

7. Create a budget through Wizard (Wizard -> Step 1, add personnel, students, travel, review, submit)
   After submit, click the "Download Excel" link to download the multi-sheet .xlsx file.

Notes & security:
- Passwords stored as bcrypt hashes. Use password_hash() for manual inserts.
- This is a lab/demo app. For production, add CSRF, HTTPS, stricter validation, and role checks.
