# My-Bank-Appointment-System
A web-based bank appointment booking system that enables customers to schedule appointments online and allows administrators to manage branches, appointments, and performance statistics.
# Bank Appointment System

A complete web application for managing bank appointments using PHP, MySQL, HTML, CSS, JavaScript, and Bootstrap 5.

## 📋 Overview

The Bank Appointment System is a comprehensive web application that allows customers to easily book appointments at different bank branches, manage their appointments, rate services, and view branch information with an admin dashboard for monitoring statistics.

### ✨ Key Features

1. **Authentication System**
   - User registration
   - Login and logout
   - Session-based page protection

2. **Appointment Management**
   - Book new appointments with date/time picker
   - View all appointments (upcoming, completed, cancelled)
   - Edit upcoming appointments
   - Cancel appointments
   - Delete appointments
   - Print appointment receipt

3. **Rating System**
   - Rate services with star system (1-5)
   - Add feedback comments
   - View other customer reviews

4. **Branches Display**
   - Detailed information about each branch
   - Google Maps integration
   - Business hours and contact information

5. **Admin Dashboard**
   - Comprehensive system statistics
   - Interactive charts (Chart.js)
   - View recent appointments
   - Popular services analysis

6. **Additional Features**
   - Interactive notifications (SweetAlert2)
   - Modern responsive UI (Bootstrap 5)
   - Responsive design for all devices
   - Professional bank-style interface
   - Colored appointment statuses
   - Advanced print system

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+** - Main programming language
- **MySQL** - Database
- **MySQLi** - Database connection

### Frontend
- **HTML5** - Page structure
- **CSS3** - Styling and design
- **Bootstrap 5** - UI framework
- **JavaScript (ES6)** - Interactive programming
- **Chart.js** - Charts
- **SweetAlert2** - Beautiful notifications
- **Font Awesome** - Icons

## 📁 Project Structure

```
my-bank-appointment-system/
│
├── assets/
│   ├── css/
│   │   └── style.css          # Main CSS file
│   ├── js/
│   │   └── script.js          # Main JavaScript file
│   └── images/
│       └── bank-logo.png      # Bank logo
│
├── auth/
│   ├── login.php              # Login page
│   ├── register.php           # Registration page
│   └── logout.php             # Logout
│
├── config/
│   └── db.php                 # Database configuration
│
├── includes/
│   ├── header.php             # Page header
│   ├── navbar.php             # Navigation bar
│   └── footer.php             # Page footer
│
├── pages/
│   ├── appointments.php       # Appointment management
│   ├── dashboard.php          # Admin dashboard
│   ├── rating.php             # Rating system
│   └── branches.php           # Branches page
│
├── docs/                      # Arabic documentation
│   ├── project_overview.txt
│   ├── code_explaination.txt
│
├── index.php                  # Homepage
├── database.sql               # Database file
└── README.md                  # Documentation
```

## 📊 Database Schema

### Main Tables

#### 1. users
- `id` - User ID
- `name` - Full name
- `email` - Email address (unique)
- `password` - Encrypted password
- `role` - Role (user/admin)
- `created_at` - Registration date

#### 2. appointments
- `id` - Appointment ID
- `user_id` - User ID (Foreign Key)
- `service` - Service type
- `date` - Appointment date
- `time` - Appointment time
- `status` - Status (upcoming/completed/cancelled)
- `created_at` - Booking date

#### 3. ratings
- `id` - Rating ID
- `user_id` - User ID (Foreign Key)
- `appointment_id` - Appointment ID (Foreign Key)
- `rating` - Rating (1-5)
- `feedback` - Comment
- `created_at` - Rating date

#### 4. branches
- `id` - Branch ID
- `name` - Branch name
- `address` - Address
- `map_embed` - Google Maps link
- `created_at` - Creation date

### Prerequisites

1. **XAMPP** (or any local server supporting PHP and MySQL)
2. **PHP 7.4** or higher
3. **MySQL 5.7** or higher
4. Modern web browser


## 👤 Test Accounts

### Admin Account
- **Email:** `admin@bank.com`
- **Password:** `password`

### Regular User Account
- **Email:** `john@gmail.com`
- **Password:** `password`

## 🎨 Color Palette

- **Primary Green:** `#006e3d`
- **Light Green:** `#00a854`
- **Dark Green:** `#004d2b`
- **Background:** `#ffffff`
- **Light Background:** `#f8f9fa`

## 📱 System Interfaces

### 1. Homepage (index.php)
- Welcome message
- Quick booking form (for logged-in users)
- Service cards
- Upcoming appointments
- Business hours information

### 2. Appointments Page (pages/appointments.php)
- New appointment booking form
- All appointments table
- Edit, cancel, and delete buttons
- Print receipt capability

### 3. Ratings Page (pages/rating.php)
- Appointments awaiting rating
- Star rating form
- My previous ratings
- Other customer reviews

### 4. Branches Page (pages/branches.php)
- List of all branches
- Contact information
- Interactive Google Maps
- Business hours

### 5. Dashboard (pages/dashboard.php) - Admin Only
- Statistics cards
- Interactive charts
- Recent appointments list
- Analysis

## 🔒 Security

- Password encryption using `password_hash()`
- SQL Injection protection using Prepared Statements
- XSS protection using `htmlspecialchars()`
- Session-based page protection
- User permissions verification

## 🐛 Troubleshooting

### Database Connection Error
- Make sure MySQL is running in XAMPP
- Check connection settings in `config/db.php`
- Ensure `database.sql` is imported

### Pages Not Displaying Correctly
- Make sure project is in `htdocs` folder
- Check the path: `http://localhost/my-bank-appointment-system`

### Font Display Issues
- Make sure you're connected to the internet (fonts from Google Fonts)
- Check file encoding (UTF-8)

## 📝 Important Notes

1. Project is designed to run locally on XAMPP
2. Default password for all accounts: `password`
3. Colors can be customized from `assets/css/style.css`
4. To add new branches, use phpMyAdmin directly


## 📄 Documentation

Complete Arabic documentation is available in the `/docs` folder:
- Project overview
- Code explanation

## 📧 Support and Contact

For inquiries and support:
- Email: info@bankappointments.com
- Phone: +1 (800) 123-4567

## 📄 License

This project is open source and available for educational and commercial use.

---

**Developed with ❤️ using PHP & MySQL**

© 2025 Bank Appointment System - All Rights Reserved
