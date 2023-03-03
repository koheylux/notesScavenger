<?php
//0でエラー非表示、1でエラー表示
ini_set('display_errors', "On");

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

//セッションの確認とGETの受け取り
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
  $userId = $_SESSION['id'];
  if(isset($_GET["f"]) && isset($_GET['number'])){
    $postUserId = $_GET['number'];
    $notePath = $_GET["f"];
  }else{
    toIndex();
    exit();
  }
}else{
  header('Location:logout.php');
  exit();
}

//パスをdir()によりインスタンス作成
if(file_exists($notePath)){
  $dir_name = dir($notePath);
}else{
  toIndex();
}

//画像のタイプ
$image_type = array("", "image/gif", "image/jpeg", "image/png", "image/swf");
// ディレクトリ内の画像のファイル名を配列に格納する
$file_arr = array();


//投稿者ユーザネームの取得
$sql = "SELECT * FROM users WHERE usercode = :postUserId";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(":postUserId",$postUserId);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if($result > 0){
  $postUserName= $result['username'];
}else{
  toIndex();
}


//すでに登録されているか（読み込み時の処理）
$sql = "SELECT * FROM favorite WHERE notepath = :notepath AND usercode = :userId limit 1";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(":notepath",$dir_name->path);
$stmt->bindValue(":userId",$userId);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if($result > 0){
  $favbutton = "images/love_icon.svg";
}else{
  $favbutton = "images/emplove_icon.svg";
}

//いいねの処理（post後の処理）
if(isset($_POST['fav'])) {
  //すでに登録されているか
  $sql = "SELECT * FROM favorite WHERE notepath = :notepath AND usercode = :userId limit 1";
  $stmt = $dbh->prepare($sql);
  $stmt->bindValue(":notepath",$dir_name->path);
  $stmt->bindValue(":userId",$userId);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if($result>0){//もしすでに登録されてあったら
    $sql = "DELETE FROM favorite WHERE notepath = :notepath AND usercode = :userId";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(":notepath",$dir_name->path);
    $stmt->bindValue(":userId",$userId);
    $stmt->execute();
    $favbutton = "images/emplove_icon.svg";
  }else{
    $sql = "INSERT INTO favorite (notepath,usercode) VALUES (:notepath,:userId)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(":notepath",$dir_name->path);
    $stmt->bindValue(":userId",$userId);
    $stmt->execute();
    $favbutton = "images/love_icon.svg";
  }
}

//閲覧数の計算
$sql = "SELECT count(*) FROM history WHERE notepath = :notepath AND usercode = :userId";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(":notepath",$dir_name->path);
$stmt->bindValue(":userId",$userId);
$stmt->execute();
$count = $stmt->fetch(PDO::FETCH_ASSOC);

if($count['count(*)'] == 0){
  $sql = "INSERT INTO history (notepath,usercode) VALUES (:note,:id)";
  $stmt = $dbh->prepare($sql);
  $stmt->bindValue(":note",$dir_name->path);
  $stmt->bindValue(":id",$userId);
  $stmt->execute();

  $sql = "SELECT views FROM imagetb WHERE notepath = :notepath";
  $stmt = $dbh->prepare($sql);
  $stmt->bindValue(":notepath",$dir_name->path);
  $stmt->execute();
  $views = $stmt->fetch(PDO::FETCH_ASSOC);

  $sql = "UPDATE imagetb SET views = :views WHERE notepath = :note";
  $stmt = $dbh->prepare($sql);
  $stmt->bindValue(":views",$views['views']+1);
  $stmt->bindValue(":note",$dir_name->path);
  $stmt->execute();
}

$sql = "SELECT * FROM imagetb WHERE notepath = :notepath";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(":notepath",$dir_name->path);
$stmt->execute();
$note_info = $stmt->fetch(PDO::FETCH_ASSOC);


//ディレクトリ内のファイルを１つずつ$file_arrに格納
while($file_name = $dir_name->read()) { //ディレクトリ内のすべてのファイルを読み込む
  $path = $dir_name->path."/".$file_name; //$dir_nameのパスと取得したファイル名を結合させ画像のパス作成
  if (@getimagesize($path)) { //ファイルにアクセス可能か？
    $file_arr[] = $path; //画像パスを配列に格納
  }
}
$dir_name->close(); //ディレクトリを閉じる

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
  <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@100;300;400;500;700;800;900&display=swap" rel="stylesheet">
  <!-- <link href="https://fonts.googleapis.com/css2?family=M+PLUS+1p:wght@500;700;800;900&display=swap" rel="stylesheet"> -->
  <link rel="stylesheet" type="text/css" href="css/headerstyle.css">
  <link rel="stylesheet" type="text/css" href="css/viewstyle.css">

  <link rel="shortcut icon" href="favicon.ico">
</head>
<body>
  <!-- header.phpが読み込まれる -->
  <div id="header"></div>

  <main>
    <?php
    if(count($file_arr) > 0) { //配列に1つ以上画像があるか?
      ?>
      <article class="titlearea">
        <a href='javascript:history.back();'><p>< 戻る</p></a>

        <?php
        echo "<h3>".htmlspecialchars($note_info['title'])."</h3>";
        ?>

        <img src="<?php echo $favbutton; ?>" class="fav">

      </article>

      <div class="viewarea">
        <article class="imagearea">
          <section>
          <div class="center">
              <div>
                <?php echo "<img src='".$file_arr[0]."' id='bigimg'>"; ?>
              </div>
              <ul>
                <?php
                for($i=0; $i< count($file_arr); $i++){
                
                  echo "<li data-image=".$file_arr[$i]." style='cursor:pointer' class='thumb'>";
                  echo "<div class='thumbbox' id='".$i."'>";
                  echo "<script>";
                  echo "$('#".$i."').css('background-image','url(".$file_arr[$i].")');";
                  echo "$('#".$i."').css('background-size','cover');";
                  echo "</script>";
                  echo "</div>";
                  echo "</li>";
                }
                ?>
              </ul>
            </div>
          </section>
        </article>

        <article class="infoarea">
          <h4>投稿者</h4>
          <div class="imageinfo">
            <div class="profileicon2"></div>
            <?php
            echo "<a href='profile.php?number=".$postUserId."'><p class='username' style='font-weight: 700; font-size: 18px;'>".$postUserName."</p></a>";
            ?>
          </div>

          <h4>カテゴリー</h4>
          <div class="imageinfo">
            <?php
            echo "<p>".$note_info['subject']."</p>";
            ?>
          </div>

          <h4>キーワード</h4>
          <div class="imageinfo">
            <?php
            echo "<p class='tagview'>".$note_info['tag1']."</p>";
            echo "<p class='tagview'>".$note_info['tag2']."</p>";
            echo "<p class='tagview'>".$note_info['tag3']."</p>";
            ?>
          </div>

          <h4>コメント</h4>
          <div class="imageinfo">
            <p><?php echo $note_info['comment']; ?></p>
          </div>

          <br>
          <div class="eye">
            <img src="images/eyeblack.svg" class="eyeicon" alt="eyeicon">
            <p><?php echo $note_info['views'] ?></p>
          </div>

        </article>
      </div>
    <?php } ?>
  </main>

  <script>
    const thumbs = document.querySelectorAll('.thumb');
    thumbs.forEach(function(item, index) {
      item.onclick = function() {
        document.getElementById('bigimg').src = this.dataset.image;
      }
    });

    $(document).ready(function(){
      $(".fav").on("click", function () {
        var postData = {"fav":true};
        if ($(this).attr("src") === "images/emplove_icon.svg" ){
          $(this).attr("src","images/love_icon.svg");
          $.post(
            "",
            postData,
          );
        } else {
          $(this).attr("src","images/emplove_icon.svg");
          $.post(
            "",
            postData,
          );
        }
      });
    });

  </script>

</body>
</html>
