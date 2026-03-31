-- =============================================================
--  FamShop Assistant — MySQL Database Creation Script
--  Project: FamShop (Laravel MVC + MySQL)
--  Description: Full schema with all tables, keys, and indexes
--  Run this in your MySQL client or phpMyAdmin BEFORE
--  running Laravel migrations, OR use it AS your migrations.
-- =============================================================

-- 1. Create and select the database
CREATE DATABASE IF NOT EXISTS famshop_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE famshop_db;

-- =============================================================
-- TABLE: users
-- The main registered user (the shopper / account owner)
-- =============================================================
CREATE TABLE users (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(100)    NOT NULL,
    email           VARCHAR(100)    NOT NULL UNIQUE,
    password        VARCHAR(255)    NOT NULL,
    gender          VARCHAR(20)     NULL,
    age             TINYINT UNSIGNED NULL,
    profile_photo   VARCHAR(255)    NULL  COMMENT 'stored path in storage/app/public',
    remember_token  VARCHAR(100)    NULL,
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: family_members
-- Each user can have many family member profiles
-- =============================================================
CREATE TABLE family_members (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    name_member     VARCHAR(100)    NOT NULL,
    age             TINYINT UNSIGNED NULL,
    gender          VARCHAR(20)     NULL,
    avatar          VARCHAR(255)    NULL  COMMENT 'path to avatar image',
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_fm_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: allergy_profiles
-- Each family member can have one allergy profile
-- with multiple allergy types (stored one row per type)
-- =============================================================
CREATE TABLE allergy_profiles (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id       BIGINT UNSIGNED NOT NULL,
    allergy_type    VARCHAR(100)    NOT NULL  COMMENT 'e.g. gluten, lactose, nuts, pork, shellfish',
    severity_level  ENUM('mild','moderate','severe') NULL DEFAULT 'moderate',
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_ap_member FOREIGN KEY (member_id)
        REFERENCES family_members(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: budgets
-- Daily / weekly / monthly budgets per family member
-- =============================================================
CREATE TABLE budgets (
    id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id           BIGINT UNSIGNED NOT NULL,
    daily_budget        DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    weekly_budget       DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    monthly_budget      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    daily_spent         DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    weekly_spent        DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    monthly_spent       DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    -- remaining_amount is calculated in PHP (budget - spent)
    created_at          TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_budget_member (member_id),
    CONSTRAINT fk_budget_member FOREIGN KEY (member_id)
        REFERENCES family_members(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: products
-- Cached product data from Open Food Facts API
-- =============================================================
CREATE TABLE products (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    barcode         VARCHAR(50)     NOT NULL UNIQUE,
    pr_name         VARCHAR(255)    NOT NULL,
    brand           VARCHAR(100)    NULL,
    price           DECIMAL(10,2)   NULL,
    image_url       VARCHAR(500)    NULL,
    halal_status    ENUM('halal','haram','unknown') NOT NULL DEFAULT 'unknown',
    raw_ingredients TEXT            NULL  COMMENT 'raw ingredient string from API',
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_barcode (barcode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: ingredients
-- Known ingredient/allergen dictionary
-- =============================================================
CREATE TABLE ingredients (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(100)    NOT NULL,
    aller_name      VARCHAR(100)    NULL  COMMENT 'allergen keyword to match in raw ingredients',
    description     TEXT            NULL,
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: products_ingredients
-- Junction: which ingredients does each product contain?
-- =============================================================
CREATE TABLE products_ingredients (
    product_id      BIGINT UNSIGNED NOT NULL,
    ingredient_id   BIGINT UNSIGNED NOT NULL,
    note            VARCHAR(255)    NULL,
    PRIMARY KEY (product_id, ingredient_id),
    CONSTRAINT fk_pi_product    FOREIGN KEY (product_id)    REFERENCES products(id)    ON DELETE CASCADE,
    CONSTRAINT fk_pi_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: alternative_products
-- Products that can be suggested as safe replacements
-- =============================================================
CREATE TABLE alternative_products (
    id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id              BIGINT UNSIGNED NOT NULL  COMMENT 'the UNSAFE product',
    alternative_product_id  BIGINT UNSIGNED NOT NULL  COMMENT 'the SAFE alternative',
    created_at              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_alt_product FOREIGN KEY (product_id)
        REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_alt_alt FOREIGN KEY (alternative_product_id)
        REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: scan_history
-- Every barcode scan performed by any member
-- =============================================================
CREATE TABLE scan_history (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    member_id       BIGINT UNSIGNED NOT NULL,
    product_id      BIGINT UNSIGNED NOT NULL,
    match_status    ENUM('safe','unsafe','over_budget','unsafe_over_budget') NOT NULL,
    reason          VARCHAR(255)    NULL  COMMENT 'e.g. Contains lactose | Price exceeds weekly budget',
    scan_date       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_sh_user   (user_id),
    INDEX idx_sh_member (member_id),
    CONSTRAINT fk_sh_user    FOREIGN KEY (user_id)    REFERENCES users(id)           ON DELETE CASCADE,
    CONSTRAINT fk_sh_member  FOREIGN KEY (member_id)  REFERENCES family_members(id)  ON DELETE CASCADE,
    CONSTRAINT fk_sh_product FOREIGN KEY (product_id) REFERENCES products(id)        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: shopping_carts
-- One active cart per user at a time
-- =============================================================
CREATE TABLE shopping_carts (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    member_id       BIGINT UNSIGNED NULL  COMMENT 'which member this cart is for',
    total_cost      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    match_status    VARCHAR(50)     NULL,
    purchase_date   TIMESTAMP       NULL  COMMENT 'set on checkout',
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_sc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: cart_items
-- Individual items inside a shopping cart
-- =============================================================
CREATE TABLE cart_items (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cart_id         BIGINT UNSIGNED NOT NULL,
    product_id      BIGINT UNSIGNED NOT NULL,
    quantity        TINYINT UNSIGNED NOT NULL DEFAULT 1,
    total_price     DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    purchase_date   TIMESTAMP       NULL,
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_ci_cart    FOREIGN KEY (cart_id)    REFERENCES shopping_carts(id) ON DELETE CASCADE,
    CONSTRAINT fk_ci_product FOREIGN KEY (product_id) REFERENCES products(id)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: shopping_lists
-- Personal shopping lists per member (NEW — not in original ERD)
-- =============================================================
CREATE TABLE shopping_lists (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id       BIGINT UNSIGNED NOT NULL,
    item_name       VARCHAR(255)    NOT NULL,
    is_checked      BOOLEAN         NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_sl_member FOREIGN KEY (member_id) REFERENCES family_members(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: feedback
-- User ratings and comments about the app
-- =============================================================
CREATE TABLE feedback (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    type            ENUM('rating','suggestion','bug') NOT NULL DEFAULT 'rating',
    rating          TINYINT UNSIGNED NULL  COMMENT '1–5 stars',
    comment         TEXT            NULL,
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_fb_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- TABLE: support_tickets
-- User-reported issues or support requests
-- =============================================================
CREATE TABLE support_tickets (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    message         TEXT            NOT NULL,
    status          ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open',
    ticket_date     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_st_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================
-- SEED DATA: Sample allergen ingredient keywords
-- Run after tables are created to populate the lookup table
-- =============================================================
INSERT INTO ingredients (name, aller_name, description) VALUES
('Milk',                'lactose',      'Dairy milk and all milk derivatives'),
('Butter',              'lactose',      'Dairy butter'),
('Cheese',              'lactose',      'All cheese types'),
('Cream',               'lactose',      'Cream and cream-based products'),
('Whey',                'lactose',      'Whey protein from milk'),
('Wheat',               'gluten',       'Wheat and wheat flour'),
('Barley',              'gluten',       'Barley grain'),
('Rye',                 'gluten',       'Rye grain'),
('Oats',                'gluten',       'Oats (may contain gluten)'),
('Gluten',              'gluten',       'Gluten protein'),
('Peanut',              'nuts',         'Groundnut / peanut'),
('Almond',              'nuts',         'Tree nut'),
('Cashew',              'nuts',         'Tree nut'),
('Walnut',              'nuts',         'Tree nut'),
('Hazelnut',            'nuts',         'Tree nut'),
('Pistachio',           'nuts',         'Tree nut'),
('Pork',                'pork',         'Pork meat'),
('Lard',                'pork',         'Pork fat'),
('Gelatin',             'pork',         'May be derived from pork'),
('Pork Rinds',          'pork',         'Pork skin products'),
('Alcohol',             'halal',        'Any ethanol/alcohol ingredient'),
('Wine',                'halal',        'Wine used in cooking'),
('Vanilla Extract',     'halal',        'May contain alcohol'),
('Egg',                 'egg',          'Chicken egg'),
('Soy',                 'soy',          'Soybean and soy derivatives'),
('Sesame',              'sesame',       'Sesame seeds and oil'),
('Shrimp',              'shellfish',    'Crustacean shellfish'),
('Crab',                'shellfish',    'Crustacean shellfish'),
('Lobster',             'shellfish',    'Crustacean shellfish');


-- =============================================================
-- Quick verification: show all created tables
-- =============================================================
SHOW TABLES;
