<?php
// events_api.php - GET (list), POST (insert), DELETE (remove)
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$pdo = get_pdo();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $stmt = $pdo->query("SELECT id, title, DATE_FORMAT(date_start, '%Y-%m-%d') AS date_start, 
    IFNULL(DATE_FORMAT(date_end, '%Y-%m-%d'),'') AS date_end
    FROM events ORDER BY date_start DESC");
  echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  exit;
}

if ($method === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $date_start = $_POST['date_start'] ?? null;
  $date_end = $_POST['date_end'] ?? null;

  if ($title === '' || $date_start === null) {
    http_response_code(400);
    echo json_encode(['error' => 'title and date_start are required']);
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO events (title, date_start, date_end) VALUES (?, ?, ?)");
  $stmt->execute([$title, $date_start, $date_end ?: null]);

  echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
  exit;
}

if ($method === 'DELETE') {
  parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
  $id = $qs['id'] ?? null;
  if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'missing id']);
    exit;
  }
  $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
  $stmt->execute([$id]);
  echo json_encode(['ok' => true]);
  exit;
}

http_response_code(405);
echo json_encode(['error' => 'method not allowed']);
