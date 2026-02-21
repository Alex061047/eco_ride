<?php
declare(strict_types=1);

// Fonctions communes de securite pour le controleur back-end.

function securityJsonHeader(): void
{
    header('Content-Type: application/json; charset=utf-8');
}

function securityAbort(int $status, string $message, string $code = 'error'): void
{
    http_response_code($status);
    echo json_encode(['status' => $code, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function securityRequireMethod(string $method): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== strtoupper($method)) {
        securityAbort(405, 'Methode non autorisee.');
    }
}

function securityStartSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function securityRequireAuth(): int
{
    securityStartSession();

    if (!isset($_SESSION['user_id']) || !is_numeric((string) $_SESSION['user_id'])) {
        securityAbort(401, 'Utilisateur non connecte.', 'not_connected');
    }

    $userId = (int) $_SESSION['user_id'];
    if ($userId <= 0) {
        securityAbort(401, 'Session invalide.', 'not_connected');
    }

    return $userId;
}

function securityGetUserRole(): ?string
{
    securityStartSession();

    if (!empty($_SESSION['user_role'])) {
        return (string) $_SESSION['user_role'];
    }

    if (!isset($_SESSION['user_id']) || !is_numeric((string) $_SESSION['user_id'])) {
        return null;
    }

    // Fallback: lit le role en base et le met en session.
    require __DIR__ . '/../Modele/db_connection.php';
    $stmt = $pdo->prepare('SELECT role FROM utilisateurs WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => (int) $_SESSION['user_id']]);
    $role = $stmt->fetchColumn();

    if ($role === false || $role === null || $role === '') {
        return null;
    }

    $_SESSION['user_role'] = (string) $role;
    return (string) $role;
}

function securityRequireRole(array $allowedRoles): int
{
    $userId = securityRequireAuth();
    $role = securityGetUserRole();

    if ($role === null || !in_array($role, $allowedRoles, true)) {
        securityAbort(403, 'Acces refuse.');
    }

    return $userId;
}

function securityReadJsonBody(bool $required = true): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        if ($required) {
            securityAbort(400, 'Corps JSON vide.');
        }
        return [];
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        securityAbort(400, 'Corps JSON invalide.');
    }

    return $data;
}
