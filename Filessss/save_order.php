<?php
// save_order.php - Append an order to orders.json in proper JSON format

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Read JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

// Get username from cookie if available
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : null;

// Basic validation for required fields
if (!isset($data['orderId']) || !isset($data['items']) || !is_array($data['items'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing orderId or items']);
    exit;
}

$order = [
    'orderId' => (string)$data['orderId'],
    'username' => $username,
    'deliveryType' => isset($data['deliveryType']) ? (string)$data['deliveryType'] : null,
    'items' => array_values($data['items']),
    'subtotal' => isset($data['subtotal']) ? (float)$data['subtotal'] : null,
    'gst' => isset($data['gst']) ? (float)$data['gst'] : null,
    'grandTotal' => isset($data['grandTotal']) ? (float)$data['grandTotal'] : null,
    'createdAt' => date('c')
];

// Conditionally include booking details only if provided by client
if (isset($data['booking']) && is_array($data['booking'])) {
    $order['booking'] = $data['booking'];
}

$ordersFile = __DIR__ . DIRECTORY_SEPARATOR . 'orders.json';

// Load existing orders or initialize
$orders = [];
if (file_exists($ordersFile)) {
    $contents = file_get_contents($ordersFile);
    if ($contents !== false && strlen($contents) > 0) {
        $decoded = json_decode($contents, true);
        if (is_array($decoded)) {
            $orders = $decoded;
        }
    }
}

// Append and persist with exclusive lock
$orders[] = $order;
$encoded = json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($ordersFile, $encoded, LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to persist order']);
    exit;
}

echo json_encode(['success' => true, 'orderId' => $order['orderId']]);
exit;
?>
