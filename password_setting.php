<?php
//0でエラー非表示、1でエラー表示
ini_set('display_errors', "On");

require("dbinfo.php");

if(!isset($_SESSION)){
  session_start();
}
session_regenerate_id(true); //セッションハイジャック対策


//セッションの確認とGETの受け取り
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
  $userId = $_SESSION['id'];
}else{
  header('Location:logout.php');
  exit();
}

//エラー内容の配列
$errors = array();

if(isset($_POST['change'])){
  $pre_password = $_POST['pre_password'];
  $new_password = $_POST['new_password'];
  $re_password = $_POST['re_password'];

  $pre_password = htmlspecialchars($pre_password, ENT_QUOTES,'UTF-8');
  $new_password = htmlspecialchars($new_password, ENT_QUOTES,'UTF-8');
  $re_password = htmlspecialchars($re_password, ENT_QUOTES,'UTF-8');

  //エラー文
  //$new_passwordが空ならエラーメッセージを表示する
  if($new_password === ""){
    $errors['email_empty'] = "メールアドレスが入力されていません。";
  }
  //$pre_passwordがカラならエラーメッセージを表示する
  if($pre_password === ""){
    $errors['pre_password_empty'] = "現在のパスワードが入力されていません。";
  }
  //$passwordがカラならエラーメッセージを表示する
  if($new_password === ""){
    $errors['new_password_empty'] = "新しいパスワードが入力されていません。";
  }
  //$re_passwordがカラならエラーメッセージを表示する
  if($re_password ===""){
    $errors['re_password_empty'] = "確認用パスワードが入力されていません。";
  }
  //不一致の時エラーメッセージを表示する
  if($new_password !==$re_password){ //もしパスワード1とパスワード2の値が異なるなら
    $errors['password_mismatch'] = "確認用パスワードの入力が一致していません";
  }

  if(empty($errors)){
    try{
      //usersから登録済みのパスワードを取り出す。
      $stmt = $dbh->prepare('SELECT * FROM users WHERE usercode = :userId');
      $stmt->execute(array(':userId' => $userId));
      $result = $stmt->fetch();
      $loginupassword = $result['password'];

      // パスワードチェック
      if(!password_verify($pre_password, $loginupassword )){
        $errors['check'] = ' 現在のパスワードが誤りです。';
      }else{
        //もし入力された現在のパスワードが合致していたら、updateする。
        $sql = "update users set password = :password where usercode = :userId";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(":password",password_hash($new_password, PASSWORD_DEFAULT));
        $stmt->bindValue(":userId",$userId);
        $stmt->execute();
        //クロスサイトリクエストフォージェリ（CSRF）対策
        $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
        
        header('Location: password_changed.php');
      }

        $dbh = null; //データベースから切断

    }catch(PDOException $e){
        echo $e->getMessage();
        exit;
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>パスワード再設定画面</title>
    <meta name="description" content="ノートコミュニケーションサービスログイン"/>
  <meta name="format-detection" content="telephone=no"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>

  <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@500;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
<style>
  body,input{
    vertical-align:baseline;  /* 文字の高さを揃える*/
    font-family: 'M PLUS Rounded 1c', sans-serif;
    background-color: #F0F8FF;
  }

  /* .logo{
    width: 200px;
    margin: 0 auto;
    display:block;
    margin-top: 20px;
    margin-bottom: 20px;
  } */
  .logo{
    width: 150px;
    margin: auto;
    margin-top: 10px;
    display:block;
  }

  @media(min-width:768px){
    .logo{
      width: 200px;
      margin-top: 20px;
    }
  }

  h3{
    background-color: white;
    width: 320px;
    margin: auto;
    margin-top: 20px;
    margin-bottom: 2px;
    padding: 30px 5%;
    text-align: center;
  }

  /* 横幅が 768px 以上であれば */
  @media(min-width:768px){
    h3{
      width: 360px;
      padding: 30px 60px;
    }
  }

  .inputarea{
    background-color: white;
    width: 320px;
    margin: auto;
    padding: 20px 5%;
    text-align: center;
  }

  /* 横幅が 768px 以上であれば */
  @media(min-width:768px){
    .inputarea{
      width: 360px;
      padding: 30px 60px;
    }
  }

  .inputarea p{
    /* margin-bottom: 40px; */
  }

  input{
    margin-bottom: 15px;
    outline: 0;   /* クリック時の青フチ削除 */
    background: #f2f2f2;
    width: 100%;
    box-sizing: border-box; /* padding,borderをwidthに含める */
    height: 50px;
    border: 0;   /* フォームのフチ消し */
    padding:  0 15px;
    font-size: 16px;
  }


  .btn{
    margin-top: 50px;
    font-weight: bold;
    cursor: pointer;
  }

  .btn:hover{
    background: #21538E;
    color: white;
    transition: .4s;
  }

  .message{
    color: red;
    height: auto;
  }

  .link{
    text-align: center;
  }

</style>
  <a href="index.php"><img class="logo" src = "images/notesscavenger_logo.svg"></a>
  <div class="titlearea">
    <h3>パスワードの変更</h3>
  </div>

  <div class="inputarea">

    <p>現在のパスワードと、新しいパスワードを<br>入力してください。</p>

    <form action="" method="POST">
      <div class="message">
        <?php if(!empty($errors['pre_password_empty'])) echo $errors['pre_password_empty']."<br>"; ?>
        <?php if(!empty($errors['new_password_empty'])) echo $errors['new_password_empty']."<br>"; ?>
        <?php if(!empty($errors['re_password_empty'])) echo $errors['re_password_empty']."<br>"; ?>
        <?php if(!empty($errors['password_mismatch'])) echo $errors['password_mismatch']."<br>"; ?>
        <?php if(!empty($errors['check'])) echo $errors['check']."<br>"; ?>
      </div>
      <input type="password" name="pre_password" value="" placeholder="現在のパスワード"><br>
      <input type="password" name="new_password" value="" placeholder="新しいパスワード"><br>
      <input type="password" name="re_password" value="" placeholder="新しいパスワード(確認)"><br>

      <input type="submit" name="change" value="変更"class = "btn">
    </form>
  </div>
</body>
</html>
