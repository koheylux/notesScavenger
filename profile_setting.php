<?php
//０でエラー非表示、1でエラー表示
ini_set( 'display_errors', 1 );

require("dbinfo.php");

if(!isset($_SESSION)){
  session_start();
}
session_regenerate_id(true); //セッションハイジャック対策

//セッションの確認、GETの受け取り
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
  $userId = $_SESSION['id'];
  $userName = $_SESSION['name'];
  if(isset($_GET['number'])){
    $userid = $_GET['number'];
  }else{
    $URL = "profile.php?number=$userId";
    header('Location:'.$URL);
    exit();
  }
}else{
  header('Location:logout.php');
  exit();
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>notes Scavenger -ノートコミュニケーションサービス-</title>

    <meta name="description" content="ノートコミュニケーションサービス"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <script src="js/jquery-3.5.1.min.js"></script>
    <script>
      //共通パーツ読み込み
      $(function() {
        $("#header").load("header.php");
      });
    </script>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+1p:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/headerstyle.css">
    <link rel="stylesheet" type="text/css" href="css/editidstyle.css">
  </head>
  <body>
    <!-- header.phpが読み込まれる -->
    <div id="header"></div>

    <main>
      <article class="titlearea">
        <h3>プロフィールの編集</h3>
      </article>

      <?php
      // 自己紹介文の取得
      $sql = "select introduction from users where usercode= :userId";
      $stmt = $dbh->prepare($sql);
      $stmt->bindValue(":userId",$userId);
      $stmt->execute();
      $result = $stmt->fetch();
      $pre_introduction=$result['introduction'];
      ?>


      <article class="textarea">
        <form method="post">
          <!-- <?php if(!empty($errors['username'])) echo $errors['username']."<br>"; ?> -->

          <?php
          if(isset($_POST['changeIntro'])){
            $userName = htmlspecialchars($_POST['userName'],ENT_QUOTES,'UTF-8');
            $introduction = htmlspecialchars($_POST['introduction'],ENT_QUOTES,'UTF-8');

            //名前の更新
            $_SESSION['name'] = $userName;

            //自己紹介の更新
            $sql = "UPDATE users SET username = :userName, introduction = :introduction WHERE usercode = :userId";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(":userId",$userId);
            $stmt->bindValue(":userName",$userName);
            $stmt->bindValue(":introduction",$introduction);
            $stmt->execute();

            echo "プロフィール変更完了しました。<br>";
            echo "<a href='index.php'>ホーム画面へ</a><br>";
            echo "<a href='profile.php?number=".$userid."'>プロフィール画面へ</a><br>";
            unset($dbh);

          }else{
          ?>
            <div class="renameinputarea">
              <p class="caption">ユーザー名（20文字以内）</p>
              <input type="text" name="userName" maxlength=20  required value=<?php echo $userName; ?> class="inputrename">
            </div>

            <div class="selfidarea">
              <p class="caption">自己紹介（1000文字以内）</p>
              <textarea class="selfid" maxlength=1000 name="introduction" ><?php echo $pre_introduction; ?></textarea>
            </div>

            <div class="submitarea">
              <button type="submit" name="changeIntro" class="uploadbutton">変更する</button>
              <?php echo"<a href='profile.php?number=".$userid."' class='backbutton'>マイページにもどる</a>"; ?>
            </div>
          <?php } ?>
        </form>
      </article>
    </main>
  </body>
</html>
