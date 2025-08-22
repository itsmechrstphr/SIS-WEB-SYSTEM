<?php
// accounts_api.php - POST create account; GET counts
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$pdo = get_pdo();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['counts'])) {
  $students = (int)$pdo->query("SELECT COUNT(*) c FROM accounts WHERE role='student'")->fetch()['c'];
  $teachers = (int)$pdo->query("SELECT COUNT(*) c FROM accounts WHERE role='teacher'")->fetch()['c'];
  $classes  = (int)$pdo->query("SELECT COUNT(*) c FROM classes WHERE is_active=1")->fetch()['c'];
  echo json_encode(['students'=>$students,'teachers'=>$teachers,'classes'=>$classes]);
  exit;
}

if ($method === 'POST') {
  $role = $_POST['role'] ?? 'student';
  $first = trim($_POST['first_name'] ?? '');
  $last  = trim($_POST['last_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  if ($first === '' || $last === '' || $email === '' || $pass === '') {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
    exit;
  }

  // Check duplicate
  $stmt = $pdo->prepare("SELECT id FROM accounts WHERE email = ? LIMIT 1");
  $stmt->execute([$email]);
  if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already exists']);
    exit;
  }

  $hash = password_hash($pass, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("INSERT INTO accounts (role, first_name, last_name, email, password_hash) VALUES (?,?,?,?,?)");
  $stmt->execute([$role,$first,$last,$email,$hash]);

  echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
  exit;
}

http_response_code(405);
echo json_encode(['error' => 'method not allowed']);
