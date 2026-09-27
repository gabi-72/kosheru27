<?php
require_once __DIR__ . '/modelo/connection.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = getCurrentUser();
    $user_id = (int)($user['id'] ?? 0);
    $user_name = trim((string)($user['name'] ?? '')) . ' ' . trim((string)($user['lastname'] ?? ''));
    $user_email = trim((string)($user['email'] ?? ''));

    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);
    if (!$data && !empty($_POST)) {
        $data = $_POST;
    }

    if (!$data) {
        sendJsonResponse(['success' => false, 'message' => 'No se recibieron datos para procesar la compra.'], 400);
    }

    $recipient_name = trim((string)($data['recipient_name'] ?? ''));
    $recipient_email = trim((string)($data['recipient_email'] ?? $user_email));
    $recipient_phone = trim((string)($data['recipient_phone'] ?? $data['phone'] ?? ''));
    $address = trim((string)($data['address'] ?? ''));
    $city = trim((string)($data['city'] ?? ''));
    $department = trim((string)($data['department'] ?? ''));
    $postal_code = trim((string)($data['postal_code'] ?? ''));
    $payment_method = strtolower(trim((string)($data['payment_method'] ?? '')));
    $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];

    $shipping_address = $address;
    if (!empty($department)) {
        $shipping_address .= ' | ' . $department;
    }
    if (!empty($postal_code)) {
        $shipping_address .= ' | CP ' . $postal_code;
    }

    $document_id = trim((string)($data['document_id'] ?? 'N/A'));
    if (empty($recipient_name) || empty($recipient_email) || empty($recipient_phone) || empty($shipping_address) || empty($city) || empty($payment_method) || empty($items)) {
        sendJsonResponse(['success' => false, 'message' => 'Completa todos los datos del receptor, dirección y medio de pago.'], 400);
    }

    mysqli_begin_transaction($conn);

    try {
        $subtotal = 0.0;
        $validated_items = [];

        foreach ($items as $it) {
            $product_id = (int)($it['id'] ?? $it['product_id'] ?? 0);
            $qty = (int)($it['quantity'] ?? 0);

            if ($product_id <= 0 || $qty <= 0) {
                throw new Exception('Producto inválido en el carrito.');
            }

            $query = "SELECT id, name, price, stock FROM products WHERE id = $product_id FOR UPDATE";
            $res = mysqli_query($conn, $query);

            if (!$res || mysqli_num_rows($res) === 0) {
                throw new Exception('El producto solicitado no existe.');
            }

            $prod = mysqli_fetch_assoc($res);
            if ((int)$prod['stock'] < $qty) {
                throw new Exception("Stock insuficiente para '{$prod['name']}'. Disponibles: {$prod['stock']} unidades.");
            }

            $unit_price = (float)$prod['price'];
            $item_total = $unit_price * $qty;
            $subtotal += $item_total;

            $validated_items[] = [
                'product_id' => (int)$prod['id'],
                'product_name' => $prod['name'],
                'current_stock' => (int)$prod['stock'],
                'quantity' => $qty,
                'unit_price' => $unit_price,
                'total_price' => $item_total
            ];
        }

        $tax_iva = round($subtotal * 0.19, 2);
        $total_price = round($subtotal + $tax_iva, 2);
        $invoice_number = generateInvoiceNumber($conn);
        $order_date = date('Y-m-d');
        $order_time = date('H:i:s');

        $esc_inv = mysqli_real_escape_string($conn, $invoice_number);
        $esc_uname = mysqli_real_escape_string($conn, $recipient_name);
        $esc_uemail = mysqli_real_escape_string($conn, $recipient_email);
        $esc_doc = mysqli_real_escape_string($conn, $document_id);
        $esc_addr = mysqli_real_escape_string($conn, $shipping_address);
        $esc_city = mysqli_real_escape_string($conn, $city);
        $esc_phone = mysqli_real_escape_string($conn, $recipient_phone);
        $esc_pay = mysqli_real_escape_string($conn, $payment_method);

        $sql_purchase = "INSERT INTO purchases (
            invoice_number, user_id, user_name, user_email, document_id,
            address, city, phone, payment_method, subtotal, tax_iva, total_price,
            order_date, order_time, status
        ) VALUES (
            '$esc_inv', $user_id, '$esc_uname', '$esc_uemail', '$esc_doc',
            '$esc_addr', '$esc_city', '$esc_phone', '$esc_pay', $subtotal, $tax_iva, $total_price,
            '$order_date', '$order_time', 'completado'
        )";

        if (!mysqli_query($conn, $sql_purchase)) {
            throw new Exception('Error al guardar la orden de compra.');
        }

        $purchase_id = mysqli_insert_id($conn);

        foreach ($validated_items as $v_item) {
            $pid = $v_item['product_id'];
            $pname = mysqli_real_escape_string($conn, $v_item['product_name']);
            $pqty = $v_item['quantity'];
            $puprice = $v_item['unit_price'];
            $ptotal = $v_item['total_price'];
            $prev_stock = $v_item['current_stock'];
            $new_stock = $prev_stock - $pqty;

            $sql_item = "INSERT INTO purchase_items (purchase_id, product_id, product_name, quantity, unit_price, total_price)
                VALUES ($purchase_id, $pid, '$pname', $pqty, $puprice, $ptotal)";
            if (!mysqli_query($conn, $sql_item)) {
                throw new Exception("Error al registrar el detalle del producto {$pname}.");
            }

            $sql_stock = "UPDATE products SET stock = $new_stock WHERE id = $pid";
            if (!mysqli_query($conn, $sql_stock)) {
                throw new Exception("Error al actualizar el inventario del producto {$pname}.");
            }

            $reason = mysqli_real_escape_string($conn, "Venta Factura {$invoice_number}");
            $sql_mov = "INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason)
                VALUES ($pid, 'salida', $pqty, $prev_stock, $new_stock, '$reason')";
            mysqli_query($conn, $sql_mov);
        }

        $sql_invoice = "INSERT INTO invoices (
            invoice_number, purchase_id, user_id, customer_name, customer_email,
            document_id, address, payment_method, issue_date, issue_time,
            subtotal, tax_iva, total_amount, status
        ) VALUES (
            '$esc_inv', $purchase_id, $user_id, '$esc_uname', '$esc_uemail',
            '$esc_doc', '$esc_addr', '$esc_pay', '$order_date', '$order_time',
            $subtotal, $tax_iva, $total_price, 'completado'
        )";

        if (!mysqli_query($conn, $sql_invoice)) {
            throw new Exception('Error al generar la factura electrónica.');
        }

        mysqli_commit($conn);
        logOrderTransaction($invoice_number, $recipient_name, $total_price);

        sendJsonResponse([
            'success' => true,
            'invoice_number' => $invoice_number,
            'total_formatted' => formatCOP($total_price),
            'message' => "¡Compra realizada correctamente! Tu factura es: {$invoice_number}"
        ]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        sendJsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
    }
}

$user = getCurrentUser();
$default_name = trim((string)($user['name'] ?? '') . ' ' . (string)($user['lastname'] ?? ''));
$default_email = (string)($user['email'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Kosheru</title>
    <script>
        (function () {
            var saved = localStorage.getItem('kosheru_theme');
            document.documentElement.setAttribute('data-theme', saved === 'dark' ? 'dark' : 'light');
        })();
    </script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logos/Isotipo transparente.png" type="image/png">
        <style>
        :root {
            --bg: #f5f0e8;
            --card: #ffffff;
            --card-alt: #fdfaf3;
            --gold: #d4a017;
            --orange: #e67e22;
            --text: #4a2c17;
            --muted: #7f6f5f;
            --line: rgba(74,44,23,0.12);
            --danger: #c0392b;
            --success: #27ae60;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #fffdf5 0%, #f5f0e8 100%);
            color: var(--text);
        }
        a { color: var(--orange); text-decoration: none; }
        .checkout-page {
            max-width: 1200px;
            margin: 48px auto;
            padding: 0 20px 40px;
        }
        .checkout-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(280px, 0.8fr);
            gap: 28px;
        }
        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 15px 35px rgba(74,44,23,0.08);
        }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .topbar h1 { margin:0; font-size: 2rem; color: var(--text); }
        .section { margin-top: 24px; }
        .section:first-child { margin-top: 0; }
        .section h2 {
            margin: 0 0 16px;
            color: var(--orange);
            font-size: 1.15rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .grid { display:grid; gap:16px; }
        .grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .field { display:flex; flex-direction:column; gap:8px; }
        .field.full { grid-column: 1 / -1; }
        .field label { font-size: 0.8rem; color: var(--muted); font-weight: 700; text-transform: uppercase; }
        .field input, .field textarea, .field select {
            width:100%;
            background: var(--card-alt);
            border: 1px solid var(--line);
            border-radius: 12px;
            color: var(--text);
            padding: 12px 14px;
            font-size: 1rem;
        }
        .field textarea { min-height: 100px; resize: vertical; }
        .field input:focus, .field textarea:focus, .field select:focus { outline: 2px solid rgba(212,160,23,0.35); border-color: var(--gold); }
        .payment-options {
            display:grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 10px;
            margin-bottom: 18px;
        }
        .payment-option {
            display:flex; align-items:center; gap:10px;
            background: var(--card-alt);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
            cursor: pointer;
            color: var(--text);
        }
        .payment-option input { accent-color: var(--orange); }
        .payment-fields { display:none; margin-top:14px; }
        .payment-fields.active { display:block; }
        .summary-panel {
            position: sticky;
            top: 24px;
            align-self: start;
        }
        .summary-list { display:flex; flex-direction:column; gap:12px; }
        .summary-item {
            display:flex; justify-content:space-between; gap:12px; border-bottom: 1px solid var(--line); padding-bottom: 12px;
        }
        .summary-item .name { font-weight: 700; color: var(--text); }
        .summary-item .meta { color: var(--muted); font-size: 0.8rem; }
        .totals { margin-top: 20px; display:flex; flex-direction:column; gap:8px; }
        .totals .row { display:flex; justify-content:space-between; color: var(--muted); }
        .totals .total { margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--line); font-size: 1.2rem; font-weight: 800; color: var(--text); }
        .submit-btn {
            width:100%;
            margin-top: 26px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--orange), var(--gold));
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.04em;
            padding: 15px 20px;
            cursor: pointer;
            font-size: 1rem;
        }
        .submit-btn:hover { filter: brightness(1.06); }
        .empty-state { color: var(--muted); padding: 20px 0; }
        .status { display:none; }
        .status.error { display:block; color: var(--danger); margin-top: 8px; }
        .status.success { display:block; color: var(--success); }
        html[data-theme="dark"] {
            --card: #221815;
            --card-alt: #23201d;
            --text: #f5f0e8;
            --muted: #b8aba0;
            --line: rgba(255,255,255,0.12);
        }
        html[data-theme="dark"] body {
            background: linear-gradient(180deg, #140e0c 0%, #221815 100%);
        }
        html[data-theme="dark"] .panel {
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        @media (max-width: 860px) {
            .checkout-shell { grid-template-columns: 1fr; }
            .payment-options, .grid.two { grid-template-columns: 1fr; }
        }
    </style>
    </head> 
<body>
        <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="checkout-page">
        <div class="topbar">
            <a href="index.php">← Volver a la tienda</a>
            <h1>Finaliza tu compra</h1>
        </div>

        <div class="checkout-shell">
            <form id="checkoutForm" class="panel" novalidate>
                <div class="section">
                    <h2>Datos del receptor</h2>
                    <div class="grid two">
                        <div class="field">
                            <label for="recipient_name">Nombre y apellido</label>
                            <input id="recipient_name" name="recipient_name" type="text" value="<?= htmlspecialchars($default_name) ?>" required>
                        </div>
                        <div class="field">
                            <label for="recipient_email">Correo electrónico</label>
                            <input id="recipient_email" name="recipient_email" type="email" value="<?= htmlspecialchars($default_email) ?>" required>
                        </div>
                        <div class="field">
                            <label for="recipient_phone">Número de teléfono móvil</label>
                            <input id="recipient_phone" name="recipient_phone" type="tel" placeholder="Ej. 3001234567" required>
                        </div>
                        <div class="field">
                            <label for="city">Ciudad</label>
                            <input id="city" name="city" type="text" placeholder="Ej. Mosquera" required>
                        </div>
                        <div class="field full">
                            <label for="address">Dirección exacta</label>
                            <textarea id="address" name="address" placeholder="Ej. Calle 10 # 13-20, barrio ..." required></textarea>
                        </div>
                        <div class="field">
                            <label for="department">Departamento o municipio</label>
                            <input id="department" name="department" type="text" placeholder="Ej. Cundinamarca" required>
                        </div>
                        <div class="field">
                            <label for="postal_code">Código postal (opcional)</label>
                            <input id="postal_code" name="postal_code" type="text" placeholder="Ej. 111611">
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h2>Datos de pago</h2>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="tarjeta" checked>
                            <span>Tarjeta</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="nequi">
                            <span>Nequi</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="bancolombia">
                            <span>Bancolombia</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="consignacion">
                            <span>Consignación</span>
                        </label>
                    </div>

                    <div class="payment-fields active" data-method="tarjeta">
                        <div class="grid two">
                            <div class="field">
                                <label for="card_name">Nombre del titular</label>
                                <input id="card_name" name="card_name" type="text" placeholder="Ej. Ana Gómez">
                            </div>
                            <div class="field">
                                <label for="card_number">Número de tarjeta</label>
                                <input id="card_number" name="card_number" type="text" inputmode="numeric" placeholder="1234 5678 9012 3456">
                            </div>
                            <div class="field">
                                <label for="card_expiry">Fecha de vencimiento</label>
                                <input id="card_expiry" name="card_expiry" type="text" placeholder="MM/AA">
                            </div>
                            <div class="field">
                                <label for="card_cvv">CVV</label>
                                <input id="card_cvv" name="card_cvv" type="password" inputmode="numeric" placeholder="123">
                            </div>
                        </div>
                    </div>

                    <div class="payment-fields" data-method="nequi">
                        <div class="grid two">
                            <div class="field">
                                <label for="nequi_phone">Número de Nequi</label>
                                <input id="nequi_phone" name="nequi_phone" type="tel" placeholder="Ej. 3001234567">
                            </div>
                            <div class="field">
                                <label for="nequi_name">Nombre del titular</label>
                                <input id="nequi_name" name="nequi_name" type="text" placeholder="Ej. Juan Pérez">
                            </div>
                        </div>
                    </div>

                    <div class="payment-fields" data-method="bancolombia">
                        <div class="grid two">
                            <div class="field">
                                <label for="bancolombia_number">Número de cuenta</label>
                                <input id="bancolombia_number" name="bancolombia_number" type="text" placeholder="Ej. 1234567890">
                            </div>
                            <div class="field">
                                <label for="bancolombia_name">Nombre del titular</label>
                                <input id="bancolombia_name" name="bancolombia_name" type="text" placeholder="Ej. María López">
                            </div>
                        </div>
                    </div>

                    <div class="payment-fields" data-method="consignacion">
                        <div class="grid two">
                            <div class="field">
                                <label for="consignacion_bank">Banco</label>
                                <select id="consignacion_bank" name="consignacion_bank">
                                    <option value="">Seleccione un banco</option>
                                    <option value="Bancolombia">Bancolombia</option>
                                    <option value="Davivienda">Davivienda</option>
                                    <option value="Banco de Bogotá">Banco de Bogotá</option>
                                    <option value="Banco de Occidente">Banco de Occidente</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="consignacion_reference">Número de referencia</label>
                                <input id="consignacion_reference" name="consignacion_reference" type="text" placeholder="Ej. 845321654">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="checkoutMessage" class="status" aria-live="polite"></div>
                <button type="submit" class="submit-btn" id="confirmCheckoutBtn">Confirmar compra</button>
            </form>

            <aside class="panel summary-panel">
                <h2>Resumen del pedido</h2>
                <div id="checkoutCartItems" class="summary-list"></div>
                <div class="totals">
                    <div class="row">
                        <span>Subtotal</span>
                        <strong id="checkoutSubtotal">$0 COP</strong>
                    </div>
                    <div class="row">
                        <span>IVA (19%)</span>
                        <strong id="checkoutIva">$0 COP</strong>
                    </div>
                    <div class="row total">
                        <span>Total</span>
                        <strong id="checkoutTotal">$0 COP</strong>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script src="js/theme.js"></script>
    <script src="js/app.js"></script>
    <script src="js/header.js"></script>
    <script src="js/carrito.js"></script>
    <script src="js/checkout.js"></script>
</body>
</html>
