<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "<h1>OK: " . htmlspecialchars(date('c')) . "</h1>";

/**
 * DB疎通の簡易テスト（本番では.env等で秘匿してください）
 * MariaDBは compose のサービス名 "db" で到達できます。
 */
$dsn  = 'mysql:host=db;dbname=myapp;charset=utf8mb4';
$user = 'myapp';
$pass = 'secret';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("
      CREATE TABLE IF NOT EXISTS ping (
        id INT AUTO_INCREMENT PRIMARY KEY,
        msg VARCHAR(64) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $stmt = $pdo->prepare("INSERT INTO ping (msg) VALUES (?)");
    $stmt->execute(['hello from Apache+PHP+MariaDB']);

    $rows = $pdo->query("SELECT COUNT(*) AS cnt FROM ping")->fetch();
    echo "<p>DB OK / ping rows: " . (int)$rows['cnt'] . "</p>";
} catch (Throwable $e) {
    echo "<pre style='color:red'>DB ERROR: " . htmlspecialchars($e->getMessage()) . "</pre>";
}
