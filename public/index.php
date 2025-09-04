<?php

  require 'validate.php';

  // 1. CSRF(; Cross-Site Request Forgeries)対策
  // なりすましのindex.phpか本物のindex.phpかを見分けるためにセッショントークンを発行しておく
  session_start();
  if(!isset($_SESSION['csrfToken'])) {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrfToken'] = $csrfToken;
  }

  // 2. クリックジャック攻撃の防衛策
  // クリックジャック攻撃...
  //   iframeなどの埋め込みで透明なボタンを本来のボタンに重ねて表示し、攻撃者に何らかの情報を渡す攻撃
  // -> httpヘッダーを操作して防衛する。
  // 施策１: XFO: DENY
  header('X-FRAME-OPTIONS: DENY');
  // -> <iframe>, <frame>, <object>, <embed>でいかなるsrcも拒否するもの。
  // 施策２: XFO: SAMEORIGIN
  // header('X-FRAME-OPTIONS: SAMEORIGIN');
  // -> 完全同一オリジンしか受け付けない指定。
  // 施策３: CSP: frame-src
  // header("Content-Security-Policy: frame-src 'self' https://example.com https://*.example.com");
  // -> 自ページが、自身のサブドメインなど、安全性が担保された特定のオリジンを埋め込むことを許可できる。

  // 3. XSS(cross-site scripting)対策
  // "<" -> "&lt;" などのようにhtmlの特殊文字へ変換。
  // これにより、$strを、コードではなく確実に文字列として扱う。
  // つまり、サニタイズ処理。
  function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }

  // バリデーション結果を格納
  $errors = validate($_POST);

  // ページ切り替えフラグ
  $PAGE_NAME = 'INPUT';
  if (!empty($_POST['btn_confirm']) && empty($errors)) {
    $PAGE_NAME = 'CONFIRM';
  }
  if (!empty($_POST['btn_submit'])) {
    $PAGE_NAME = 'COMPLETE';
  }

  // デバッグ用途
  if (!empty($_POST)) {
    echo '<pre>';
    var_dump($_POST);
    echo '</pre>';
  }
  if (!empty($_SESSION)) {
    echo '<pre>';
    var_dump($_SESSION);
    echo '</pre>';
  }

?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PHPでフォーム</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body>
  <?php if ($PAGE_NAME === 'INPUT') : ?>
    
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h1>入力画面</h1>
      
          <!-- エラー文表示部分 -->
          <?php if (!empty($errors) && !empty($_POST['btn_confirm'])) : ?>
            <ul>
              <?php foreach ($errors as $e) { echo "<li>{$e}</li>"; } ?>
            </ul>
          <?php endif; ?>

          <form method="POST" action="index.php">
            <div class="mb-3">
              <label for="name" class="form-label">氏名</label>
              <input id="name" type="text" name="name" class="form-control" required value="<?php if (!empty($_POST['name'])) { echo h($_POST['name']); } ?>">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">メールアドレス</label>
              <input id="email" type="email" name="email" class="form-control" required value="<?php if (!empty($_POST['email'])) { echo h($_POST['email']); } ?>">
            </div>
            <fieldset class="mb-3">
              <legend class="col-form-label pt-0">性別</legend>
              <div class="form-check-inline">
                <input id="male" type="radio" name="gender" class="form-check-input" value="1" <?php if (!empty($_POST['gender']) && $_POST['gender'] === '1') { echo 'checked'; } ?> />
                <label class="form-check-label" for="male">男性</label>
              </div>
              <div class="form-check-inline">
                <input id="female" type="radio" name="gender" class="form-check-input" value="2" <?php if (!empty($_POST['gender']) && $_POST['gender'] === '2') { echo 'checked'; } ?> />
                <label class="form-check-label" for="female">女性</label>
              </div>
            </fieldset>
            <div class="mb-3">
              <label for="age" class="form-label">年齢</label>
              <select id="age" name="age" class="form-select">
                <option value="">選択してください</option>
                <option value="1" <?php if (!empty($_POST['age']) && $_POST['age'] === '1') { echo 'selected'; } ?>>～19歳</option>
                <option value="2" <?php if (!empty($_POST['age']) && $_POST['age'] === '2') { echo 'selected'; } ?>>20歳～29歳</option>
                <option value="3" <?php if (!empty($_POST['age']) && $_POST['age'] === '3') { echo 'selected'; } ?>>30歳～39歳</option>
                <option value="4" <?php if (!empty($_POST['age']) && $_POST['age'] === '4') { echo 'selected'; } ?>>40歳～49歳</option>
                <option value="5" <?php if (!empty($_POST['age']) && $_POST['age'] === '5') { echo 'selected'; } ?>>50歳～59歳</option>
                <option value="6" <?php if (!empty($_POST['age']) && $_POST['age'] === '6') { echo 'selected'; } ?>>60歳～</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="home_page" class="form-label">ホームページ</label>
              <input id="home_page" type="url" name="home_page" class="form-control" value="<?php if (!empty($_POST['home_page'])) { echo h($_POST['home_page']); } ?>">
            </div>
            <div class="mb-3">
              <label for="content" class="form-label">お問い合わせ内容</label>
              <textarea id="content" name="content" row="5" class="form-control"><?php if (!empty($_POST['content'])) { echo h($_POST['content']); } ?></textarea>
            </div>
            <div class="mb-3 form-check">
              <input id="agreement" type="checkbox" name="agreement" class="form-check-input" value="1"/>
              <label for="agreement" class="form-check-label">利用規約に同意する</label>
            </div>
      
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrfToken']; ?>" />
            <input type="submit" name="btn_confirm" class="btn btn-primary" value="確認画面へ" />
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>


  <?php if ($PAGE_NAME === 'CONFIRM') : ?>
    <?php if ($_POST['csrf_token'] === $_SESSION['csrfToken']) : ?>
      <h1>確認画面</h1>
      <form method="POST" action="index.php">
        <ul>
          <li>氏名: <?php echo h($_POST['name']); ?></li>
          <li>メールアドレス: <?php echo h($_POST['email']); ?></li>
          <li>
            性別:
            <?php echo match ($_POST['gender']) {
              '1' => '男',
              '2' => '女',
            } ?>
          </li>
          <li>
            年齢:
            <?php echo match ($_POST['age']) {
              '1' => '～19歳',
              '2' => '20歳～29歳',
              '3' => '30歳～39歳',
              '4' => '40歳～49歳',
              '5' => '50歳～59歳',
              '6' => '60歳～',
            } ?>
          </li>
          <li>ホームページ: <?php echo h($_POST['home_page']); ?></li>
          <li>お問い合わせ内容: <?php echo h($_POST['content']); ?></li>
        </ul>

        <input type="hidden" name="name" value="<?php echo h($_POST['name']); ?>">
        <input type="hidden" name="email" value="<?php echo h($_POST['email']); ?>">
        <input type="hidden" name="gender" value="<?php echo h($_POST['gender']); ?>">
        <input type="hidden" name="age" value="<?php echo h($_POST['age']); ?>">
        <input type="hidden" name="home_page" value="<?php echo h($_POST['home_page']); ?>">
        <input type="hidden" name="content" value="<?php echo h($_POST['content']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo h($_POST['csrf_token']); ?>">

        <input type="submit" name="btn_back" class="btn btn-secondary" value="戻る" />
        <input type="submit" name="btn_submit" class="btn btn-primary" value="送信する" />
      </form>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($PAGE_NAME === 'COMPLETE') : ?>
    <?php if ($_POST['csrf_token'] === $_SESSION['csrfToken']) : ?>
      <h1>完了画面</h1>
      <p>送信が完了しました。</p>
      <?php unset($_SESSION['csrfToken']) ?>
    <?php endif; ?>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>
