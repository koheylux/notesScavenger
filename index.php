<?php
//0でエラー非表示、1でエラー表示
ini_set( 'display_errors', 1 );

require("dbinfo.php");

if(!isset($_SESSION)){
  session_start();
}
session_regenerate_id(true); //セッションハイジャック対策

if(isset($_SESSION['id']) && isset($_SESSION['name'])){
	
}else{
	header('Location:login.php');
	exit();
}

// 画像、ディレクトリ、タイトル、作成したユーザ名、閲覧数を配列に格納
$file_arr = array();
$dir_arr = array();
$title_arr = array();
$user_arr = array();
$id_arr = array();
$views_arr = array();

//科目用連想配列
$word = array(
	'japanese'=> 'SELECT * FROM imagetb WHERE subject = "語学" ORDER BY date DESC',
	'math'    => 'SELECT * FROM imagetb WHERE subject = "数学" ORDER BY date DESC',
	'science' => 'SELECT * FROM imagetb WHERE subject = "科学" ORDER BY date DESC',
	'society' => 'SELECT * FROM imagetb WHERE subject = "社会" ORDER BY date DESC',
	'english' => 'SELECT * FROM imagetb WHERE subject = "英語" ORDER BY date DESC',
	'other'   => 'SELECT * FROM imagetb WHERE subject = "その他" ORDER BY date DESC',
);

//表示用連想配列
$hyouzi = array(
	'japanese'=> '語学のノート一覧',
	'math'	  => '数学のノート一覧',
	'science' => '科学のノート一覧',
	'society' => '社会のノート一覧',
	'english' => '英語のノート一覧',
	'other'   => 'その他のノート一覧',
);


//新着10件(auto_increment)の高い順に10件取得
if(isset($_GET['page'])){
	if($_GET['page'] === "rank") $sql = "SELECT * FROM imagetb ORDER BY views DESC";
	else if($_GET['page'] === "many") $sql = "SELECT * FROM imagetb ORDER BY date DESC";
	else $sql = $word[$_GET['page']];
}else{
	$sql = "SELECT * FROM imagetb ORDER BY imagecode DESC limit 15";
}

$stmt = $dbh->query($sql);

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

			$(window).bind("unload",function(){});
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
				<?php
					if(isset($_GET['page'])){
						if($_GET['page'] === 'rank')echo "<h2>ランキング</h2>";
						else if($_GET['page'] === 'many')echo "<h2>すべてのノート</h2>";
						else echo "<h2>".$hyouzi[$_GET['page']]."</h2>";
					}else {
						echo "<h2>新着ノート</h2>";
						echo "<a href='index.php?page=many' class='textstyle1'>もっと見る</a>";
				}
				?>
			</div>

			<!-- ノート表示欄 -->
			<article class="">
				<ul class="notebookarea">
				<?php
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
