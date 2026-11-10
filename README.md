# SNW ART STUDIO

A modern, luxury fine art portfolio and bespoke commission website built with PHP 8+ and MySQL. Crafted specifically for independent portrait artists and fine pencil sketching studios.

---

## ✨ Features

- **Luxury Aesthetic & Editorial Design:** Built with Plus Jakarta Sans & Playfair Display, warm alabaster palettes, and champagne gold accents.
- **Dynamic Portfolio & Gallery:** Filter by categories (Pencil Drawings, Portrait Arts), high-resolution imagery, and detail pages.
- **Bespoke Commission Flow:** Interactive commission type selector, drag-and-drop reference photo upload with instant client-side preview, and reference code generation.
- **Studio Admin Portal:** Secure administration area (`/admin`) to manage collection artworks, categories, client commissions, and order statuses.
- **WhatsApp Integration:** Direct inquiries on artworks and floating WhatsApp support widget with pre-filled messages.
- **Security Protections:** CSRF token verification, PDO prepared statements, restricted uploads execution via `.htaccess`, and session protection.

---

## 🚀 Quick Setup (Localhost)

1. **Clone or Download:**
   Place this repository in your web server root (e.g. `c:/xampp/htdocs/SNW ART STUDIO`).
2. **Start MySQL & Apache:**
   Open XAMPP Control Panel and start **Apache** and **MySQL**.
3. **Import Database:**
   Open phpMyAdmin or MySQL CLI and import `database/schema.sql` into database `snw_art_studio`.
4. **Configuration:**
   Check `config/config.php` for database settings, `BASE_URL`, and `WHATSAPP_NUMBER`.
5. **Access the Site:**
   - Public Website: `http://localhost/SNW%20ART%20STUDIO/index.php`
   - Studio Portal: `http://localhost/SNW%20ART%20STUDIO/admin/login.php`

### Seeded Admin Credentials:
- **Email:** `admin@snwartstudio.com`
- **Password:** `password` *(Change this immediately after first login)*

---

## 📁 Project Structure

```text
├── admin/               # Studio administration dashboard & management
├── assets/              # Modern CSS styles and JavaScript
├── components/          # Reusable header, navigation, and footer
├── config/              # Database connection, helpers, and configurations
├── database/            # Database schema and initial seed data
├── uploads/             # Studio artworks and customer reference uploads
└── public pages         # index, gallery, artwork, about, contact, order, confirmation
```
