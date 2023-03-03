<?php
//0でエラー非表示、1でエラー表示
ini_set('display_errors', "On");

require("dbinfo.php");

date_default_timezone_set('Asia/Tokyo'); //タイムゾーンを東京に設定

if(!isset($_SESSION)){
	session_start();
}
session_regenerate_id(true); //セッションハイジャック対策

//フォームのリロード対策
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// 	header('Location:toukou.php');
// 	exit;
// }

//セッションの確認とGETの受け取り
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
  $usercode = $_SESSION['id'];
}else{
  header('Location:logout.php');
  exit();
}

//制限など(タイトル名、タグ名)
$title_limit = 20;
$tag_limit = 16;
$comment_limit = 256;

if(isset($_FILES['file']) && isset($_POST['title'])){
	//タグの文字数確認
	$tag = "";
	$tag2 = "";
	$tag3 = "";
	$title = "";
	$comment = "";
	$uploadFlag = 0;

	if(isset($_POST['tag'])){if($_POST['tag']!=="") $tag=htmlspecialchars($_POST['tag'],ENT_QUOTES,"UTF-8");}; //タグが設定されていれば格納
	if(isset($_POST['tag2'])){if($_POST['tag2']!=="") $tag2=htmlspecialchars($_POST['tag2'],ENT_QUOTES,"UTF-8");};
	if(isset($_POST['tag3'])){if($_POST['tag3']!=="") $tag3=htmlspecialchars($_POST['tag3'],ENT_QUOTES,"UTF-8");};
	if(isset($_POST['title'])){if($_POST['title']!=="") $title=htmlspecialchars($_POST['title'],ENT_QUOTES,"UTF-8");};
	if(isset($_POST['comment'])){if($_POST['comment']!=="") $comment=htmlspecialchars($_POST['comment'],ENT_QUOTES,"UTF-8");};


	if(mb_strlen($tag)>$tag_limit || mb_strlen($title)>$title_limit || mb_strlen($tag2)>$tag_limit || mb_strlen($tag3)>$tag_limit || mb_strlen($comment)>$comment_limit ){
		echo "タグ、コメント、タイトルの文字数オーバーです。";
		unset($dbh);
		echo "<button onclick='history.back()'>入力画面に戻る</button>";
		exit();
	}

	// $stmt=null;
	// $note_number=NULL; //ノートディレクトリの番号

	// $sql = "SELECT * FROM imagetb ORDER BY imagecode DESC";
	// $stmt= $dbh->query($sql);
	// $row = $stmt->rowCount();
	// $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
	// if($row == 0){
	// 	$note_number = "1";
	// }
	// else{
	// 	$note_number = $result2['imagecode']+1;
	// }

	// $stmt = NULL;

	//ノートのディレクトリ作成
	$path_name = uniqid(); //被らないようにuniqid()を使用
	// $dir_path = "./notebook/".$usercode."/".$note_number; //$resultと作成したノートディレクトリを結合
	$dir_path = "./notebook/".$usercode."/".$path_name; //$resultと作成したノートディレクトリを結合
	mkdir($dir_path,0775);

	for($i = 0;$i < count($_FILES["file"]["name"]);$i++){
		if(is_uploaded_file($_FILES["file"]["tmp_name"][$i])){

			//拡張子判別
			$mimetype  = mime_content_type($_FILES['file']['tmp_name'][$i]);
			$extension = array_search($mimetype, [ 'jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', ]);
			$set = $i.".".$extension;
			move_uploaded_file($_FILES["file"]["tmp_name"][$i],$dir_path."/".$set);
			$uploadFlag = 1;
		}
	}
	if($uploadFlag==1){
		//ノートの親ディレクトリ、パス、タグをDBに挿入
		$sql = "INSERT INTO imagetb (imagecode,notepath,title,date,subject,tag1,tag2,tag3,views,comment) VALUES (null,:notepath,:title,:date,:subject,:tag1,:tag2,:tag3,:views,:comment)";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(':notepath',$dir_path);
		$stmt->bindValue(':title',$title);
		$stmt->bindValue(':date',date("Y-m-d H:i:s"));
		$stmt->bindValue(':subject',$_POST['subject']);
		$stmt->bindValue(':tag1',$tag);
		$stmt->bindValue(':tag2',$tag2);
		$stmt->bindValue(':tag3',$tag3);
		$stmt->bindValue(':views',0);
		$stmt->bindValue(':comment',$comment);

		$stmt->execute();
	}
  $status = 1;
}else{

	unset($pdo);
  $status = 0;

  exit();
}

?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="js/uploadcmpt.js"></script>
    <link rel="stylesheet" type="text/css" href="css/upcmptstyle.css">

    <title>投稿完了 | notesScavenger</title>
  </head>
  <body>
    <?php if($status == 1) : ?>
      <div class="popup" id="js-popup">
        <div class="popup-inner">
          <h2>投稿が完了しました!</h2>
          <div class="margindiv"></div>
          <button type="button" onclick="location.href='upload.php'">続けて投稿する</button>
					<?php
						$URL = "view.php?f=$dir_path&number=$usercode";
					?>
					<button type='button' onclick="location.href='<?php echo $URL; ?>'">投稿したノートをみる</button>
          <a href="index.php">ホームに戻る</a>
        </div>
        <div class="black-background"></div>
      </div>
    <?php else : ?>
      <div class="popup" id="js-popup">
        <div class="popup-inner">
          <h2>エラー発生</h2>
          <p class="errormsg">ノートが投稿できませんでした。<br>もう一度最初からやり直してください。</p>
          <a href="index.php" class="">ホームに戻る</a>
        </div>
        <div class="black-background"></div>
      </div>
    <?php endif; ?>

  </body>
</html>
