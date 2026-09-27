<?php
/**
 * Kosheru - API de Gestión de Productos e Imágenes Admin
 */
require_once __DIR__ . '/../../../modelo/connection.php';
require_once __DIR__ . '/../../../includes/session.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/helpers.php';

requireAdmin(true);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $res = mysqli_query($conn, $sql);
    $productos = [];

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $row['price_formatted'] = formatCOP($row['price']);
            $productos[] = $row;
        }
    }

    sendJsonResponse(['success' => true, 'productos' => $productos]);
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? 'save';

    if ($action === 'delete') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        if ($product_id > 0) {
            $sql = "DELETE FROM products WHERE id = $product_id";
            if (mysqli_query($conn, $sql)) {
                sendJsonResponse(['success' => true, 'message' => 'Producto eliminado correctamente.']);
            }
        }
        sendJsonResponse(['success' => false, 'message' => 'Error al eliminar el producto.'], 400);
    }

    // Agregar o Editar Producto
    $id = (int)($_POST['id'] ?? 0);
    $name = strip_tags(trim($_POST['name'] ?? ''));
    $category = strip_tags(trim($_POST['category'] ?? ''));
    $origin = strip_tags(trim($_POST['origin'] ?? ''));
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $description = strip_tags(trim($_POST['description'] ?? ''));

    if (empty($name) || empty($category) || empty($origin) || $price <= 0 || $stock < 0) {
        sendJsonResponse(['success' => false, 'message' => 'Por favor completa todos los campos del producto válidamente.'], 400);
    }

    $image_path = '';

    // Procesar Subida Física de Imagen
    if (isset($_FILES['image_file']) && $_FILES['image_file']['name'] !== '') {
        $upload_error = $_FILES['image_file']['error'];
        if ($upload_error !== UPLOAD_ERR_OK) {
            sendJsonResponse(['success' => false, 'message' => 'Error al subir la imagen. Código de error: ' . $upload_error], 400);
        }

        $file_tmp = $_FILES['image_file']['tmp_name'];
        $file_name = $_FILES['image_file']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($file_ext, $allowed_exts)) {
            sendJsonResponse(['success' => false, 'message' => 'Formato de imagen no permitido. Usa JPG, JPEG, PNG o WEBP.'], 400);
        }

        $target_dir = __DIR__ . '/../../../imagenes/productos/';
        if (!is_dir($target_dir)) {
            @mkdir($target_dir, 0755, true);
        }

        // Nombre seguro único
        $unique_name = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $target_file = $target_dir . $unique_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $image_path = 'imagenes/productos/' . $unique_name;
        } else {
            sendJsonResponse(['success' => false, 'message' => 'No se pudo guardar la imagen en el servidor. Revisa los permisos de la carpeta.'], 500);
        }
    }

    $esc_name = mysqli_real_escape_string($conn, $name);
    $esc_cat = mysqli_real_escape_string($conn, $category);
    $esc_orig = mysqli_real_escape_string($conn, $origin);
    $esc_desc = mysqli_real_escape_string($conn, $description);

    if ($id > 0) {
        // Actualizar
        $sql = "UPDATE products SET 
                name = '$esc_name', 
                category = '$esc_cat', 
                origin = '$esc_orig', 
                price = $price, 
                stock = $stock, 
                description = '$esc_desc'";
        
        if (!empty($image_path)) {
            $esc_img = mysqli_real_escape_string($conn, $image_path);
            $sql .= ", image = '$esc_img'";
        }

        $sql .= " WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            sendJsonResponse(['success' => true, 'message' => 'Producto actualizado correctamente.']);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Error al actualizar el producto.'], 500);
        }
    } else {
        // Crear
        if (empty($image_path)) {
            $image_path = 'img/logos/Isologo%20transferente%20(2).png'; // Default fallback
        }
        $esc_img = mysqli_real_escape_string($conn, $image_path);

        $sql = "INSERT INTO products (name, category, origin, price, stock, image, description) 
                VALUES ('$esc_name', '$esc_cat', '$esc_orig', $price, $stock, '$esc_img', '$esc_desc')";

        if (mysqli_query($conn, $sql)) {
            sendJsonResponse(['success' => true, 'message' => '¡Producto agregado correctamente! Aparecerá automáticamente en la tienda.']);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Error al guardar el nuevo producto.'], 500);
        }
    }
}
?>
