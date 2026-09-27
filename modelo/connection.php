<?php
/**
 * Kosheru - Configuración Centralizada de Base de Datos y Auto-Migración
 * Compatible con XAMPP (Localhost) e InfinityFree (Hosting compartido sin SSH / sin mysqlnd)
 */

// =========================================================================
// CONFIGURACIÓN DE CREDENCIALES
// Para desplegar en InfinityFree, modifica las siguientes 4 variables:
// =========================================================================
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "if0_42541968_logic_nova_db";

// Conexión MySQLi
$conn = @mysqli_connect($db_host, $db_user, $db_pass);

if (!$conn) {
    die("Error crítico de conexión a MySQL: " . mysqli_connect_error());
}

// Asegurar conjunto de caracteres UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Intentar seleccionar la base de datos o crearla si es localhost
if (!@mysqli_select_db($conn, $db_name)) {
    $create_db_sql = "CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (mysqli_query($conn, $create_db_sql)) {
        mysqli_select_db($conn, $db_name);
    } else {
        die("Error al seleccionar la base de datos: " . mysqli_error($conn));
    }
}

/**
 * Función de Auto-Migración y Verificación del Esquema
 * Se ejecuta automáticamente para garantizar que todas las tablas y datos semilla existan.
 */
function setupDatabaseSchema($conn) {
    // 1. Tabla users
    $sql_users = "CREATE TABLE IF NOT EXISTS `users` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(100) NOT NULL,
      `lastname` VARCHAR(100) NOT NULL,
      `username` VARCHAR(50) NOT NULL UNIQUE,
      `email` VARCHAR(120) NOT NULL UNIQUE,
      `password` VARCHAR(255) NOT NULL,
      `rol` ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
      `state` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_users);

    // 2. Tabla categories
    $sql_categories = "CREATE TABLE IF NOT EXISTS `categories` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `slug` VARCHAR(100) NOT NULL UNIQUE,
      `name` VARCHAR(100) NOT NULL,
      `description` TEXT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_categories);

    // 3. Tabla products
    $sql_products = "CREATE TABLE IF NOT EXISTS `products` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_products);

    // 4. Tabla providers
    $sql_providers = "CREATE TABLE IF NOT EXISTS `providers` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(150) NOT NULL,
      `contact_name` VARCHAR(100) NULL,
      `phone` VARCHAR(30) NULL,
      `email` VARCHAR(120) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_providers);

    // 5. Tabla purchases
    $sql_purchases = "CREATE TABLE IF NOT EXISTS `purchases` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_purchases);

    // 6. Tabla purchase_items
    $sql_items = "CREATE TABLE IF NOT EXISTS `purchase_items` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `purchase_id` INT NOT NULL,
      `product_id` INT NOT NULL,
      `product_name` VARCHAR(150) NOT NULL,
      `quantity` INT NOT NULL,
      `unit_price` DECIMAL(12,2) NOT NULL,
      `total_price` DECIMAL(12,2) NOT NULL,
      INDEX `idx_purchase_id` (`purchase_id`),
      INDEX `idx_product_id` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_items);

    // 7. Tabla invoices
    $sql_invoices = "CREATE TABLE IF NOT EXISTS `invoices` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_invoices);

    // 8. Tabla stock_movements
    $sql_stock_mov = "CREATE TABLE IF NOT EXISTS `stock_movements` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `product_id` INT NOT NULL,
      `movement_type` ENUM('entrada', 'salida') NOT NULL,
      `quantity` INT NOT NULL,
      `previous_stock` INT NOT NULL,
      `new_stock` INT NOT NULL,
      `reason` VARCHAR(255) NOT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX `idx_stock_product_id` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_stock_mov);

    // 9. Tabla suggestions
    $sql_suggestions = "CREATE TABLE IF NOT EXISTS `suggestions` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(100) NOT NULL,
      `email` VARCHAR(120) NOT NULL,
      `message` TEXT NOT NULL,
      `status` ENUM('pendiente', 'revisado') NOT NULL DEFAULT 'pendiente',
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX `idx_sugg_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_suggestions);

    // 10. Tabla product_reviews (reseñas y calificación con estrellas)
    $sql_reviews = "CREATE TABLE IF NOT EXISTS `product_reviews` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `product_id` INT NOT NULL,
      `customer_name` VARCHAR(100) NOT NULL,
      `rating` TINYINT UNSIGNED NOT NULL,
      `comment` TEXT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX `idx_review_product_id` (`product_id`),
      CHECK (`rating` BETWEEN 1 AND 5)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    mysqli_query($conn, $sql_reviews);

    // --- MIGRACIÓN DE SEMILLAS (SÓLO SI NO EXISTEN) ---

    $check_admin = mysqli_query($conn, "SELECT id FROM users WHERE username = 'admin' LIMIT 1");
    if ($check_admin && mysqli_num_rows($check_admin) == 0) {
        $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $sql_admin = "INSERT INTO users (name, lastname, username, email, password, rol, state) 
                      VALUES ('Administrador', 'Kosheru', 'admin', 'admin@kosheru.com', '$admin_pass', 'admin', 'activo')";
        mysqli_query($conn, $sql_admin);
    }

    $check_cat = mysqli_query($conn, "SELECT COUNT(*) as total FROM categories");
    $cat_row = mysqli_fetch_assoc($check_cat);
    if ($cat_row['total'] == 0) {
        $sql_cats = "INSERT INTO categories (slug, name, description) VALUES
            ('proteinas-especiales', 'Proteínas Especiales', 'Proteínas de origen vegetal y alta biodisponibilidad orgánicas e importadas.'),
            ('super-alimentos', 'Súper Alimentos', 'Alimentos densos en nutrientes, antioxidantes, fibras y vitaminas puras.'),
            ('tes-condimentos', 'Tés & Condimentos', 'Selección refinada de tés ancestrales, especias y sazonadores naturales.')";
        mysqli_query($conn, $sql_cats);
    }

    $check_prod = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
    $prod_row = mysqli_fetch_assoc($check_prod);
    if ($prod_row['total'] == 0) {
        $seeds = [
            // Proteínas Especiales
            ['Tofu Orgánico Extra Firme', 'Proteínas Especiales', 'JAPÓN 🇯🇵', 18900, 45, 'img/tofu.jpg', 'Tofu de alta pureza rico en proteínas vegetales orgánicas, sin OMG ni conservantes.'],
            ['Proteína de Guisante Aislada 80%', 'Proteínas Especiales', 'BÉLGICA 🇧🇪', 65000, 30, 'img/Proteína Vegetal de Arveja.jpg', 'Proteína aislada de guisante orgánico hipoalergénica con perfil completo de aminoácidos.'],
            ['Proteína de Cáñamo Orgánica (Hemp)', 'Proteínas Especiales', 'CANADÁ 🇨🇦', 72000, 25, 'img/proteina de cañamo.jpg', 'Polvo de semilla de cáñamo natural cargado de edestina, albúmina y Omega 3.'],
            ['Spirulina Crunch Orgánica', 'Proteínas Especiales', 'HAWÁI 🇺🇸', 58000, 40, 'img/Espirulina Azul en Polvo.jpg', 'Microalga spirulina cultivada en aguas oceánicas puras, rica en fitocianina y hierro.'],
            ['Proteína de Semilla de Calabaza', 'Proteínas Especiales', 'AUSTRIA 🇦🇹', 49000, 20, 'img/Proteína Vegetal de Arveja.jpg', 'Concentrado orgánico de semilla de calabaza alto en zinc, magnesio y proteína vegana.'],
            ['Edamame Deshidratado Orgánico', 'Proteínas Especiales', 'JAPÓN 🇯🇵', 24000, 50, 'img/Avena Integral Orgánica.jpg', 'Snack crocante de vainas de soya tierna ricas en fibra soluble y aminoácidos esenciales.'],
            ['Tempeh de Soya Tradicional', 'Proteínas Especiales', 'INDONESIA 🇮🇩', 22000, 15, 'img/Tempeh de Soya Orgánica.jpg', 'Alimento fermentado artesanal súper digestivo y cargado de probióticos y proteína.'],
            ['Proteína de Arroz Integral Germinado', 'Proteínas Especiales', 'TAILANDIA 🇹🇭', 54000, 28, 'img/Proteína Vegetal de Arveja.jpg', 'Proteína hipoalergénica germinada a baja temperatura para conservar nutrientes.'],

            // Súper Alimentos
            ['Maca Andina Negra Orgánica', 'Súper Alimentos', 'PERÚ 🇵🇪', 38000, 60, 'img/Maca Andina en Polvo.jpg', 'Raíz sagrada de los Andes peruano recomendada para vigor, energía y balance hormonal.'],
            ['Polvo de Açai Orgánico Liofilizado', 'Súper Alimentos', 'BRASIL 🇧🇷', 68000, 35, 'img/acai.avif', 'Fruto silvestre de la Amazonía brasileña concentrado en antioxidantes y polifenoles.'],
            ['Semillas de Chía Orgánica Negra', 'Súper Alimentos', 'PARAGUAY 🇵🇾', 16500, 80, 'img/Semillas de Chía Negra.jpg', 'Supersemilla rica en fibra mucilaginosa y ácidos grasos esenciales Omega 3.'],
            ['Quinoa Real Blanca en Grano', 'Súper Alimentos', 'BOLIVIA 🇧🇴', 19800, 75, 'img/Quinoa Orgánica Premium.jpg', 'Grano ancestral de los salares andinos sin gluten con proteína completa.'],
            ['Hifas de Cordyceps Militaris', 'Súper Alimentos', 'TÍBET 🇨🇳', 95000, 12, 'img/cordiceps.jpg', 'Hongo adaptógeno milenario valorado para optimizar el rendimiento físico y respiratorio.'],
            ['Melena de León (Lion\'s Mane)', 'Súper Alimentos', 'JAPÓN 🇯🇵', 88000, 18, 'img/melena de leon.jpg', 'Hongo funcional puro procesado para fomentar la claridad mental y neurogénesis.'],
            ['Baobab en Polvo Orgánico', 'Súper Alimentos', 'SENEGAL 🇸🇳', 42000, 22, 'img/baobab.jpg', 'Superfruta africana rica en vitamina C natural, electrolitos y prebiotic pectina.'],
            ['Bayas de Goji Silvestres', 'Súper Alimentos', 'HIMALAYA 🇳🇵', 36000, 40, 'img/Bayas Goji Premium.jpg', 'Frutos secos del Himalaya conocidos como la baya de la longevidad.'],
            ['Camu Camu Salvaje en Polvo', 'Súper Alimentos', 'PERÚ 🇵🇪', 46000, 30, 'img/camu camu.webp', 'La mayor fuente vegetal de Vitamina C bioasimilable del planeta Tierra.'],
            ['Cacao Puro en Nibs Orgánico', 'Súper Alimentos', 'ECUADOR 🇪🇨', 29000, 50, 'img/nibs de cacao.webp', 'Trozos crudos de grano de cacao fino de aroma rico en magnesio y anandamina.'],

            // Tés & Condimentos
            ['Té Matcha Grado Ceremonial', 'Tés & Condimentos', 'UJI JAPÓN 🇯🇵', 79000, 50, 'img/Matcha Ceremonial Grado A+.png', 'Hojas de té verde finamente molidas en piedra volcánica con L-teanina concentrada.'],
            ['Cúrcuma Cero Toxinas en Polvo', 'Tés & Condimentos', 'INDIA 🇮🇳', 21000, 65, 'img/Cúrcuma Molida Premium.jpg', 'Raíz aromática con 5% garantizado de curcuminoides activos y piperina.'],
            ['Jengibre Dorado Orgánico molido', 'Tés & Condimentos', 'INDIA 🇮🇳', 18500, 60, 'img/Jengibre en Polvo.jpg', 'Sazonador termogénico y digestivo cultivado de forma 100% libre de pesticidas.'],
            ['Té Oolong Gran Reserva Wuyi', 'Tés & Condimentos', 'CHINA 🇨🇳', 45000, 25, 'img/té oolong.jpg', 'Té azul semifermentado artesanal de fragancia orquídea y matices tostados.'],
            ['Sal Rosa del Himalaya Molida', 'Tés & Condimentos', 'PAKISTÁN 🇵🇰', 14000, 100, 'img/sal rosa del himalaya.jpg', 'Sal cristalina sin refinación química con más de 84 oligoelementos naturales.'],
            ['Ashwagandha KSM-66 en Polvo', 'Tés & Condimentos', 'INDIA 🇮🇳', 52000, 35, 'img/ashwagandha.avif', 'Raíz adaptógena india idónea para conciliar calma mental y reducir cortisol.'],
            ['Garcinia Cambogia Extracto', 'Tés & Condimentos', 'SRI LANKA 🇱🇰', 48000, 20, 'img/garcinia.jpg', 'Extracto estandarizado en ácido hidroxicítrico (HCA) natural.']
        ];

        foreach ($seeds as $s) {
            $name = mysqli_real_escape_string($conn, $s[0]);
            $category = mysqli_real_escape_string($conn, $s[1]);
            $origin = mysqli_real_escape_string($conn, $s[2]);
            $price = (float)$s[3];
            $stock = (int)$s[4];
            $image = mysqli_real_escape_string($conn, $s[5]);
            $desc = mysqli_real_escape_string($conn, $s[6]);

            $sql_p = "INSERT INTO products (name, category, origin, price, stock, image, description) 
                      VALUES ('$name', '$category', '$origin', $price, $stock, '$image', '$desc')";
            mysqli_query($conn, $sql_p);
        }
    }

    // --- MIGRACIÓN ACTIVA DE DATOS EXISTENTES (IMÁGENES Y CATEGORÍAS) ---
    $correct_images = [
        'Tofu Orgánico Extra Firme' => 'img/tofu.jpg',
        'Proteína de Guisante Aislada 80%' => 'img/Proteína Vegetal de Arveja.jpg',
        'Proteína de Cáñamo Orgánica (Hemp)' => 'img/proteina de cañamo.jpg',
        'Spirulina Crunch Orgánica' => 'img/Espirulina Azul en Polvo.jpg',
        'Proteína de Semilla de Calabaza' => 'img/Proteína Vegetal de Arveja.jpg',
        'Edamame Deshidratado Orgánico' => 'img/Avena Integral Orgánica.jpg',
        'Tempeh de Soya Tradicional' => 'img/Tempeh de Soya Orgánica.jpg',
        'Proteína de Arroz Integral Germinado' => 'img/Proteína Vegetal de Arveja.jpg',
        'Maca Andina Negra Orgánica' => 'img/Maca Andina en Polvo.jpg',
        'Polvo de Açai Orgánico Liofilizado' => 'img/acai.avif',
        'Semillas de Chía Orgánica Negra' => 'img/chia.webp',
        'Quinoa Real Blanca en Grano' => 'img/Quinoa Orgánica Premium.jpg',
        'Hifas de Cordyceps Militaris' => 'img/cordiceps.jpg',
        'Melena de León (Lion\'s Mane)' => 'img/melena de leon.jpg',
        'Baobab en Polvo Orgánico' => 'img/baobab.jpg',
        'Bayas de Goji Silvestres' => 'img/Bayas Goji Premium.jpg',
        'Camu Camu Salvaje en Polvo' => 'img/camu camu.webp',
        'Cacao Puro en Nibs Orgánico' => 'img/nibs de cacao.webp',
        'Té Matcha Grado Ceremonial' => 'img/Matcha Ceremonial Grado A+.png',
        'Cúrcuma Cero Toxinas en Polvo' => 'img/Cúrcuma Molida Premium.jpg',
        'Jengibre Dorado Orgánico molido' => 'img/Jengibre en Polvo.jpg',
        'Té Oolong Gran Reserva Wuyi' => 'img/té oolong.jpg',
        'Sal Rosa del Himalaya Molida' => 'img/sal rosa del himalaya.jpg',
        'Ashwagandha KSM-66 en Polvo' => 'img/ashwagandha.avif',
        'Garcinia Cambogia Extracto' => 'img/garcinia.jpg'
    ];

    foreach ($correct_images as $prod_name => $correct_img_path) {
        $esc_name = mysqli_real_escape_string($conn, $prod_name);
        $esc_img = mysqli_real_escape_string($conn, $correct_img_path);
        // Actualizamos si contiene .svg o si tiene .../ delante
        mysqli_query($conn, "UPDATE products SET image = '$esc_img' WHERE name = '$esc_name' AND (image LIKE '%.svg' OR image LIKE '.../%')");
    }

    // Corregir también cualquier categoría codificada como entidad HTML
    mysqli_query($conn, "UPDATE products SET category = 'Tés & Condimentos' WHERE category = 'Tés &amp; Condimentos'");

    // Asegurar que las nuevas categorías existan en la tabla categories
    $new_categories = [
        ['vegetarianos', 'Vegetarianos', 'Alimentos 100% vegetarianos libres de ingredientes de origen animal.'],
        ['sin-gluten', 'sin gluten', 'Productos certificados libres de gluten y trazas.'],
        ['veganos', 'Veganos', 'Productos 100% veganos a base de plantas.'],
        ['reemplazos-de-carne', 'reemplazos de carne', 'Sustitutos cárnicos saludables y altos en proteína vegetal.']
    ];
    foreach ($new_categories as $cat) {
        $slug = $cat[0];
        $name = $cat[1];
        $desc = $cat[2];
        $check = mysqli_query($conn, "SELECT id FROM categories WHERE slug = '$slug' LIMIT 1");
        if ($check && mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO categories (slug, name, description) VALUES ('$slug', '$name', '$desc')");
        }
    }

    // Sembrar nuevos productos reales basados en las imágenes en img/
    $new_products = [
        ['Hamburguesa Vegana Gourmet', 'reemplazos de carne', 'COLOMBIA 🇨🇴', 24900, 20, 'img/Hamburguesa Vegana Gourmet.jpg', 'Hamburguesa a base de plantas con sabor ahumado gourmet y alta proteína.'],
        ['Seitán Artesanal', 'reemplazos de carne', 'COLOMBIA 🇨🇴', 16900, 15, 'img/Seitán Artesanal.jpg', 'Carne vegetal de trigo tierna, sazonada con especias naturales y lista para cocinar.'],
        ['Granola Artesanal Premium', 'Súper Alimentos', 'COLOMBIA 🇨🇴', 18500, 35, 'img/Granola Artesanal Premium.jpg', 'Mezcla crujiente de avena, frutos secos y endulzada naturalmente con miel de agave.'],
        ['Leche de Almendras Orgánica', 'Veganos', 'ESTADOS UNIDOS 🇺🇸', 12500, 40, 'img/Leche de Almendras Orgánica.jpg', 'Bebida vegetal cremosa de almendras seleccionadas sin azúcar añadida.'],
        ['Avena Integral Orgánica', 'sin gluten', 'CANADÁ 🇨🇦', 9500, 50, 'img/Avena Integral Orgánica.jpg', 'Hojuelas de avena integral ricas en fibra soluble, ideales para desayunos saludables.']
    ];

    foreach ($new_products as $p) {
        $name = mysqli_real_escape_string($conn, $p[0]);
        $cat = mysqli_real_escape_string($conn, $p[1]);
        $origin = mysqli_real_escape_string($conn, $p[2]);
        $price = (float)$p[3];
        $stock = (int)$p[4];
        $img = mysqli_real_escape_string($conn, $p[5]);
        $desc = mysqli_real_escape_string($conn, $p[6]);

        $check = mysqli_query($conn, "SELECT id FROM products WHERE name = '$name' LIMIT 1");
        if ($check && mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO products (name, category, origin, price, stock, image, description) 
                                 VALUES ('$name', '$cat', '$origin', $price, $stock, '$img', '$desc')");
        }
    }

    // --- SEMBRAR RESEÑAS DE EJEMPLO (SOLO SI LA TABLA ESTÁ VACÍA) ---
    // Reseñas de muestra para que la tienda no se vea "vacía" en la demo.
    // Puedes editarlas o borrarlas desde phpMyAdmin cuando tengas reseñas reales.
    $check_reviews = mysqli_query($conn, "SELECT COUNT(*) as total FROM product_reviews");
    $reviews_row = mysqli_fetch_assoc($check_reviews);
    if ($reviews_row['total'] == 0) {
        $review_seeds = [
            'Tofu Orgánico Extra Firme' => [
                ['Camila R.', 5, 'Excelente textura, no se deshace al cocinar. Ya es parte fija de mi dieta.'],
                ['Andrés M.', 4, 'Buen producto, aunque me gustaría que trajera más unidades por el precio.'],
                ['Laura V.', 5, 'El mejor tofu que he probado en Colombia, se nota el origen japonés.']
            ],
            'Té Matcha Grado Ceremonial' => [
                ['Sofía P.', 5, 'Sabor increíble, nada que ver con el matcha de supermercado.'],
                ['Julián T.', 5, 'Vino muy bien empacado y llegó rápido. Totalmente recomendado.']
            ],
            'Quinoa Real Blanca en Grano' => [
                ['Mariana G.', 4, 'Buena calidad, grano parejo y sin impurezas.'],
                ['Esteban C.', 5, 'La compro cada mes, nunca falla.']
            ],
            'Spirulina Crunch Orgánica' => [
                ['Daniela H.', 4, 'Se siente el cambio de energía después de dos semanas tomándola.'],
                ['Ricardo A.', 3, 'Buen producto pero el sabor es fuerte, hay que mezclarla bien.']
            ],
            'Maca Andina Negra Orgánica' => [
                ['Valentina S.', 5, 'Notable diferencia en energía diaria, y el empaque llegó perfecto.']
            ],
            'Ashwagandha KSM-66 en Polvo' => [
                ['Felipe N.', 5, 'Me ayudó bastante con el estrés del trabajo, la recomiendo.'],
                ['Paula D.', 4, 'Buena calidad, aunque el sabor no es el más agradable solo.']
            ],
            'Cacao Puro en Nibs Orgánico' => [
                ['Nicolás F.', 5, 'Perfecto para agregar al yogurt en las mañanas, sabor intenso y natural.']
            ],
            'Hamburguesa Vegana Gourmet' => [
                ['Isabella K.', 5, 'Sorprendente, la textura es casi idéntica a la carne. Toda mi familia quedó encantada.'],
                ['Santiago O.', 4, 'Muy buena opción vegana, el sabor ahumado se nota bastante.']
            ],
        ];

        foreach ($review_seeds as $product_name => $reviews) {
            $esc_pname = mysqli_real_escape_string($conn, $product_name);
            $res_prod = mysqli_query($conn, "SELECT id FROM products WHERE name = '$esc_pname' LIMIT 1");
            if ($res_prod && mysqli_num_rows($res_prod) > 0) {
                $prod_row = mysqli_fetch_assoc($res_prod);
                $pid = (int)$prod_row['id'];

                foreach ($reviews as $r) {
                    $esc_customer = mysqli_real_escape_string($conn, $r[0]);
                    $rating = (int)$r[1];
                    $esc_comment = mysqli_real_escape_string($conn, $r[2]);
                    mysqli_query($conn, "INSERT INTO product_reviews (product_id, customer_name, rating, comment) 
                                         VALUES ($pid, '$esc_customer', $rating, '$esc_comment')");
                }
            }
        }
    }
}

// =========================================================================
// GUARD DE MIGRACIÓN: solo ejecuta setupDatabaseSchema() UNA VEZ.
// Antes corría en cada request (15-20+ queries por carga de página).
// Ahora se usa un archivo bandera: si ya existe, se salta toda la
// migración con un solo file_exists() (una operación de disco, no de BD).
// Si alguna vez agregas una tabla nueva o más semillas, borra el archivo
// ".schema_migrated" en esta misma carpeta para forzar que se vuelva a
// ejecutar una sola vez.
// =========================================================================
$schema_flag_file = __DIR__ . '/.schema_migrated';

if (!file_exists($schema_flag_file)) {
    setupDatabaseSchema($conn);
    @file_put_contents($schema_flag_file, 'Migración ejecutada: ' . date('Y-m-d H:i:s'));
}
?>
