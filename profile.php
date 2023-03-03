<?php
  //0でエラー非表示、1でエラー表示
  ini_set( 'display_errors', 1 );

  require("dbinfo.php");

  if(!isset($_SESSION)){
    session_start();
  }
  session_regenerate_id(true); //セッションハイジャック対策

  //エラー時のリダイレクト処理
  function toIndex(){
    header('Location:index.php');
    exit();
  }

  //セッションの確認、GETの受け取り
  if(isset($_SESSION['id']) && isset($_SESSION['name'])){
    $userId = $_SESSION['id'];
    $userName = $_SESSION['name'];
    if(isset($_GET['number'])){
      $postUserId = $_GET['number'];
    }else{
      toIndex();
      exit();
    }
  }else{
    header('Location:logout.php');
    exit();
  }

  //GETの受け取り（タブの値の確認）
  $tab = '/profile|gazou|good/';
  if(isset($_GET['page'])){
    if(preg_match($tab, $_GET['page'])){
      $page = $_GET['page'];
    }else{
      $page = "profile";
    }
  }else{
    $page = "profile";
  }

  
  //いいね用タイトルなど配列
  $file_arr = array();
  $dir_arr = array();
  $title_arr = array();
  $user_arr = array();
  $id_arr = array();
  $views_arr = array();

  $sql = "SELECT username,introduction FROM users WHERE users.usercode = :postUserId";
  $stmt = $dbh->prepare($sql);
  $stmt->bindValue(":postUserId",$postUserId);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if($result>0){
    $postUserName = $result['username'];
    $introduction = $result['introduction'];
  }
  else{
    toIndex();
  }

  if($introduction === " " || empty($introduction)) $introduction = "自己紹介はまだありません。";

  $sql = "SELECT * FROM imagetb WHERE notepath like :notepath ORDER BY date DESC";
  $stmt = $dbh->prepare($sql);
  $stmt->bindValue(":notepath","./notebook/".$postUserId."/%");
  $stmt->execute();
  $row_check = $stmt->rowCount();

  $sql = "SELECT * FROM favorite WHERE usercode = :usercode";
  $stmt2 = $dbh->prepare($sql);
  $stmt2->bindValue(":usercode",$postUserId);
  $stmt2->execute();

  while($row = $stmt2->fetch(PDO::FETCH_ASSOC)){
    $usercode2 = explode("/",$row['notepath']);

    $sql = "SELECT * FROM imagetb WHERE notepath = :notepath";
    $stmt3 = $dbh->prepare($sql);
    $stmt3->bindValue(":notepath",$row['notepath']);
    $stmt3->execute();
    $result = $stmt3->fetch(PDO::FETCH_ASSOC);

    $sql= "SELECT username,usercode FROM users WHERE usercode = :usercode";
    $stmt3 = $dbh->prepare($sql);
    $stmt3->bindValue(":usercode",$usercode2[2]);
    $stmt3->execute();
    $result2 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $first_picture = glob($row['notepath']."/0.*");

    $file_arr[] = $first_picture[0];
    $dir_arr[] = $row['notepath'];
    $title_arr[] = $result['title'];
    $user_arr[] = $result2['username'];
    $id_arr[] = $result2['usercode'];
    $views_arr[] = $result['views'];

    $stmt3 = NULL;
    $result = NULL;
    $result2 = NULL;
  }

?>

<!DOCTYPE html>
<html>
<head>
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
		<link rel="stylesheet" type="text/css" href="css/mypagestyle.css">
	</head>
</head>
<body>
  <!-- header.phpが読み込まれる -->
  <div id="header"></div>

  <main>
    <article class="profilearea">
      <div class="personaldata">
        <div class="bigprficon"><img src="images/usericon.png" alt="usericon" width="70" height="70"></div>
        <p><?php echo $postUserName."<br>"; ?></p>
      </div>
      <?php
        if($postUserId == $userId){
          echo "<a href='profile_setting.php?number=".$postUserId."'>プロフィールの編集</a>";
        }
      ?>
    </article>

    <article class="viewarea">
      <div class="tab-wrap">
        <!-- タブ1 -->
        <?php
          if($page === "profile") echo "<input id='TAB-01' type='radio' name='TAB' class='tab-switch' checked='checked'>";
          else echo "<input id='TAB-01' type='radio' name='TAB' class='tab-switch'>";
        ?>
        <label for="TAB-01" class="tab-label">プロフィール</label>
          <div class="tab-content">
            <h3>自己紹介</h3>
            <div class="selfID">
              <p><?php echo $introduction. "<br>"; ?></p>
            </div>
          </div>

        <!-- タブ2 -->
        <?php
          if($page === "gazou") echo "<input id='TAB-02' type='radio' name='TAB' class='tab-switch' checked='checked'>";
          else echo "<input id='TAB-02' type='radio' name='TAB' class='tab-switch'>";
        ?>
        <label for="TAB-02" class="tab-label">投稿ノート一覧</label>
        <div class="tab-content">
          <?php if($postUserId == $userId && $row_check !== 0){ ?>
            <button onclick="location.href='delete.php'" class="deletebtn">ノートを選択して削除</button>
          <?php } ?>
          <ul class="notebookarea">
            <?php
              if($row_check === 0){
                echo "投稿されたノートはありません。<br>";
                echo "<a href='upload.php'>ノート投稿する</a><br>";
              }else{
                $i = 0;
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                  $first_picture = glob($row['notepath']."/0.*");
                  echo "<li class='notebook'>";
                  echo "<a href='view.php?f=".$row['notepath']."&number=".$postUserId."'>";
                  echo "<div class='notebookbox' id='".$i."'>";
                  echo "<script>";
                  echo "$('#".$i."').css('background-image','url(".$first_picture[0].")');";
                  echo "$('#".$i."').css('background-size','cover');";
                  echo "</script>";
                  echo "<div class='eye'>";
                  echo "<img src='images/eye.svg' class='eyeicon' alt='eyeicon'>";
                  echo "<p>".$row['views']."</p>";
                  echo "</div>";
                  echo "</div></a>";
                  echo "<div class='notebooktitle' id='style-".$i."'>";
                  echo "<div class='style_placeholder1'>";
                  echo $row['title']." ";
                  echo "</div>";
                  echo "<div class='style_placeholder2'></div>";
                  echo "</div>";
                  echo "</li>";
                  $i++;
                }
              }
            ?>
          </ul>
        </div>

        <!-- タブ3 -->
        <?php
          if($page === "good")echo "<input id='TAB-03' type='radio' name='TAB' class='tab-switch' checked='checked'>";
          else echo "<input id='TAB-03' type='radio' name='TAB' class='tab-switch'>";
        ?>
        <label for="TAB-03" class="tab-label">いいね一覧</label>
        <div class="tab-content">
          <ul class="notebookarea">
            <?php
              // for($i = 0;$i < count($file_arr);$i++){
              for($i = count($file_arr)-1; $i>=0; $i--){
                $tmp = $row_check + $i;
                echo "<li class='notebook'>";
                echo "<a href='view.php?f=".$dir_arr[$i]."&number=".$id_arr[$i]."'>";
                echo "<div class='notebookbox' id='".$tmp."'>";
                echo "<script>";
                echo "$('#".$tmp."').css('background-image','url(".$file_arr[$i].")');";
                echo "$('#".$tmp."').css('background-size','cover');";
                echo "</script>";
                echo "<div class='eye'>";
                echo "<img src='images/eye.svg' class='eyeicon' alt='eyeicon'>";
                echo "<p>".$views_arr[$i]."</p>";
                echo "</div>";
                echo "</div></a>";
                echo "<div class='notebooktitle' id='style-".$tmp."'>";
                echo "<div class='style_placeholder1'>";
                echo $title_arr[$i]." ";
                echo "</div>";
                echo "<div class='style_placeholder2'></div>";
                echo "</div>";
                echo "</li>";
              }
            ?>
          </ul>
        </div>

      </div>
    </article>
  </main>
</body>
</html>
