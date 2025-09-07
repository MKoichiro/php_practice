<?php

// 本来は.env + .gitignoreで秘匿
const DB_DSN = 'mysql:dbname=training;host=db;charset=utf8mb4';
const DB_USER= 'myapp';
CONST DB_PASSWORD = 'secret';

try {
  $pdo = new PDO(
    DB_DSN,
    DB_USER,
    DB_PASSWORD,
    [
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 結果セットを連想配列で受け取る指定。
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // 
      PDO::ATTR_EMULATE_PREPARES => false,              // SQLインジェクション対策
    ]
  );
  echo '接続成功';
} catch (PDOEXception $error) {
  echo $error->getMessage() . "<br>";
  exit();
}
