<?php
// order.php - API endpoint to append orders as NDJSON to orders.txt
// If accessed with GET, optionally serve the existing order.html page

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }

    if (!isset($data['orderId']) || !isset($data['items']) || !is_array($data['items'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing orderId or items']);
        exit;
    }

    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : null;

    $order = [
        'orderId' => (string)$data['orderId'],
        'username' => $username,
        'deliveryType' => isset($data['deliveryType']) ? (string)$data['deliveryType'] : null,
        'items' => array_values($data['items']),
        'cartItems' => isset($data['cartItems']) ? $data['cartItems'] : null, // Include cart items from frontend
        'subtotal' => isset($data['subtotal']) ? (float)$data['subtotal'] : null,
        'gst' => isset($data['gst']) ? (float)$data['gst'] : null,
        'grandTotal' => isset($data['grandTotal']) ? (float)$data['grandTotal'] : null,
        'createdAt' => date('c')
    ];

    if (isset($data['booking']) && is_array($data['booking'])) {
        $order['booking'] = $data['booking'];
    }

    $line = json_encode($order, JSON_UNESCAPED_UNICODE);
    if ($line === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to encode order']);
        exit;
    }

    $file = __DIR__ . DIRECTORY_SEPARATOR . 'orders.txt';
    $result = file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    if ($result === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to write to orders.txt']);
        exit;
    }

    echo json_encode(['success' => true, 'orderId' => $order['orderId']]);
    exit;
}

// For GET requests, serve the existing order page for convenience
// If your server does not execute PHP in .html, keeping order.html static is fine
// This simply proxies the content
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'order.html')) {
    readfile(__DIR__ . DIRECTORY_SEPARATOR . 'order.html');
    exit;
}

// Fallback
header('Content-Type: text/plain');
echo 'Order endpoint is ready.';
exit;
?>
