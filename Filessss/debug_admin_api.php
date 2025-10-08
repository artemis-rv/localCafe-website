<?php
// Debug version of admin API
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Start session
session_start();

// Debug session info
$debug_info = [
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'cookies' => $_COOKIE,
    'get_params' => $_GET,
    'post_data' => $_POST
];

try {
    // Check admin session
    if(!isset($_SESSION['admin']) && !isset($_COOKIE['admin'])){
        echo json_encode([
            'error' => 'unauthorized', 
            'debug' => $debug_info,
            'message' => 'No admin session found'
        ]);
        exit;
    }

    // Test database connection
    $conn = mysqli_connect('localhost','root','','localcafee');
    if(!$conn){ 
        echo json_encode([
            'error'=>'db', 
            'message' => 'Database connection failed: ' . mysqli_connect_error(),
            'debug' => $debug_info
        ]); 
        exit; 
    }

    // Test table existence
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'registered_users'");
    if(mysqli_num_rows($table_check) == 0) {
        echo json_encode([
            'error' => 'table_not_found',
            'message' => 'Table registered_users does not exist',
            'debug' => $debug_info
        ]);
        exit;
    }

    // Get table structure
    $structure = mysqli_query($conn, "DESCRIBE registered_users");
    $columns = [];
    while($row = mysqli_fetch_assoc($structure)) {
        $columns[] = $row;
    }

    // Get record count
    $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM registered_users");
    $count = mysqli_fetch_assoc($count_result)['total'];

    // Get sample data
    $sample_result = mysqli_query($conn, "SELECT * FROM registered_users LIMIT 3");
    $sample_data = [];
    while($row = mysqli_fetch_assoc($sample_result)) {
        $sample_data[] = $row;
    }

    echo json_encode([
        'success' => true,
        'table_structure' => $columns,
        'total_records' => $count,
        'sample_data' => $sample_data,
        'debug' => $debug_info
    ]);

} catch(Exception $e) {
    echo json_encode([
        'error' => 'exception',
        'message' => $e->getMessage(),
        'debug' => $debug_info
    ]);
}

mysqli_close($conn);
?>
