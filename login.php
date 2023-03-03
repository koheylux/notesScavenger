<?php
  //データベース情報
  require('dbinfo.php');

  //初期化
  $errors = array();
  // $email = "";
  // $pass = "";

  if(isset($_POST['login'])){
    //データ受け取り
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    //エスケープ処理
    $email = htmlspecialchars($email, ENT_QUOTES,'UTF-8');
    $pass = htmlspecialchars($pass, ENT_QUOTES,'UTF-8');

    //emailなし
    if($email == ""){
      $errors['email_empty'] = "メールアドレスが入力されていません。";
    }

    //パスワードなし
    if($pass == ""){
      $errors['pass_empty'] = "パスワードが入力されていません。";
    }

    if(empty($errors)){
      try{
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(":email",$email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result > 0){
          $userName = $result['username'];
          $userId = $result['usercode'];

          // パスワードチェック
          if(!password_verify($pass, $result['password'])){
            $errors['check'] = 'メールアドレスまたはパスワードが誤りです。';
          }
          else{
            session_start();
            $_SESSION['name'] = $userName;
            $_SESSION['id'] = $userId;
            header('Location: index.php');
            exit();
          }

        }else{
          $errors['check'] = 'メールアドレスまたはパスワードが誤りです。';
        }

        $dbh = NULL; //データベースから切断

      }catch(Exception $e){
        print $e->getMessage();
        exit();
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ログイン | notesScavenger</title>

    <meta name="description" content="ノートコミュニケーションサービスログイン"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="stylesheet" type="text/css" href="css/loginstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@500;700;800;900&display=swap" rel="stylesheet">
  </head>
  <body>
    <a href="login.php"><img class="logo" src = "images/notesscavenger_logo.svg"></a>

    <br>

    <div class="signuparea">
      <p>アカウントをお持ちでない方はこちら</p>
      <input type="button" onclick="location.href='signup.php'" value="新規会員登録" class="signup-btn">
    </div>

    <div class="login">
      <h3>notesScavengerにログイン</h3><br>

      <form action="" method="post">

        <!-- エラー処理 -->
        <div class="message">
          <?php
          if(!empty($errors['email_empty'])) echo $errors['email_empty']."<br>";
          if(!empty($errors['pass_empty'])) echo $errors['pass_empty']."<br>";
          if(!empty($errors['check'])) echo $errors['check']."<br>";
          ?>
        </div>

        <br>
        <input type="text" name="email" placeholder="メールアドレス"><br>

        <br>
        <input type="password" name="pass" placeholder="パスワード"><br>

        <input type="submit" name="login" value="ログイン" class="login-btn">
      </form>
    </div>

    <br>
    <!-- <div class="link">
      パスワードをお忘れの方は
      <a href="repass_mail_form.php">こちら</a><br>
    </div> -->


  </body>
</html>
