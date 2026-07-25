# ⚖️ LawyerCasePro

**LawyerCasePro** is a simple web system that helps law offices manage their cases in one place, instead of using paper files or scattered spreadsheets. It is built with **PHP** and **MySQL**, and runs on **XAMPP**.

The system has **4 types of users**, and each one gets their own dashboard with only the tools they need:

- 🧑‍💼 **Admin** — adds new cases and assigns them to an advocate and a court.
- 🧑‍⚖️ **Advocate** — adds and tracks their own cases and hearings.
- 👨‍⚖️ **Judge** — updates case status, hearing dates, and sees the full case list.
- 👤 **Client** — can only view their own case, no editing (read-only).

On top of that, the system also comes with a few extra things that make it easy and comfortable to use:

- 🌐 A **language button** to switch the whole landing page between English and Bengali.
- 📄 A **Download** button on every dashboard, to save the case list as a clean PDF file.
- 🔍 A **search box** to quickly find a case by case number, client name, or advocate name.
- 🔐 Safe **login and registration**, with passwords stored in an encrypted (hashed) form, not plain text.

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Database Schema](#-database-schema)
- [Installation & Setup](#-installation--setup)
- [Usage](#-usage)
- [Role-Based Access](#-role-based-access)
- [Contributing](#-contributing)
- [Contact](#-contact)

---

## ✨ Features

- 🔐 **Secure Authentication** — Registration and login with hashed passwords (`password_hash` / `password_verify`) and PHP sessions.
- 🧑‍⚖️ **Four Role-Based Dashboards** — Admin, Advocate, Judge, and Client, each with its own guarded view and permissions.
- 📄 **Case Management** — Add, search, filter, and update cases (case number, case type, court, status, hearing dates, parties involved).
- 🔎 **Live Search** — Search cases by case number, client name, or advocate name across dashboards.
- 📥 **PDF Export** — Every dashboard can export the current case table to a professional, landscape-orientation PDF using **jsPDF** + **jsPDF-AutoTable**.
- 🌐 **Bilingual UI (English ⇄ Bengali)** — A language toggle switches all landing page text and form placeholders between English and Bengali on the fly.
- 📬 **Contact Form** — Visitor messages are stored directly in the database for follow-up.
- 🎨 **Responsive UI** — Built with Tailwind CSS and Font Awesome for a clean, mobile-friendly experience.
- 🚪 **Session-Based Logout** — Fully clears session data and cookies on logout.

---

## 🛠 Tech Stack

| Layer            | Technology                                              |
|-------------------|----------------------------------------------------------|
| Backend           | PHP (procedural, `mysqli`)                               |
| Database          | MySQL / MariaDB (via XAMPP)                              |
| Frontend          | HTML5, Tailwind CSS, vanilla JavaScript                  |
| Icons             | Font Awesome 6                                            |
| PDF Generation    | [jsPDF](https://github.com/parallax/jsPDF) + [jsPDF-AutoTable](https://github.com/simonbengtsson/jsPDF-AutoTable) |
| Local Server      | XAMPP (Apache + MySQL)                                    |

---

## 📁 Project Structure

```
LawyerCasePro/
├── LawyerCaseManagement_Landing Page.html   # Public landing page (hero, features, about, contact)
├── LawyerCaseManagementLanding Page.js      # Language toggle (EN ⇄ BN) logic for the landing page
├── Register.php                             # New user registration (role selection: Admin/Advocate/Judge/Client)
├── Login.php                                # Login + role-based redirect
├── Logout.php                               # Session teardown and redirect to landing page
├── Contact.php                              # Handles contact form submissions
├── Admin Dashboard.php                      # Admin: add cases, assign advocates/courts, export PDF
├── Advocate Dashboard.php                   # Advocate: manage own cases, opposing parties, export PDF
├── Judge Dashboard.php                      # Judge: update status/hearing dates, master case log, export PDF
├── Client Dashboard.php                     # Client: read-only case view, search, export PDF
└── README.md
```

> **Note:** File names currently contain spaces (e.g. `Admin Dashboard.php`). If you plan to deploy this on a server or share it more broadly, consider renaming files to use hyphens or underscores (e.g. `admin-dashboard.php`) for better URL and cross-platform compatibility.

---

## 🗄 Database Schema

The system uses a MySQL database named **`lawyercasepro`** with the following core tables:

| Table               | Purpose                                                              |
|---------------------|-----------------------------------------------------------------------|
| `register`          | Stores user accounts (`fullName`, `email`, `contact`, `password`, `role`) |
| `adminDashboard`    | Cases created by Admins                                              |
| `advocateDashboard` | Cases created/managed by Advocates                                   |
| `judgeDashboard`    | Master case log used by Judges and Clients (status, hearing dates)   |
| `contact`           | Messages submitted through the public contact form                  |

> A ready-to-import SQL file (`lawyercasepro.sql`) is recommended for new setups — see [Roadmap](#-roadmap).

---

## ⚙️ Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (or any Apache + PHP + MySQL stack)
- A modern web browser

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/<your-username>/LawyerCasePro.git
   ```

2. **Move the project into your XAMPP `htdocs` folder**
   ```bash
   mv LawyerCasePro /path/to/xampp/htdocs/
   ```

3. **Start Apache and MySQL** from the XAMPP Control Panel.

4. **Create the database**
   - Open `phpMyAdmin` (`http://localhost/phpmyadmin`)
   - Create a new database named `lawyercasepro`
   - Create the tables listed in [Database Schema](#-database-schema) (or import the provided SQL file, if available)

5. **Configure the database connection**
   By default, all PHP files connect using:
   ```php
   $conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
   ```
   Update the host/username/password in each file if your local MySQL setup differs.

6. **Run the app**
   Open your browser and go to:
   ```
   http://localhost/LawyerCasePro/LawyerCaseManagement_Landing%20Page.html
   ```

---

## 🚀 Usage

1. **Register** an account and select your role (Admin, Advocate, Judge, or Client).
2. **Log in** — you'll be redirected automatically to the dashboard matching your role.
3. Depending on your role:
   - **Admin**: Add a new case, assigning a client, advocate, case type, court, and status.
   - **Advocate**: Add/manage your own cases and track opposing parties.
   - **Judge**: Update hearing dates and case status; view the full case log.
   - **Client**: Load and search your cases in a read-only view.
4. Use the **search bar** on any dashboard to filter cases by case number, client, or advocate name.
5. Click **Download** on any dashboard to export the visible case table as a PDF.
6. Use the **language toggle** (🌐 বাংলা / English) on the landing page to switch languages.
7. Click **Logout** to safely end your session.

---

## 👥 Role-Based Access

| Role       | Dashboard              | Key Permissions                                              |
|------------|------------------------|----------------------------------------------------------------|
| **Admin**    | `Admin Dashboard.php`    | Add clients & cases, assign court/advocate, view all, export PDF |
| **Advocate** | `Advocate Dashboard.php` | Add/manage own cases, track opposing parties, export PDF        |
| **Judge**    | `Judge Dashboard.php`    | Update case status & hearing dates, view master log, export PDF |
| **Client**   | `Client Dashboard.php`   | View own case status (read-only), search, export PDF            |

Each dashboard is protected by a session-based **auth guard**: if a user isn't logged in with the matching role, they're redirected to `Login.php`.


---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

1. Fork the project
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---


