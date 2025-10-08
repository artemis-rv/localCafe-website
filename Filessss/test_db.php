<?php
// Test database connection and table structure
header('Content-Type: application/json');

$conn = mysqli_connect('localhost','root','','localcafee');
if(!$conn){ 
  echo json_encode(['error'=>'db', 'message' => 'Database connection failed: ' . mysqli_connect_error()]); 
  exit; 
}

// Test table structure
$result = mysqli_query($conn, "DESCRIBE registered_users");
if(!$result) {
  echo json_encode(['error'=>'table', 'message' => 'Table registered_users does not exist or error: ' . mysqli_error($conn)]);
  exit;
}

$columns = [];
while($row = mysqli_fetch_assoc($result)) {
  $columns[] = $row;
}

// Test data count
$countResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM registered_users");
$count = mysqli_fetch_assoc($countResult)['total'];

// Test actual data
$dataResult = mysqli_query($conn, "SELECT * FROM registered_users LIMIT 5");
$sampleData = [];
while($row = mysqli_fetch_assoc($dataResult)) {
  $sampleData[] = $row;
}

echo json_encode([
  'success' => true,
  'table_structure' => $columns,
  'total_records' => $count,
  'sample_data' => $sampleData
]);

mysqli_close($conn);
?>
