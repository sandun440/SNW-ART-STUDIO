CREATE DATABASE IF NOT EXISTS snw_art_studio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE snw_art_studio;

CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE artworks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image_path VARCHAR(255) DEFAULT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_artworks_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_no VARCHAR(30) NOT NULL UNIQUE,
    artwork_id INT UNSIGNED DEFAULT NULL,
    customer_name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    artwork_type ENUM('existing', 'pencil', 'portrait') NOT NULL,
    description TEXT NOT NULL,
    status ENUM('new', 'reviewing', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_artwork FOREIGN KEY (artwork_id) REFERENCES artworks(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE order_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_images_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE studio_settings (
    setting_key VARCHAR(64) PRIMARY KEY,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO studio_settings (setting_key, setting_value) VALUES
    ('bank_name', 'Commercial Bank of Ceylon'),
    ('bank_account_name', 'SNW ART STUDIO'),
    ('bank_account_number', '8001234567'),
    ('bank_branch', 'Colombo Main Branch'),
    ('bank_swift', 'CCEYLKKL'),
    ('whatsapp_number', '94765237415'),
    ('studio_email', 'studio@snwartstudio.com'),
    ('studio_hours', 'Monday — Saturday: 09:00 AM — 06:00 PM (IST)')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

INSERT INTO categories (name, slug) VALUES
    ('Pencil Drawings', 'pencil-drawings'),
    ('Portrait Arts', 'portrait-arts');

-- Temporary password: password. Change it immediately after first login.
INSERT INTO admins (name, email, password_hash) VALUES
    ('Studio Administrator', 'admin@snwartstudio.com', '$2y$10$TzOh8xQd6MZ8BUtuda1eUeugMFtZ00yNF9oUCPaCK.kG0chufMujy');

INSERT INTO artworks (category_id, title, slug, description, price, image_path, featured) VALUES
    (1, 'The Sacred Tusker', 'the-sacred-tusker', 'An intricate traditional graphite study capturing the noble presence, weathered skin textures, and majestic tusks of a Sri Lankan temple tusker, drawn with patient fidelity from a reference photograph.', 350.00, 'uploads/artworks/the-tusker.jpg', 1),
    (1, 'Quiet Study No. 01', 'quiet-study-no-01', 'A study in light, gesture and the quiet poetry of graphite.', 180.00, 'uploads/artworks/study-01.jpg', 1),
    (2, 'The Gaze', 'the-gaze', 'A considered portrait exploring presence through layered charcoal and graphite.', 260.00, 'uploads/artworks/study-02.jpg', 1),
    (1, 'Nocturne in Graphite', 'nocturne-in-graphite', 'Intricate pencil rendering exploring subtle shadow gradations and architectural lines.', 220.00, 'uploads/artworks/study-03.jpg', 1),
    (2, 'Ethereal Whispers', 'ethereal-whispers', 'Expressive charcoal and soft graphite portrait capturing intimate human expression.', 310.00, 'uploads/artworks/study-04.jpg', 1),
    (1, 'The Scholar\'s Reflection', 'the-scholars-reflection', 'A detailed character study crafted with traditional 2B-8B graphite pencils on archival vellum.', 195.00, 'uploads/artworks/study-05.jpg', 0),
    (2, 'Serenity in Shadow', 'serenity-in-shadow', 'High-contrast portrait emphasizing natural lighting, emotional resonance, and deep textural depth.', 285.00, 'uploads/artworks/study-06.jpg', 0);
