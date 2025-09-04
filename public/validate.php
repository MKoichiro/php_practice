<?php

function validate(array $request): array {

    $request += array_fill_keys(['name','email','gender','age','home_page','content','agreement'], '');
  
  [
    'name' => $name,
    'email' => $email,
    'gender' => $gender,
    'age' => $age,
    'home_page' => $homePage,
    'content' => $content,
    'agreement' => $agreement
  ] = $request;
    
  $errors = [];
  if (empty($name)) {
    $errors['name_presence'] = '「氏名」は必須です。';
  }
  if (20 < mb_strlen($name)){
    $errors['name_excess'] = '「氏名」は20文字以内にしてください。';
  }
  if (empty($email)) {
    $errors['email_presence'] = '「メールアドレス」は必須です。';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email_invalid'] = '「メールアドレス」は正しい形式で入力してください。';
  }
  if (empty($gender)) {
    $errors['gender'] = '「性別」を選択して下さい。';
  }
  if (empty($age)) {
    $errors['age'] = '「年齢区分」を選択してください。';
  }
  if (!empty($homePage) && !filter_var($homePage, FILTER_VALIDATE_URL)) {
    $errors['home_page_invalid'] = '「ホームページ」は正しい形式で入力してください。';
  }
  if (empty($content)) {
    $errors['content_presence'] = '「お問い合わせ内容」は必須です。';
  }
  if (200 < mb_strlen($content)){
    $errors['content_excess'] = '「お問い合わせ内容」は200文字以内にしてください。';
  }
  if (empty($agreement)) {
    $errors['agreement'] = '規約に同意して下さい。';
  }

  return $errors;
}
