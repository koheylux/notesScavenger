<?php
  if(!isset($_SESSION)){
    session_start();
  }

  $_SESSION = array(); //セッションを空にする

  if(isset($_COOKIE[session_name()]) == true){ //クッキー情報があったら
    setcookie(session_name(), '', time() -42000, '/'); //pc側のクッキーから削除
  }
  session_destroy(); //セッション破棄
?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>notesScavenger</title>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@500;700;800;900&display=swap" rel="stylesheet">
  </head>
  <body style="font-family: 'M PLUS Rounded 1c', sans-serif;">
    <header>
      <a href="index.php">
        <img src = "images/notesscavenger_logo.svg" style="width: 150px; margin: 0 auto; display:block;">
      </a>
    </header>

    <main style="margin-top: 50px;">
      <div style="margin: 50px;">
        <h4 style="border-bottom: 2px solid #999;">ログアウトしました</h4>
        <br>
        下記のリンクより再度ログインを行ってください <br>
        <br>
        <a href="login.php" style="text-decoration: none; font-weight: bold;">ログインする</a>
      </div>
    </main>
  </body>
</html>
