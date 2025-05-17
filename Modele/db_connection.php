<?php
require '../../vendor/autoload.php';
use Dotenv\Dotenv;

// Charger les variables d'environnement (si elles existent en local)
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad(); // Utilise safeLoad pour ne pas planter si .env est absent

// Vérifie si on est sur Heroku
$jawsdb_url = getenv("JAWSDB_URL") ?: $_ENV['JAWSDB_URL'] ?? null;

if ($jawsdb_url) {
    
    $url = parse_url($jawsdb_url);
    $host = $url["host"];
    $port = $url["port"] ?? 3306;
    $dbname = ltrim($url["path"], '/');
    $username = $url["user"];
    $password = $url["pass"];
} else {
    // Variables locales
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $dbname = $_ENV['DB_NAME'] ?? 'eco_ride';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASS'] ?? '';
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
