<?php
function parseEnv($path)
{
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $val = trim($val);
            $val = trim($val, "\"'");
            $env[trim($key)] = $val;
        }
    }
    return $env;
}

$env = parseEnv(__DIR__ . '/.env'); // prilagodi ako ti je .env drugdje

$db_host = $env['DB_HOST'] ?? '127.0.0.1';
$db_name = $env['DB_DATABASE'] ?? 'web_base';
$db_user = $env['DB_USERNAME'] ?? 'root';
$db_pass = $env['DB_PASSWORD'] ?? '';

$pdo = new PDO(
    "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
    $db_user,
    $db_pass
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $pdo->exec("CALL DropYesterdayTable()");
    echo "Tabela za jučerašnji dan je uspješno obrisana.";
} catch (PDOException $e) {
    echo "Greška: " . $e->getMessage();
}
?>