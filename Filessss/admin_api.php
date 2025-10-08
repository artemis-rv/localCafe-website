<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
header('Content-Type: application/json');

// Simple admin session check
session_start();
if(!isset($_SESSION['admin']) && !isset($_COOKIE['admin'])){
  echo json_encode([ 
    'error' => 'unauthorized',
    'message' => 'Please login as admin first',
    'session_id' => session_id(),
    'has_session_admin' => isset($_SESSION['admin']),
    'has_cookie_admin' => isset($_COOKIE['admin'])
  ]);
  exit;
}

$conn = mysqli_connect('localhost','root','','localcafee');
if(!$conn){ 
  echo json_encode(['error'=>'db', 'message' => 'Database connection failed: ' . mysqli_connect_error()]); 
  exit; 
}

// Ensure columns exist (no-op if already there)
@mysqli_query($conn, "ALTER TABLE registered_users ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
@mysqli_query($conn, "ALTER TABLE registered_users ADD COLUMN status VARCHAR(20) DEFAULT 'active'");
// Ensure id primary key exists for admin operations
@mysqli_query($conn, "ALTER TABLE registered_users ADD COLUMN id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");

$action = $_GET['action'] ?? '';

function body(){ return json_decode(file_get_contents('php://input'), true) ?: []; }

if($action === 'list'){
  $q = trim($_GET['query'] ?? '');
  $status = trim($_GET['status'] ?? '');
  $where = [];
  $params = [];
  $types = '';
  if($q !== ''){ $where[] = "(name LIKE ? OR email LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; $types .= 'ss'; }
  if($status !== ''){ $where[] = "status = ?"; $params[] = $status; $types .= 's'; }
  $sql = "SELECT id,name,email,phone,role,status FROM registered_users" . (count($where)? (' WHERE '.implode(' AND ',$where)) : '') . " ORDER BY id DESC";
  $stmt = $conn->prepare($sql);
  if($types){ $stmt->bind_param($types, ...$params); }
  $stmt->execute();
  $res = $stmt->get_result();
  $users = [];
  while($row = $res->fetch_assoc()){ $users[] = $row; }
  
  // Debug info (remove in production)
  error_log("SQL Query: " . $sql);
  error_log("Found " . count($users) . " users");
  
  echo json_encode(['users'=>$users, 'debug'=>['sql'=>$sql, 'count'=>count($users)]]);
  exit;
}

if($action === 'create'){
  $b = body();
  $name = trim($b['name']??'');
  $email = trim($b['email']??'');
  $phone = trim($b['phone']??'');
  $password = trim($b['password']??'');
  $role = trim($b['role']??'user');
  if(!$name || !$email || !$password){ echo json_encode(['error'=>'missing']); exit; }
  $hashed = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT INTO registered_users (name,email,password,phone,role,status) VALUES (?,?,?,?,?, 'active')");
  $stmt->bind_param('sssss', $name,$email,$hashed,$phone,$role);
  $ok = $stmt->execute();
  echo json_encode(['success'=>$ok]);
  exit;
}

if($action === 'update'){
  $b = body();
  $id = intval($b['id']??0);
  $name = trim($b['name']??'');
  $email = trim($b['email']??'');
  $phone = trim($b['phone']??'');
  $role = trim($b['role']??'user');
  if(!$id){ echo json_encode(['error'=>'id']); exit; }
  $stmt = $conn->prepare("UPDATE registered_users SET name=?, email=?, phone=?, role=? WHERE id=?");
  $stmt->bind_param('ssssi', $name,$email,$phone,$role,$id);
  $ok = $stmt->execute();
  echo json_encode(['success'=>$ok]);
  exit;
}

if($action === 'status'){
  $b = body();
  $id = intval($b['id']??0);
  $status = trim($b['status']??'active');
  if(!$id){ echo json_encode(['error'=>'id']); exit; }
  $stmt = $conn->prepare("UPDATE registered_users SET status=? WHERE id=?");
  $stmt->bind_param('si', $status,$id);
  $ok = $stmt->execute();
  echo json_encode(['success'=>$ok]);
  exit;
}

if($action === 'delete'){
  $b = body();
  $id = intval($b['id']??0);
  if(!$id){ echo json_encode(['error'=>'id']); exit; }
  $stmt = $conn->prepare("DELETE FROM registered_users WHERE id=?");
  $stmt->bind_param('i',$id);
  $ok = $stmt->execute();
  echo json_encode(['success'=>$ok]);
  exit;
}

echo json_encode(['error'=>'unknown_action']);
?>


