-- --------------------------------------------------
-- réinitialisation de la base
-- --------------------------------------------------

-- Supprime l'ancienne base pour repartir proprement.
DROP DATABASE IF EXISTS vite_gourmand;

-- Crée la base avec un encodage adapté aux accents.
CREATE DATABASE vite_gourmand
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Sélectionne la base utilisée par les tables suivantes.
USE vite_gourmand;


-- --------------------------------------------------
-- utilisateurs et rôles
-- --------------------------------------------------

-- Enregistre les rôles disponibles dans l'application.
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Enregistre les comptes utilisateurs, employés et administrateurs.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    address VARCHAR(255) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    city VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Enregistre les liens temporaires de réinitialisation du mot de passe.
CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_password_reset_tokens_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- --------------------------------------------------
-- informations affichées sur le site
-- --------------------------------------------------

-- Enregistre les horaires visibles dans le pied de page.
CREATE TABLE opening_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_number TINYINT NOT NULL UNIQUE,
    day_name VARCHAR(20) NOT NULL,
    opening_time TIME NULL,
    closing_time TIME NULL,
    is_closed BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB;


-- --------------------------------------------------
-- catégories des menus
-- --------------------------------------------------

-- Enregistre les thèmes proposés pour les menus.
CREATE TABLE themes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Enregistre les régimes alimentaires disponibles.
CREATE TABLE dietary_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;


-- --------------------------------------------------
-- menus et images
-- --------------------------------------------------

-- Enregistre les menus proposés par le traiteur.
CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    theme_id INT NOT NULL,
    dietary_type_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    conditions TEXT NOT NULL,
    minimum_order_days INT NOT NULL DEFAULT 1,
    available_from DATE NULL,
    available_until DATE NULL,
    minimum_people INT NOT NULL,
    base_price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_menus_theme
        FOREIGN KEY (theme_id)
        REFERENCES themes(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_menus_dietary_type
        FOREIGN KEY (dietary_type_id)
        REFERENCES dietary_types(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- --------------------------------------------------
-- plats et allergènes
-- --------------------------------------------------

-- Enregistre les plats disponibles dans les différents menus.
-- La photo est stockée directement dans la base de données.
CREATE TABLE dishes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    dish_type ENUM('starter', 'main_course', 'dessert') NOT NULL,
    photo LONGBLOB NULL,
    photo_mime_type VARCHAR(100) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

-- Enregistre les allergènes connus.
CREATE TABLE allergens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Associe un plat à un ou plusieurs allergènes.
CREATE TABLE dish_allergen (
    dish_id INT NOT NULL,
    allergen_id INT NOT NULL,

    PRIMARY KEY (dish_id, allergen_id),

    CONSTRAINT fk_dish_allergen_dish
        FOREIGN KEY (dish_id)
        REFERENCES dishes(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_dish_allergen_allergen
        FOREIGN KEY (allergen_id)
        REFERENCES allergens(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- Associe un plat à un ou plusieurs menus.
CREATE TABLE menu_dish (
    menu_id INT NOT NULL,
    dish_id INT NOT NULL,

    PRIMARY KEY (menu_id, dish_id),

    CONSTRAINT fk_menu_dish_menu
        FOREIGN KEY (menu_id)
        REFERENCES menus(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_menu_dish_dish
        FOREIGN KEY (dish_id)
        REFERENCES dishes(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- --------------------------------------------------
-- statuts des commandes
-- --------------------------------------------------

-- Enregistre les différents états possibles d'une commande.
CREATE TABLE order_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;


-- --------------------------------------------------
-- commandes
-- --------------------------------------------------

-- Enregistre les commandes effectuées par les clients.
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    menu_id INT NOT NULL,
    current_status_id INT NOT NULL,

    customer_first_name VARCHAR(100) NOT NULL,
    customer_last_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(190) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,

    delivery_address VARCHAR(255) NOT NULL,
    delivery_postal_code VARCHAR(10) NOT NULL,
    delivery_city VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    delivery_time TIME NOT NULL,

    people_count INT NOT NULL,
    menu_price DECIMAL(10, 2) NOT NULL,
    delivery_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_price DECIMAL(10, 2) NOT NULL,

    cancellation_reason TEXT NULL,
    cancellation_contact_method VARCHAR(50) NULL,

    equipment_loaned BOOLEAN NOT NULL DEFAULT FALSE,
    equipment_return_deadline DATETIME NULL,
    equipment_returned_at DATETIME NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_orders_menu
        FOREIGN KEY (menu_id)
        REFERENCES menus(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_orders_current_status
        FOREIGN KEY (current_status_id)
        REFERENCES order_statuses(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Conserve l'historique des changements de statut.
CREATE TABLE order_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status_id INT NOT NULL,
    changed_by_user_id INT NULL,
    note TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_order_status_history_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_order_status_history_status
        FOREIGN KEY (status_id)
        REFERENCES order_statuses(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_order_status_history_user
        FOREIGN KEY (changed_by_user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;


-- --------------------------------------------------
-- avis clients
-- --------------------------------------------------

-- Enregistre les avis laissés après une commande terminée.
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL UNIQUE,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT NOT NULL,
    moderation_status ENUM('pending', 'approved', 'refused')
        NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_reviews_rating
        CHECK (rating BETWEEN 1 AND 5),

    CONSTRAINT fk_reviews_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_reviews_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- --------------------------------------------------
-- formulaire de contact
-- --------------------------------------------------

-- Enregistre les messages envoyés depuis la page de contact.
CREATE TABLE contact_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;