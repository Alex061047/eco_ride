<?php
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

// Charger les variables d'environnement (si elles existent en local)
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad(); // Utilise safeLoad pour ne pas planter si .env est absent

// Variables locales en priorité
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? null;
$port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? null;
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? null;
$username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? null;
$password = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? null;

$hasLocalConfig = !empty($host) && !empty($dbname) && !empty($username);

if (!$hasLocalConfig) {
    // Fallback Heroku/JawsDB si la config locale n'est pas disponible
    $jawsdb_url = getenv("JAWSDB_URL") ?: $_ENV['JAWSDB_URL'] ?? null;
    if ($jawsdb_url) {
        $url = parse_url($jawsdb_url);
        $host = $url["host"];
        $port = $url["port"] ?? 3306;
        $dbname = ltrim($url["path"], '/');
        $username = $url["user"];
        $password = $url["pass"];
    }
}

// Valeurs de secours en local
$host = $host ?: 'localhost';
$port = $port ?: '3306';
$dbname = $dbname ?: 'eco_ride';
$username = $username ?: 'root';
$password = $password ?? '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
