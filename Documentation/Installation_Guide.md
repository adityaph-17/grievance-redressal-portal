# ⚙️ Installation Guide  
# Grievance Redressal Portal

This guide will help you install and run the **Grievance Redressal Portal** on your local machine using XAMPP.

---

# 📌 Prerequisites

Before starting, make sure the following software is installed on your system:

- ✅ XAMPP (Apache & MySQL)
- ✅ Web Browser (Google Chrome / Microsoft Edge)
- ✅ Git *(Optional)*

---

# 📥 Step 1: Download the Project

## Option 1: Clone Repository using Git

```bash
git clone https://github.com/adityaph-17/grievance-redressal-portal.git
```

## Option 2: Download ZIP

- Open the GitHub repository
- Click **Code → Download ZIP**
- Extract the ZIP file

---

# 📁 Step 2: Move Project Folder

1. Copy the project folder
2. Paste it inside the XAMPP `htdocs` directory:

```text
C:\xampp\htdocs\
```

Example:

```text
C:\xampp\htdocs\grievance-redressal-portal
```

---

# 🗄️ Step 3: Setup Database

## 1. Start XAMPP Services

Open **XAMPP Control Panel** and start:

- Apache
- MySQL

---

## 2. Open phpMyAdmin

Open your browser and visit:

```text
http://localhost/phpmyadmin
```

---

## 3. Create Database

Create a new database named:

```text
grievance_portal
```

---

## 4. Import SQL File

1. Click on the newly created database
2. Open the **Import** tab
3. Select the `.sql` file from the project folder
4. Click **Go**

---

# ⚙️ Step 4: Configure Database Connection

Open the database configuration file  
(example: `config.php`)

Update the following credentials:

```php
<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "grievance_portal";

?>
```

---

# 📧 Step 5: Configure Email (PHPMailer)

Open the mail configuration file and update your Gmail credentials:

```php
$mail->Username = "your-email@gmail.com";
$mail->Password = "your-app-password";
```

> ⚠️ Important:  
> Use a **Gmail App Password** instead of your actual Gmail password.

---

# ▶️ Step 6: Run the Project

Open your browser and visit:

```text
http://localhost/grievance-redressal-portal
```

---

# 🛠️ Troubleshooting

- **Apache not starting** → Check if port 80/443 is already in use  
- **MySQL error** → Restart XAMPP and start MySQL again  
- **Email not sending** → Verify Gmail App Password  
- **Database connection failed** → Check credentials in `config.php`  
- **Page not loading** → Ensure project folder is inside `htdocs`

```text
C:\xampp\htdocs\grievance-redressal-portal
```

Open project:

```text
http://localhost/grievance-redressal-portal
```

---

# ✅ System Ready

Your **Grievance Redressal Portal** should now be running successfully 🎉

---

# 📌 Repository

GitHub Repository:

```text
https://github.com/adityaph-17/grievance-redressal-portal
```
