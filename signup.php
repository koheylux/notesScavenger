<?php
//データベース情報
require('dbinfo.php');

//初期化
$errors = array();

if(isset($_POST['login'])){
  //データ受け取り
  // $name = $_POST['name'];
  $userName = $_POST['userName'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass2 = $_POST['pass2'];

  //エスケープ処理
  // $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
  $userName = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');
  $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
  $pass = htmlspecialchars($pass, ENT_QUOTES, 'UTF-8');
  $pass2 = htmlspecialchars($pass2, ENT_QUOTES, 'UTF-8');

  //名前無し
  // if($name == ''){
  if($userName == ''){
    $errors['name_empty'] = "名前が入力されていません。";
  }

  //email無し
  if($email == ''){
    $errors['email_empty'] = "メールアドレスが入力されていません。";
  }

  //パスワード無し
  if($pass == ''){
    $errors['pass_empty'] = "パスワードが入力されていません。";
  }

  //パスワード不一致
  if($pass != $pass2){
    $errors['pass2_err'] = "パスワードが一致しません";
  }

  //入力問題なし
  if(empty($errors)){
    //暗号化
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    try{
      //SQL文(登録情報の確認)
      $sql = 'SELECT * FROM users WHERE email = ?';
      $stmt = $dbh->prepare($sql);
      $data1[] = $email;
      $stmt->execute($data1);

      $result = $stmt->fetch();
      $stmt = null;

      if($result > 0){
        $errors['registrated_email'] = "このメールアドレスはすでに登録されています。";
        $dbh = null; //データベースから切断
      }
      else{
        //SQL文(データベースに追加)
        $sql = 'INSERT INTO users(usercode, username, email, password, introduction) VALUES (null,?,?,?,?)';
        $stmt = $dbh->prepare($sql);
        $data[] = $userName;
        $data[] = $email;
        $data[] = $hash;
        $data[] = " ";
        $stmt->execute($data);

        //ユーザコードの取り出し&フォルダ作成
        //$stmt = null;
        $sql = "SELECT usercode FROM users WHERE email = :email";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':email',$email);
        $stmt->execute();

        $result = $stmt->fetch();
        $dir_path = "./notebook/".$result['usercode'];
        var_dump(mkdir($dir_path,0755));

        //ユーザコードでプロフィールテーブルにレコードを挿入
        // $sql = "INSERT INTO profile(id,comment) VALUES (:id,null)";
        // $stmt = $dbh->prepare($sql);
        // $stmt->bindValue(":id",$result['usercode']);
        // $stmt->execute();

        $dbh = null; //データベースから切断

        header('Location: login.php');

        exit();
      }
    }
    catch(Exception $e){
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
    <title>新規登録(notesScavenger)</title>

    <meta name="description" content="ノートコミュニケーションサービスログイン"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="stylesheet" type="text/css" href="css/loginstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@500;700;800;900&display=swap" rel="stylesheet">
  </head>
  <body>
    <a href="index.php"><img class="logo" src = "images/notesscavenger_logo.svg"></a>

    <br>
    <div class="login">
      <h3>新規登録</h3><br>
      <form action="" method="post">

        <!-- エラー処理 -->
        <div class="message">
          <?php
          if(!empty($errors['name_empty'])) echo $errors['name_empty']."<br>";
          if(!empty($errors['email_empty'])) echo $errors['email_empty']."<br>";
          if(!empty($errors['pass_empty'])) echo $errors['pass_empty']."<br>";
          if(!empty($errors['pass2_err'])) echo $errors['pass2_err']."<br>";
          if(!empty($errors['registrated_email'])) echo $errors['registrated_email']."<br>";
          ?>
        </div>

        <!-- ユーザ名<br> -->
        <!-- <input type="text" name="name"><br> -->
        <!-- <input type="text" name="userName"><br> -->
        <input type="text" name="userName" placeholder="ユーザ名"><br>


        <!-- メールアドレス<br> -->
        <!-- <input type="text" name="email"><br> -->
        <input type="text" name="email" placeholder="メールアドレス"><br>


        <!-- パスワード<br> -->
        <!-- <input type="password" name="pass"><br> -->
        <input type="password" name="pass" placeholder="パスワード"><br>


        <!-- 確認用パスワード<br> -->
        <!-- <input type="password" name="pass2"><br> -->
        <input type="password" name="pass2" placeholder="確認用パスワード"><br>

        <div class="button" >
          <input type="submit" name="login" value="新規登録" class="login-btn">
          <!-- <a href="login.php"><input type="button" value="戻る" class="login-btn"></a> -->
          <a href="login.php" class="backbutton">アカウントをお持ちの方</a>
        </div>
      </form>
    </div>
  </body>
</html>
