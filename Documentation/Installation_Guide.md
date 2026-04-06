⚙️ Installation Guide

Grievance Redressal Portal

⸻

📌 Prerequisites

Make sure the following are installed:
	•	XAMPP (Apache & MySQL)
	•	Web Browser (Chrome/Edge)
	•	Git (optional)

⸻

📥 Step 1: Clone Repository: https://github.com/adityaph-17/grievance-redressal-portal.git
OR download ZIP and extract.

📁 Step 2: Move Project Folder
	•	Copy project folder
	•	Paste inside:
         C:\xampp\htdocs\

🗄️ Step 3: Setup Database
	1.	Open XAMPP Control Panel
	2.	Start Apache and MySQL
	3.	Open browser and go to:
        http://localhost/phpmyadmin
    4.	Create new database:
        grievance_portal
    5.	Import SQL file:
	•	Click Import
	•	Select .sql file from project
	•	Click Go

⚙️ Step 4: Configure Database Connection

Open config file (example: config.php) and update:
$host = "localhost";
$user = "root";
$password = "";
$database = "grievance_portal";

📧 Step 5: Configure Email (PHPMailer)
	1.	Open mail configuration file
	2.	Add your email credentials:
        $mail->Username = "your-email@gmail.com";
        $mail->Password = "your-app-password";

    ⚠️ Use Gmail App Password, not your real password.

▶️ Step 6: Run Project

Open browser and go to:
http://localhost/your-project-folder


🛠️ Troubleshooting
	•	Apache not starting → Check port (80/443)
	•	MySQL error → Restart XAMPP
	•	Email not sending → Check App Password
	•	Database not connecting → Verify config file

✅ System Ready

Your project should now be running successfully 🎉