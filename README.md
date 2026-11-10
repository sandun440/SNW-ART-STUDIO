# SNW ART STUDIO

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777bb4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL Database](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Live Demo](https://img.shields.io/badge/Live%20Demo-snwartstudio.infinityfree.io-c5a059?style=flat-square)](http://snwartstudio.infinityfree.io)
[![Author](https://img.shields.io/badge/Developer-Sandun%20Sameera-11100e?style=flat-square)](https://sandunsameera.vercel.app/)

A bespoke, luxury fine art portfolio and commission management web application crafted for independent portrait artists and fine pencil sketching studios. Designed with an editorial aesthetic, zero-friction client workflows, and production-grade security.

🌐 **Live Website:** [http://snwartstudio.infinityfree.io](http://snwartstudio.infinityfree.io)  
👨‍💻 **Developer Portfolio:** [https://sandunsameera.vercel.app/](https://sandunsameera.vercel.app/)

---

## ✨ Key Features

### 🎨 Visual & Client Experience
- **Luxury Editorial Design System:** Warm alabaster/cream palette (`#faf7f2`), champagne gold accents (`#c5a059`), serif typography (*Playfair Display* & *Plus Jakarta Sans*), subtle glassmorphism, and responsive micro-interactions.
- **Dynamic Portfolio & Gallery:** Filter by categories (*Pencil Drawings*, *Portrait Arts*) with dynamic artwork counts, high-resolution imagery, and dedicated piece showcase pages.
- **Bespoke Commission Flow:** Interactive commission type selector (Original piece, custom portrait, or fine art pencil drawing), drag-and-drop reference photo upload with instant client-side thumbnail preview.
- **Client Order Tracking Portal (`track.php`):** Private commission tracking with Order Reference No. and phone number verification. Features a visual 4-step progress tracker:
  1. `Order Received`
  2. `Studio Review`
  3. `On Easel (Drawing in progress)`
  4. `Completed & Dispatched`
- **Bank Transfer & Payment Policy (`payment.php` & `confirmation.php`):** Clear payment instructions with 1-click clipboard copy for account details and direct WhatsApp receipt submission.
- **WhatsApp Integration:** 1-click floating chat widget and dynamic pre-filled inquiry buttons for artworks and commission orders.

### 🛡️ Studio Administration Portal (`/admin`)
- **Direct Header Navigation Pill:** Visible lock `Admin` button when logged out; switches to live `● Dashboard` pill when authenticated.
- **Metrics & Overview:** Live stat cards for total artworks, pending commissions, completed works, and total revenue.
- **Artworks Management:** Complete CRUD operations, category assignment, price tagging, and featured piece toggling.
- **Commission Order Management:** View client contact details, inspect uploaded reference photographs, update status stages, and launch 1-click WhatsApp messaging.
- **Category Manager:** Add, edit, or reorganize studio art categories.

### 🔒 Security & Architecture
- **Environment Auto-Detection:** Automatically switches database and base URL configurations between Localhost (Windows XAMPP) and Production (Linux / InfinityFree).
- **Environment Variables (`.env`):** Native `.env` parser support without bloated third-party dependencies.
- **CSRF Token Protection:** Form token validation on all state-changing POST requests.
- **SQL Injection Defense:** Strict PDO prepared statements throughout all queries.
- **Direct Upload Execution Protection:** `.htaccess` rules prevent executing scripts inside upload and database folders.

---

## 🚀 Quick Setup (Local Development)

### Prerequisites
- **XAMPP / WampServer** with PHP 8.1+ and MySQL / MariaDB.
- Git (optional).

### 1. Installation
Clone or copy the project into your local server root directory:
```bash
# Path example for XAMPP on Windows:
C:\xampp\htdocs\SNW ART STUDIO
```

### 2. Database Configuration
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`) or MySQL CLI.
2. Create database:
   ```sql
   CREATE DATABASE IF NOT EXISTS snw_art_studio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import [`database/schema.sql`](database/schema.sql) into `snw_art_studio`.

### 3. Environment Setup
Copy the example environment configuration:
```bash
cp .env.example .env
```
Ensure your local credentials match in `.env`:
```ini
DB_HOST=127.0.0.1
DB_NAME=snw_art_studio
DB_USER=root
DB_PASS=
BASE_URL=http://localhost/SNW%20ART%20STUDIO/
```

### 4. Run the Application
Open your browser and navigate to:
- **Public Portfolio:** `http://localhost/SNW%20ART%20STUDIO/`
- **Order Tracking:** `http://localhost/SNW%20ART%20STUDIO/track.php`
- **Admin Dashboard:** `http://localhost/SNW%20ART%20STUDIO/admin/`

#### Default Admin Credentials:
| Credential | Value |
| :--- | :--- |
| **Email** | `admin@snwartstudio.com` |
| **Password** | `password` |

*(Be sure to update your password in the admin settings after your first sign-in)*

---

## 📁 Project Directory Structure

```text
SNW ART STUDIO/
├── admin/
│   ├── index.php             # Admin metrics & recent commissions dashboard
│   ├── login.php             # Glassmorphism luxury authentication card
│   ├── logout.php            # Secure session termination
│   ├── artworks.php          # Full CRUD portfolio art manager
│   ├── categories.php        # Art category manager
│   └── orders.php            # Client commission tracker & WhatsApp communicator
├── assets/
│   ├── css/
│   │   └── style.css         # Complete editorial luxury design system
│   └── js/
│       └── app.js            # Client interactions, file upload previews
├── components/
│   ├── header.php            # Navigation bar, mobile drawer, dynamic admin CTA
│   └── footer.php            # 4-column footer, developer portfolio credit, WhatsApp
├── config/
│   ├── config.php            # Environment detection, settings & constants
│   ├── database.php          # Singleton PDO connection wrapper
│   └── helpers.php           # Sanitization, CSRF, URL, and upload helpers
├── database/
│   └── schema.sql            # Clean MySQL schema with seeded sample artworks
├── uploads/
│   ├── artworks/             # High-resolution original portfolio imagery
│   └── orders/               # Client reference photograph uploads
├── .env.example              # Template environment configuration
├── .htaccess                 # Apache routing and security rewrite rules
├── about.php                 # Artist profile, philosophy, and studio values
├── artwork.php               # High-res artwork showcase & specification page
├── confirmation.php          # Post-commission reference & bank transfer card
├── contact.php               # General studio inquiry form
├── gallery.php               # Filterable collection showcase
├── index.php                 # Curated homepage, hero feature, commission pillars
├── order.php                 # Bespoke commission intake form
├── payment.php               # Bank transfer instructions & payment policy
├── track.php                 # Client-side order & easel progress tracker
└── README.md                 # Project documentation
```

---

## 👨‍💻 Developer & Author

**Sandun Sameera**  
Full-Stack Web Developer & Software Engineer  
- 🌐 **Portfolio Website:** [sandunsameera.vercel.app](https://sandunsameera.vercel.app/)  
- 💼 **Specialties:** PHP, JavaScript, Full-Stack Architecture, Modern UX/UI Engineering

---

## 📄 License

This project was developed for **SNW ART STUDIO**. All rights reserved. Original artwork photography and studio assets remain the intellectual property of the artist.
