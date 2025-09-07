<?php

function insertContact($request) {
  // テスト用ダミーデータ
  // $dummy_request = [
  //   'name' => 'なまえ',
  //   'gender' => '1',
  //   'email' => 'test@test.com',
  //   'age' => '30',
  //   'url' => 'http://localhost:8081',
  //   'content' => 'あいうえお',
  // ];
  // var_dump($dummy_request);
  var_dump($request);
  // exit();

  // 1. 接続
  require 'db_connection.php';

  // 2. クエリ準備
  $keys = array_keys($request);
  $columns = implode(', ', $keys);
  $values = ':' . implode(', :', $keys);
  $sql = 'INSERT INTO contacts (' . $columns . ') VALUES (' . $values . ');';
  var_dump($columns);
  var_dump($values);
  echo '<br>' . $sql;

  // 3. INSERT 処理
  try {
    $statement = $pdo->prepare($sql); // プリペアードステートメント
    $statement->execute($request);    // クエリ実行
    // $statement->fetchall(); // ローカル変数に結果セット全体を代入
    // return $result;
    echo '<br>' . 'INSERT 完了';
  } catch (PDOException $error) {
    echo '<br>' . 'INSERT 失敗';
    echo $error->getMessage();
    exit();
  }
}
