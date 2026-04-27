# 🎓 IRMS — Internship Result Management System

A web-based internship result management system built with **PHP** and **MySQL**. IRMS allows universities to manage student internship placements, collect marks from lecturers and supervisors, and generate graded result reports — all through a role-based multi-user interface.

---

## ✨ Features

### 👤 Role-Based Access
Four distinct user roles, each with a dedicated dashboard:

| Role | Access |
|---|---|
| **Admin** | Full system control — manage students, internships, users, and reports |
| **Lecturer** | Enter marks and view results for assigned students |
| **Supervisor** | Enter marks and view results for assigned students |
| **Student** | View own internship details, marks, and final grade |

### 🛠️ Admin Capabilities
- **Student management** — add, edit, delete students; bulk import via CSV
- **Internship management** — assign students to lecturers, supervisors, and companies; manage company records; bulk import companies via CSV
- **User management** — manage lecturers and supervisors; bulk import via CSV with downloadable templates
- **Results overview** — view all graded internships with filtering
- **Print report** — generate printable summary reports filterable by assessor and grade

### 📝 Assessor (Lecturer / Supervisor) Capabilities
- View assigned students' internship details
- Enter component-based marks (0–100) with optional comments per component
- Marks are automatically weighted and totalled based on component weightage
- View submitted results and detailed breakdowns

### 🎓 Student View
- View internship placement details (company, lecturer, supervisor, duration)
- View component marks submitted by both lecturer and supervisor
- See computed **average marks** and final grade: **Distinction** (≥80), **Pass** (≥50), **Fail** (<50)

---

## 🗂️ Project Structure

```
COMP1044_SRC/
├── login.php                  # Unified login page for all roles
├── logout.php                 # Session destroy & redirect
├── index.php                  # Entry redirect
├── connection.php             # MySQL database connection
├── style.css                  # Global stylesheet
├── script.js                  # Shared JavaScript
├── COMP1044_Database.sql      # Full database schema + seed data
│
├── admin/
│   ├── dashboard.php          # Admin overview with stats cards
│   ├── students.php           # CRUD + CSV bulk import for students
│   ├── internships.php        # CRUD for internships and companies
│   ├── users.php              # CRUD + CSV bulk import for lecturers/supervisors
│   ├── results.php            # Results overview table
│   ├── result_detail.php      # Per-student detailed result view
│   ├── print_report.php       # Printable report with grade/assessor filters
│   ├── csv_template.php       # CSV template downloads
│   ├── header.php             # Shared admin header/nav
│   └── footer.php             # Shared admin footer
│
├── assessor/
│   ├── dashboard.php          # Assessor overview
│   ├── marks.php              # Enter / update component marks
│   ├── view_results.php       # List of assigned students and results
│   ├── result_detail.php      # Detailed marks breakdown per student
│   ├── header.php             # Shared assessor header/nav
│   └── footer.php             # Shared assessor footer
│
└── student/
    └── dashboard.php          # Student internship & marks view
```

---

## 🗄️ Database Schema

**Database:** `COMP1044_Database`

| Table | Key Columns |
|---|---|
| `admin` | `admin_id`, `admin_name`, `admin_email`, `admin_password` |
| `student` | `student_id`, `student_name`, `student_email`, `programme`, `student_password` |
| `lecturer` | `lecturer_id`, `lecturer_name`, `lecturer_email`, `lecturer_password` |
| `supervisor` | `supervisor_id`, `supervisor_name`, `supervisor_email`, `supervisor_password`, `company_name` |
| `company` | `company_id`, `company_name` |
| `internship` | `internship_id`, `student_id`, `lecturer_id`, `supervisor_id`, `company_id`, `duration`, `l_marks_id`, `s_marks_id`, `average_marks` |
| `component` | `component_id`, `component_name`, `component_weightage` |
| `lecturer_marks` | `l_marks_id`, `component_id`, `component_mark`, `total_marks`, `comments` |
| `supervisor_marks` | `s_marks_id`, `component_id`, `component_mark`, `total_marks`, `comments` |

---

## ⚙️ Setup & Installation

### Requirements
- PHP 8.x
- MySQL 8.x
- Apache/Nginx (e.g. via XAMPP, MAMP, or Laragon)

### Steps

1. **Clone or extract** the project into your web server's root directory (e.g. `htdocs/` or `www/`).

2. **Import the database:**
   - Open phpMyAdmin (or MySQL CLI)
   - Create a database named `COMP1044_Database`
   - Import `COMP1044_Database.sql`

3. **Configure the database connection** in `connection.php`:
   ```php
   $host     = "localhost";
   $user     = "root";
   $password = "your_password";
   $database = "COMP1044_Database";
   ```

4. **Visit** `http://localhost/COMP1044_SRC/` in your browser.

---

## 🔐 Default Login Credentials

| Role | ID | Password |
|---|---|---|
| Admin | `A001` | `admin123` |
| Lecturer | `L001` | `pass123` |
| Supervisor | `S001` | `pass123` |
| Student | *(see database)* | `password123` |

> ⚠️ **Security Notice:** Passwords are stored as plain text. This project is intended for academic/demo use only. Do **not** deploy to a public server without implementing proper password hashing (`password_hash` / `password_verify`) and prepared statements.

---

## 📋 CSV Bulk Import

Admins can bulk-import records using CSV files. Download the provided templates from within the system:

| Template | Required Columns |
|---|---|
| Students | `student_id`, `student_name`, `student_password`, `student_email`, `programme` |
| Lecturers | `lecturer_id`, `lecturer_name`, `lecturer_email`, `lecturer_password` |
| Supervisors | `supervisor_id`, `supervisor_name`, `supervisor_email`, `supervisor_password`, `company_name` |
| Companies | `company_name` |

---

## 🏫 Grade Scheme

| Average Marks | Grade |
|---|---|
| ≥ 80 | 🌟 Distinction |
| 50 – 79 | ✅ Pass |
| < 50 | ❌ Fail |
| Not yet graded | ⏳ Pending |

---

## 🛠️ Tech Stack

- **Backend:** PHP 8.x (procedural MySQLi)
- **Database:** MySQL 8.x
- **Frontend:** HTML5, CSS3, vanilla JavaScript
- **Database tool:** phpMyAdmin
