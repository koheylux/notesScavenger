<?php
//0でエラー非表示、1でエラー表示
ini_set('display_errors', "On");

require("dbinfo.php");

if(!isset($_SESSION)){
  session_start();
}
session_regenerate_id(true); //セッションハイジャック対策

//セッションの確認
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
  $usercode = $_SESSION['id'];
}else{
  header('Location:logout.php');
  exit();
}

$file_arr = array();
$dir_arr = array();
$title_arr = array();
$user_arr = array();
$id_arr = array();
$views_arr = array();

if(isset($_GET['search'])){
	//$sql = "SELECT * FROM imagetb WHERE title = :search OR subject = :search OR tag1 = :search OR tag2 = :search OR tag3 = :search ORDER BY date DESC ";	
	$sql = "SELECT * FROM imagetb WHERE title like :search OR subject like :search OR tag1 like :search OR tag2 like :search OR tag3 like :search ORDER BY date DESC ";
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(":search","%".$_GET['search']."%");
	$stmt->execute();
	$row_check = $stmt->rowCount();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$usercode = explode("/",$row['notepath']);


		$sql = "SELECT username,usercode FROM users WHERE usercode = :usercode";
		$stmt2 = $dbh->prepare($sql);
		$stmt2->bindValue(":usercode",$usercode[2]);
		$stmt2->execute();
		$result = $stmt2->fetch(PDO::FETCH_ASSOC);
		$row_check = $stmt2->rowCount();

		$first_picture = glob($row['notepath']."/0.*");

		$file_arr[] = $first_picture[0];
		$dir_arr[] = $row['notepath'];
		$title_arr[] = $row['title'];
		$user_arr[] = $result['username'];
		$id_arr[] = $result['usercode'];
		$views_arr[] = $row['views'];


		$stmt2 = NULL;
		$result = NULL;
	}
}else{
	header('Location:index.php');
  exit();
}

unset($dbh);
?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
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
		<link rel="stylesheet" type="text/css" href="/css/headerstyle.css">
		<link rel="stylesheet" type="text/css" href="/css/indexstyle.css">
		<link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@500;700;800;900&display=swap" rel="stylesheet">
	</head>
	<body>
		<!-- header.phpが読み込まれる -->
		<div id="header"></div>

		<main>
			<div class="caption">
				<?php if($row_check !== 0 || $_GET['search'] !== "")echo "<h2>".$_GET['search']." の検索結果</h2>"; ?>
			</div>
			<!-- ノート表示欄 -->
			<article class="">
				<ul class="notebookarea">
				<?php
					if($row_check === 0){
						echo "ノートが見つかりませんでした。<br>";
						echo "<a href='index.php'>ホーム画面へ</a>";
						exit(1);
					}	
					for($i = 0;$i < count($file_arr);$i++){
						echo "<li class='notebook'>";
						echo "<a href='view.php?f=".$dir_arr[$i]."&number=".$id_arr[$i]."'>";
						echo "<div class='notebookbox' id='".$i."'>";
						echo "<script>";
						echo "$('#".$i."').css('background-image','url(".$file_arr[$i].")');";
						echo "$('#".$i."').css('background-size','cover');";
						echo "</script>";
						echo "<div class='eye'>";
						echo "<img src='images/eye.svg' class='eyeicon' alt='eyeicon'>";
						echo "<p>".$views_arr[$i]."</p>";
						echo "</div>";
						echo "</div></a>";
						echo "<div class='notebooktitle' id='style-".$i."'>";
						echo "<div class='style_placeholder1'>";
						echo $title_arr[$i]." ";
						echo "</div>";
						echo "<div class='style_placeholder2'></div>";
						echo "</div>";
						echo "</li>";
					}
				?>
				</ul>
			</article>
		</main>
	</body>
</html>
