<?php
//０でエラー非表示、1でエラー表示
ini_set( 'display_errors', 1 );

require("dbinfo.php");

if(!isset($_SESSION)){
  session_start();
}
session_regenerate_id(true); //セッションハイジャック対策

//セッションの確認とGETの受け取り
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
  $usercode = $_SESSION['id'];
}else{
  header('Location:logout.php');
  exit();
}

// if(isset($_SESSION['login']) == false){
//   require('logout.php');
//   exit();
// }

// if (isset($_SESSION["id"])) {
//   $usercode = $_SESSION['id'];
// }

$row_check = 0;
$delete_button_flag = false;

if(isset($_POST['note']) && is_array($_POST['note'])){ //削除するノートが選択された場合
	$delete_button_flag = true;
	$path = $_POST['note'];
	$delete_flag = true; //データ削除成功したかどうか

	//glob関数で全てのファイルのパス名を取得

	//$dir = glob($pattern,GLOB_BRACE);
	foreach($path as $select_path){
		//ディレクトリ内全てのphg,jpgファイルのマッチングパターンを用意
		$pattern = "{".$select_path."/*.png,".$select_path."/*.jpg,".$select_path."/*.gif*}";

		//glob関数で全てのファイルのパス名を取得
		$dir = glob($pattern,GLOB_BRACE);

		foreach($dir as $file){
      unlink($file); //ファイル削除
		}

	 	//データベースから該当レコードを削除
    $stmt = null;
    $sql = "DELETE FROM imagetb WHERE notepath = :notepath1";

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':notepath1',$select_path);
		$stmt->execute();

		$sql = "DELETE FROM history WHERE notepath = :notepath2";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(":notepath2",$select_path);
		$stmt->execute();

		$sql = "DELETE FROM favorite WHERE notepath = :notepath3";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(":notepath3",$select_path);
		$stmt->execute();

		rmdir($select_path);

		/*
		if(count(glob($select_path,"/*")) === 0){ //フォルダ内に画像があるかどうか
			if(!(rmdir($select_path))) $delete_flag = false; //フォルダ削除ができなかったら
		}
		 */

	}
	/*
	if($delete_flag === true){
		echo "ノート削除完了しました。<br>";
		echo "<a href='delete.php'>ノート削除選択画面に戻る</a><br>";
 		echo "<a href='index.php'>ホーム画面に戻る</a><br>";
  	unset($dbh);
	}
	 */


}else{

  //if(isset($_SESSION['id'])) $usercode = $_SESSION['id'];

  $sql = "SELECT * FROM imagetb WHERE notepath like :notepath ORDER BY date DESC";
  $stmt = $dbh->prepare($sql);
  $stmt->bindValue(":notepath","./notebook/".$usercode."/%");
  $stmt->execute();

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
    <link rel="stylesheet" type="text/css" href="css/deletestyle.css">
  </head>
  <body>
    <!-- header.phpが読み込まれる -->
    <div id="header"></div>

    <main>
      <article class="titlearea">
        <?php if($delete_button_flag === false)echo "<a href='javascript:history.back();'><p>< 戻る</p></a>";?>
        <h3>ノートを削除する</h3>
      </article>

      <article class="deletearea">
        <div class="tab-content">

          <FORM action="" name="delete_notebook"  METHOD="POST" onsubmit="return check();" >
            <?php if($row_check === 0 && $delete_button_flag === false) echo "<button type='submit' class='deletebtn'>選択したノートを削除</button>";?>
            <ul class="notebookarea">
              <?php
              $row_check = $stmt->rowCount();
              if($row_check === 0 && isset($_POST['note'])){
              echo "<p>削除完了しました。</p><br>";
              echo "<a href='profile.php?number=".$usercode."'>プロフィールへ戻る</a>";
              ?>
              <!--<button onclick="location.href='upload.php'" class='upbtn'>ノートを投稿する</button>-->
              <?php
              }else{
                $i = 0;
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                  $first_picture = glob($row['notepath']."/0.*");
                  echo "<li class='notebook'>";
                  echo "<label for='checkbtn-".$i."' style='cursor: pointer'><div class='notebookbox' id='".$i."'>";
                  echo "<input type='checkbox' name='note[]'  class='checkbtn' value='".$row['notepath']."' id='checkbtn-".$i."'>";
                  echo "<script>";
                  echo "$('#".$i."').css('background-image','url(".$first_picture[0].")');";
                  echo "$('#".$i."').css('background-size','cover');";
                  echo "</script>";
                  echo "</div></label>";
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
          </FORM>
        </div>
      </article>
    </main>

    <script>
    //submitの判定
    function check(){
      var flag = false;
      for(i = 0;i < document.delete_notebook.length;i++){
        if(document.delete_notebook[i].checked){
          flag = true;
        }
      }
      if(!flag){
        alert("ノートが選択されていません。");
        return false;
      }
      else if(!(window.confirm('本当に削除しますか？'))){
        return false;
      }
    }
    </script>
  </body>
</html>
