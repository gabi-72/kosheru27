-- ============================================================
-- KOSHERU - Base de Datos Completa con Estructura y Semillas
-- Compatible con phpMyAdmin para InfinityFree / XAMPP
-- Codificación: UTF-8 / utf8mb4
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Crear o seleccionar la base de datos
CREATE DATABASE IF NOT EXISTS `if0_42541968_logic_nova_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `if0_42541968_logic_nova_db`;

-- ============================================================
-- TABLA: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `lastname` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
  `state` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: categories
-- ============================================================
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: products
-- ============================================================
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `origin` VARCHAR(100) NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `image` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_category` (`category`),
  INDEX `idx_name` (`name`),
  INDEX `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: providers
-- ============================================================
CREATE TABLE IF NOT EXISTS `providers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `contact_name` VARCHAR(100) NULL,
  `phone` VARCHAR(30) NULL,
  `email` VARCHAR(120) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: purchases
-- ============================================================
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `user_name` VARCHAR(150) NOT NULL,
  `user_email` VARCHAR(120) NOT NULL,
  `document_id` VARCHAR(30) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `tax_iva` DECIMAL(12,2) NOT NULL,
  `total_price` DECIMAL(12,2) NOT NULL,
  `order_date` DATE NOT NULL,
  `order_time` TIME NOT NULL,
  `status` ENUM('completado', 'pendiente', 'cancelado') NOT NULL DEFAULT 'completado',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_invoice_number` (`invoice_number`),
  INDEX `idx_order_date` (`order_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: purchase_items
-- ============================================================
CREATE TABLE IF NOT EXISTS `purchase_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `purchase_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `total_price` DECIMAL(12,2) NOT NULL,
  INDEX `idx_purchase_id` (`purchase_id`),
  INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: invoices
-- ============================================================
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(30) NOT NULL UNIQUE,
  `purchase_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(120) NOT NULL,
  `document_id` VARCHAR(30) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `issue_date` DATE NOT NULL,
  `issue_time` TIME NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `tax_iva` DECIMAL(12,2) NOT NULL,
  `total_amount` DECIMAL(12,2) NOT NULL,
  `status` ENUM('completado', 'pendiente', 'cancelado') NOT NULL DEFAULT 'completado',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_invoice_number_inv` (`invoice_number`),
  INDEX `idx_user_id_inv` (`user_id`),
  INDEX `idx_purchase_id_inv` (`purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: stock_movements
-- ============================================================
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `movement_type` ENUM('entrada', 'salida') NOT NULL,
  `quantity` INT NOT NULL,
  `previous_stock` INT NOT NULL,
  `new_stock` INT NOT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_stock_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: suggestions
-- ============================================================
CREATE TABLE IF NOT EXISTS `suggestions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('pendiente', 'revisado') NOT NULL DEFAULT 'pendiente',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_sugg_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Administrador por defecto (password = 'admin123' hasheado con PASSWORD_DEFAULT)
INSERT IGNORE INTO `users` (`name`, `lastname`, `username`, `email`, `password`, `rol`, `state`)
VALUES ('Administrador', 'Kosheru', 'admin', 'admin@kosheru.com', '$2y$10$5e4K4OGZxW5eYBMqF9g4HuPqj7d2TbSBEBCsmT.Ul5JQ3WIi0UM2i', 'admin', 'activo');

-- Categorías
INSERT IGNORE INTO `categories` (`slug`, `name`, `description`) VALUES
('proteinas-especiales', 'Proteínas Especiales', 'Proteínas de origen vegetal y alta biodisponibilidad orgánicas e importadas.'),
('super-alimentos', 'Súper Alimentos', 'Alimentos densos en nutrientes, antioxidantes, fibras y vitaminas puras.'),
('tes-condimentos', 'Tés & Condimentos', 'Selección refinada de tés ancestrales, especias y sazonadores naturales.'),
('vegetarianos', 'Vegetarianos', 'Alimentos 100% vegetarianos libres de ingredientes de origen animal.'),
('sin-gluten', 'sin gluten', 'Productos certificados libres de gluten y trazas.'),
('veganos', 'Veganos', 'Productos 100% veganos a base de plantas.'),
('reemplazos-de-carne', 'reemplazos de carne', 'Sustitutos cárnicos saludables y altos en proteína vegetal.');

-- 30 Productos de Superalimentos Kosheru
INSERT IGNORE INTO `products` (`name`, `category`, `origin`, `price`, `stock`, `image`, `description`) VALUES
('Tofu Orgánico Extra Firme', 'Proteínas Especiales', 'JAPÓN 🇯🇵', 18900, 45, 'img/tofu.jpg', 'Tofu de alta pureza rico en proteínas vegetales orgánicas, sin OMG ni conservantes.'),
('Proteína de Guisante Aislada 80%', 'Proteínas Especiales', 'BÉLGICA 🇧🇪', 65000, 30, 'img/Proteína Vegetal de Arveja.jpg', 'Proteína aislada de guisante orgánico hipoalergénica con perfil completo de aminoácidos.'),
('Proteína de Cáñamo Orgánica (Hemp)', 'Proteínas Especiales', 'CANADÁ 🇨🇦', 72000, 25, 'img/proteina de cañamo.jpg', 'Polvo de semilla de cáñamo natural cargado de edestina, albúmina y Omega 3.'),
('Spirulina Crunch Orgánica', 'Proteínas Especiales', 'HAWÁI 🇺🇸', 58000, 40, 'img/Espirulina Azul en Polvo.jpg', 'Microalga spirulina cultivada en aguas oceánicas puras, rica en fitocianina y hierro.'),
('Proteína de Semilla de Calabaza', 'Proteínas Especiales', 'AUSTRIA 🇦🇹', 49000, 20, 'img/Proteína Vegetal de Arveja.jpg', 'Concentrado orgánico de semilla de calabaza alto en zinc, magnesio y proteína vegana.'),
('Edamame Deshidratado Orgánico', 'Proteínas Especiales', 'JAPÓN 🇯🇵', 24000, 50, 'img/Avena Integral Orgánica.jpg', 'Snack crocante de vainas de soya tierna ricas en fibra soluble y aminoácidos esenciales.'),
('Tempeh de Soya Tradicional', 'Proteínas Especiales', 'INDONESIA 🇮🇩', 22000, 15, 'img/Tempeh de Soya Orgánica.jpg', 'Alimento fermentado artesanal súper digestivo y cargado de probióticos y proteína.'),
('Proteína de Arroz Integral Germinado', 'Proteínas Especiales', 'TAILANDIA 🇹🇭', 54000, 28, 'img/Proteína Vegetal de Arveja.jpg', 'Proteína hipoalergénica germinada a baja temperatura para conservar nutrientes.'),
('Maca Andina Negra Orgánica', 'Súper Alimentos', 'PERÚ 🇵🇪', 38000, 60, 'img/Maca Andina en Polvo.jpg', 'Raíz sagrada de los Andes peruano recomendada para vigor, energía y balance hormonal.'),
('Polvo de Açai Orgánico Liofilizado', 'Súper Alimentos', 'BRASIL 🇧🇷', 68000, 35, 'img/acai.avif', 'Fruto silvestre de la Amazonía brasileña concentrado en antioxidantes y polifenoles.'),
('Semillas de Chía Orgánica Negra', 'Súper Alimentos', 'PARAGUAY 🇵🇾', 16500, 80, 'img/Semillas de Chía Negra.jpg', 'Supersemilla rica en fibra mucilaginosa y ácidos grasos esenciales Omega 3.'),
('Quinoa Real Blanca en Grano', 'Súper Alimentos', 'BOLIVIA 🇧🇴', 19800, 75, 'img/Quinoa Orgánica Premium.jpg', 'Grano ancestral de los salares andinos sin gluten con proteína completa.'),
('Hifas de Cordyceps Militaris', 'Súper Alimentos', 'TÍBET 🇨🇳', 95000, 12, 'img/cordiceps.jpg', 'Hongo adaptógeno milenario valorado para optimizar el rendimiento físico y respiratorio.'),
('Melena de León (Lion\'s Mane)', 'Súper Alimentos', 'JAPÓN 🇯🇵', 88000, 18, 'img/melena de leon.jpg', 'Hongo funcional puro procesado para fomentar la claridad mental y neurogénesis.'),
('Baobab en Polvo Orgánico', 'Súper Alimentos', 'SENEGAL 🇸🇳', 42000, 22, 'img/baobab.jpg', 'Superfruta africana rica en vitamina C natural, electrolitos y prebiotic pectina.'),
('Bayas de Goji Silvestres', 'Súper Alimentos', 'HIMALAYA 🇳🇵', 36000, 40, 'img/Bayas Goji Premium.jpg', 'Frutos secos del Himalaya conocidos como la baya de la longevidad.'),
('Camu Camu Salvaje en Polvo', 'Súper Alimentos', 'PERÚ 🇵🇪', 46000, 30, 'img/camu camu.webp', 'La mayor fuente vegetal de Vitamina C bioasimilable del planeta Tierra.'),
('Cacao Puro en Nibs Orgánico', 'Súper Alimentos', 'ECUADOR 🇪🇨', 29000, 50, 'img/nibs de cacao.webp', 'Trozos crudos de grano de cacao fino de aroma rico en magnesio y anandamina.'),
('Té Matcha Grado Ceremonial', 'Tés & Condimentos', 'UJI JAPÓN 🇯🇵', 79000, 50, 'img/Matcha Ceremonial Grado A+.png', 'Hojas de té verde finamente molidas en piedra volcánica con L-teanina concentrada.'),
('Cúrcuma Cero Toxinas en Polvo', 'Tés & Condimentos', 'INDIA 🇮🇳', 21000, 65, 'img/Cúrcuma Molida Premium.jpg', 'Raíz aromática con 5% garantizado de curcuminoides activos y piperina.'),
('Jengibre Dorado Orgánico molido', 'Tés & Condimentos', 'INDIA 🇮🇳', 18500, 60, 'img/Jengibre en Polvo.jpg', 'Sazonador termogénico y digestivo cultivado de forma 100% libre de pesticidas.'),
('Té Oolong Gran Reserva Wuyi', 'Tés & Condimentos', 'CHINA 🇨🇳', 45000, 25, 'img/té oolong.jpg', 'Té azul semifermentado artesanal de fragancia orquídea y matices tostados.'),
('Sal Rosa del Himalaya Molida', 'Tés & Condimentos', 'PAKISTÁN 🇵🇰', 14000, 100, 'img/sal rosa del himalaya.jpg', 'Sal cristalina sin refinación química con más de 84 oligoelementos naturales.'),
('Ashwagandha KSM-66 en Polvo', 'Tés & Condimentos', 'INDIA 🇮🇳', 52000, 35, 'img/ashwagandha.avif', 'Raíz adaptógena india idónea para conciliar calma mental y reducir cortisol.'),
('Garcinia Cambogia Extracto', 'Tés & Condimentos', 'SRI LANKA 🇱🇰', 48000, 20, 'img/garcinia.jpg', 'Extracto estandarizado en ácido hidroxicítrico (HCA) natural.'),
('Hamburguesa Vegana Gourmet', 'reemplazos de carne', 'COLOMBIA 🇨🇴', 24900, 20, 'img/Hamburguesa Vegana Gourmet.jpg', 'Hamburguesa a base de plantas con sabor ahumado gourmet y alta proteína.'),
('Seitán Artesanal', 'reemplazos de carne', 'COLOMBIA 🇨🇴', 16900, 15, 'img/Seitán Artesanal.jpg', 'Carne vegetal de trigo tierna, sazonada con especias naturales y lista para cocinar.'),
('Granola Artesanal Premium', 'Súper Alimentos', 'COLOMBIA 🇨🇴', 18500, 35, 'img/Granola Artesanal Premium.jpg', 'Mezcla crujiente de avena, frutos secos y endulzada naturalmente con miel de agave.'),
('Leche de Almendras Orgánica', 'Veganos', 'ESTADOS UNIDOS 🇺🇸', 12500, 40, 'img/Leche de Almendras Orgánica.jpg', 'Bebida vegetal cremosa de almendras seleccionadas sin azúcar añadida.'),
('Avena Integral Orgánica', 'sin gluten', 'CANADÁ 🇨🇦', 9500, 50, 'img/Avena Integral Orgánica.jpg', 'Hojuelas de avena integral ricas en fibra soluble, ideales para desayunos saludables.');tógena india idónea para conciliar calma mental y reducir cortisol.'),
('Garcinia Cambogia Extracto', 'Tés & Condimentos', 'SRI LANKA 🇱🇰', 48000, 20, 'imagenes/productos/garcinia.svg', 'Extracto estandarizado en ácido hidroxicítrico (HCA) natural.');
