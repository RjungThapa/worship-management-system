# Worship Management System

A web-based Worship Management System designed to organize and manage church service activities, including service logs, members, and song resources. This is a solo academic project.

---

## Live Demo

https://worshipmanagementsys.freehosting.dev

---

## Project Overview

The Worship Management System is designed to help structure and manage worship services by maintaining records of:

- Church service logs
- Members
- Songs and worship resources

The system is currently designed for admin-only usage as an internal management tool. Future updates will introduce multi-user functionality and improved access control.

---

## Tech Stack

- Backend: PHP (Vanilla)
- Database: MariaDB
- Frontend: HTML, CSS, Bootstrap 5
- Server: Apache (XAMPP / InfinityFree compatible)

---

## Features

- Service log management
- Member management system
- Song management module
- Organized worship data structure
- PDF-based song storage (current implementation)
- Admin-only access (intentional design)

---

## Future Improvements

- Multi-user system (Admin, Worship Leader, Members)
- Migration from PDF-based songs to ChordPro format for structured lyrics and chords
- Authentication and role-based access control
- Improved dashboard analytics for service tracking
- UI/UX improvements for better usability
- Enhanced deployment and scalability support

---

## Database Design

ER diagram:

<img width="835" height="633" alt="Updated Logical ERM" src="https://github.com/user-attachments/assets/d42651cf-ca7b-4879-a83c-64acaa269a0b" />


---


## Setup Instructions

## Setup Instructions

### 1. Clone the repository
git clone https://github.com/your-username/worship-management-system.git
cd worship-management-system

### 2. Move project to server directory (XAMPP / Apache)
Copy the project folder into your web server directory:
/htdocs/

Example (Windows XAMPP):
C:\xampp\htdocs\worship-management-system

### 3. Start server
Start:
- Apache
- MySQL
(using XAMPP Control Panel)

### 4. Create database
Open phpMyAdmin:
http://localhost/phpmyadmin

Create database:
worship_management

### 5. Import database
- Open the database
- Go to Import
- Select:
sql/database.sql
- Click Import

### 6. Configure database connection
Create config.php in project root:

<?php
return [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => 'root',
    'db'   => 'worship_management'
];

### 7. Run project
Open browser:
http://localhost/worship-management-system/
