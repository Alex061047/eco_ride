<?php
require_once __DIR__ . '/../db_connection.php';
header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !is_numeric((string) $_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Acces interdit."]);
    exit;
}

$sessionRole = $_SESSION['user_role'] ?? null;
if ($sessionRole !== 'admin') {
    $stmtRole = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = :id LIMIT 1");
    $stmtRole->execute(['id' => (int) $_SESSION['user_id']]);
    $sessionRole = $stmtRole->fetchColumn() ?: null;
    if ($sessionRole !== null) {
        $_SESSION['user_role'] = $sessionRole;
    }
}

if ($sessionRole !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Acces interdit."]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, pseudo, email, role, credit, note FROM utilisateurs ORDER BY pseudo ASC");
$stmt->execute();
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($utilisateurs, JSON_UNESCAPED_UNICODE);
