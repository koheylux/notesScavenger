<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//セッションの確認とGETの受け取り
if(isset($_SESSION['id']) && isset($_SESSION['name']) && isset($_SESSION['token'])){
  $userName = $_SESSION['name'];
  unset($_SESSION['token']);
}else{
  header('Location:logout.php');
  exit();
}

// $email = $_SESSION['email'];
// if(empty($email)){
// 	echo"不正アクセスの可能性あり";
// 	exit();
// }
//エラーメッセージの初期化
$errors = array();

// if(empty($_SESSION['token'])){
// 	echo"不正アクセスの可能性あり";
// 	exit();
// }else{
//   unset($_SESSION['token']);
// }

// //セッション変数を全て解除
// $_SESSION = array();

// //クッキーの削除
// if (isset($_COOKIE["PHPSESSID"])) {
// setcookie("PHPSESSID", '', time() - 1800, '/');
// }

// //セッションを破棄する
// session_destroy();


?>


<!DOCTYPE html>

<html>
	<head>
	<meta charset="utf-8">
	<title>メール送信完了画面 | notesScavenger</title>
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
		margin-bottom: 40px;
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
		font-size: 14px;
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
	</style>
	<a href="index.php"><img class="logo" src = "images/notesscavenger_logo.svg"></a>

	<div class="titlearea">	
    <h3>パスワード変更完了画面</h3>
	</div>
	<div class="inputarea">
		<?php echo $userName?>
		<br>
		<br>パスワード変更を完了しました。<br>
		
		<input type="button" value="戻る" onclick="location.href='index.php'" class="btn">
	</div>


</body>
</html>
