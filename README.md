# CS360-BudgetBuilder

BudgetBuilder is a web-based application designed for managing and tracking budgets.  
This guide explains how to install, configure, and run the project locally using **XAMPP** and **Composer**.

---

## Prerequisites

Before running the project, ensure you have the following installed:

- [XAMPP](https://www.apachefriends.org/) (includes Apache and MySQL)
- PHP (bundled with XAMPP)
- [Composer](https://getcomposer.org/) (PHP dependency manager)
- A web browser (Chrome, Firefox, Edge, etc.)

---

## Installation Steps

### 1. Place Project in XAMPP
- Locate your XAMPP installation directory.
- Copy the **CS360-BudgetBuilder** folder into the `htdocs` directory.  

### 2. Install Composer in the Project
Open a terminal/command prompt inside the project folder and run:

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"

php composer-setup.php

php -r "unlink('composer-setup.php');"

php composer.phar install
```

### 3. Import the Database

- Start XAMPP Control Panel and ensure Apache and MySQL are running.
- Open http://localhost/phpmyadmin.
- Create a new database (budgetbuilder)
- Import the provided budgetbuilder.sql into the database

### 4. Run the Website
- Open your browser and go to:
```bash
http://localhost/CS360-BudgetBuilder/

```
