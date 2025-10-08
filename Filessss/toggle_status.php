<?php
header('Content-Type: application/json');
include 'includes/db_connect.php';

$id = intval($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = $conn->prepare("UPDATE registered_users SET status = IF(status='active','inactive','active') WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Status toggled successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to toggle status."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid ID."]);
}
$conn->close();
?>
