<?php

require 'db_connection.php';

// 1. ユーザー入力なし query
// $sql = 'SELECT * FROM contacts WHERE id = 1'; // クエリ定義
// $statement = $pdo->query($sql);               // クエリ実行
// $result = $statement->fetchall();             // ローカル変数に結果セット全体を代入

// 2. ユーザー入力あり prepare, bind, execute

// idはフォームから受け取る
$sql = 'SELECT * FROM contacts WHERE id = :id'; // 名前付きプレースホルダー

$statement = $pdo->prepare($sql);                 // プリペアードステートメント
$id = 1;                                          // 本来フォームから受け取る
$statement->bindValue('id', $id, PDO::PARAM_INT); // 紐づけ
$statement->execute();                            // クエリ実行
$result = $statement->fetchall();                 // ローカル変数に結果セット全体を代入

echo '<pre>';
var_dump($result[0]);
echo '<pre>';

// トランザクション
/**
 * トランザクション: 複数のクエリを「まとめて実行」
 * ->「まとめて実行」というのは、どこか途中のクエリで失敗した場合に初期状態に戻す（ロールバックする）、という含蓄がある
 * たとえば、銀行のシステムで、AさんからBさんに振り込む場合、処理のフローは以下のように細分化できる
 * 1. Aさんから引き落とし (UPDATE)
 * 2. Bさんへ入金         (UPDATE)
 * これらを独立して実行してしまうと、1だけが正常実行される致命的な不具合が起こりうる
 * このような場合に1, 2のクエリは「まとめて実行」する（＝「トランザクションを張る」）必要がある。
 * 
 * PDOでは、beginTransaction, commit, rollbackというメソッドを使用する
 */

$pdo->beginTransaction();

try {
  $statement = $pdo->prepare($sql);                 // プリペアードステートメント
  $id = 1;                                          // 本来フォームから受け取る
  $statement->bindValue('id', $id, PDO::PARAM_INT); // 紐づけ
  $statement->execute();                            // クエリ実行「予約」
  $pdo->commit();
} catch (PDOException $error) {
  $pdo->rollback();
}
