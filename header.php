<?php
	//0でエラー非表示、1でエラー表示
	ini_set( 'display_errors', 1 );

	require("dbinfo.php");

	if(!isset($_SESSION)){
		session_start();
	}
	session_regenerate_id(true); //セッションハイジャック対策

  //セッションの確認
  if(isset($_SESSION['id']) && isset($_SESSION['name'])){
    $userId = $_SESSION['id'];
		if(isset($_GET["search"])){
			$searchWords = $_GET['search'];
		}else{
			$searchWords = "";
		}
  }else{
		// header('Location:logout.php');
		exit();
	}
?>

<header>
	<!-- スマホ画面用 -->
	<div class="smallsc">
		<div class="topbar">
			<!-- ロゴタイトル -->
			<a class="logoarea" href="index.php"><img src="images/notesscavenger_logo.svg" alt="notesscavenger"></a>

			<div class="iconarea">
				<!-- ノート投稿 -->
				<a href="upload.php" class="upload-btn">
					<img src="images/notebook_icon.svg" class="notebookimg" alt="notebook">
					<span>投稿</span>
				</a>

				<!-- プロフィール画像 -->
				<div class="profileicon">
					<div><img src="images/usericon.png" alt="usericon" width="33" height="33"></div>
					<!-- <img src="images/usericon.png" alt="usericon"> -->
					<div class="profilemenu">
						<ul class="hidden">
							<?php echo "<a href='profile.php?number=".$userId."'>";?><li>プロフィール</li></a>
							<?php echo "<a href='profile.php?number=".$userId."&page=gazou'>";?><li>投稿ノート一覧</li></a>
							<!-- <a href="" class=""><li>メールアドレスの変更</li></a> -->
							<a href="password_setting.php"><li>パスワードの変更</li></a>
							<a href="logout.php" class="logoutbtn"><li>ログアウト</li></a>
						</ul>
					</div>
				</div>

			</div>

		</div>

		<!-- 検索窓 -->
		<div class="searcharea">
			<form action="search.php" method="get">
				<input type="search" name="search" class="search" placeholder="キーワードを入力" value="<?=$searchWords?>">
				<button type="submit"  class="lupeimg">
					<img src="images/lupe_icon.svg" class="bellimg" alt="lupe">
				</button>
			</form>
		</div>

		<nav class="menu">

			<div class="category">
				<div class="menulist">
					<img src="images/category_icon.svg">
					<span>カテゴリー</span>
					<ul class="hidden">
						<a href="index.php"><li class="newbtn">新着ノート</li></a>
						<a href="index.php?page=japanese"><li>語学</li></a>
						<a href="index.php?page=math"><li>数学</li></a>
						<a href="index.php?page=science"><li>科学</li></a>
						<a href="index.php?page=society"><li>社会</li></a>
						<a href="index.php?page=english"><li>英語</li></a>
						<a href="index.php?page=other"><li>その他</li></a>
					</ul>
				</div>
			</div>

			<div class="ranking">
				<div class="menulist">
					<?php echo "<a href='index.php?page=rank'>"; ?>
						<img src="images/lank_icon.svg">
						<span>ランキング</span>
					</a>
				</div>
			</div>

			<div class="lovelist">
				<div class="menulist">
						<?php echo "<a href='profile.php?number=".$userId."&page=good'>"; ?>
						<img src="images/love_icon.svg">
						<span>いいね一覧</span>
					</a>
				</div>
			</div>

		</nav>

	</div>

	<!-- PC画面用 -->
	<div class="bigsc">
		<div class="bigtopbar">
			<!-- ロゴタイトル -->
			<div  class="biglogoarea">
				<a href="index.php"><img src="images/notesscavenger_logo.svg" alt="notesscavenger"></a>
			</div>

			<!-- 検索窓 -->
			<div class="bigsearcharea">
				<form action="search.php" method="get">
					<input type="search" name="search" class="bigsearch" placeholder="キーワードを入力" value="<?=$searchWords?>">
					<button type="submit" class="lupeimg">
						<img src="images/lupe_icon.svg" class="bellimg" alt="lupe">
					</button>
				</form>
			</div>

			<div class="iconarea">
				<!-- ノート投稿 -->
				<a href="upload.php" class="upload-btn">
					<img src="images/notebook_icon.svg" class="notebookimg" alt="notebook">
					<span>投稿</span>
				</a>

				<!-- プロフィール画像 -->
				<div class="profileicon">
					<div><img src="images/usericon.png" alt="usericon" width="33" height="33"></div>
					<div class="profilemenu">
						<ul class="hidden">
							<?php echo "<a href='profile.php?number=".$userId."'>";?><li>プロフィール</li></a>
							<?php echo "<a href='profile.php?number=".$userId."&page=gazou'>";?><li>投稿ノート一覧</li></a>
							<!-- <a href="remail_mail_form.php" class="btnmgn"><li>メールアドレスの変更</li></a> -->
							<a href="password_setting.php"><li>パスワードの変更</li></a>
							<a href="logout.php" class="logoutbtn"><li>ログアウト</li></a>
						</ul>
					</div>
				</div>

			</div>

		</div>

		<nav class="bigmenu">

			<div class="category">
				<div class="menulist">
					<img src="images/category_icon.svg">
					<span>カテゴリー</span>
					<ul class="hidden">
						<a href="index.php"><li class="newbtn">新着ノート</li></a>
						<a href="index.php?page=japanese"><li>語学</li></a>
						<a href="index.php?page=math"><li>数学</li></a>
						<a href="index.php?page=science"><li>科学</li></a>
						<a href="index.php?page=society"><li>社会</li></a>
						<a href="index.php?page=english"><li>英語</li></a>
						<a href="index.php?page=other"><li>その他</li></a>
					</ul>
				</div>
			</div>

			<div class="ranking">
				<div class="menulist">
					<?php echo "<a href='index.php?page=rank'>"; ?>
						<img src="images/lank_icon.svg">
						<span>ランキング</span>
					</a>
				</div>
			</div>

			<div class="lovelist">
				<div class="menulist">
					<?php echo "<a href='profile.php?number=".$userId."&page=good'>"; ?>
						<img src="images/love_icon.svg">
						<span>いいね一覧</span>
					</a>
				</div>
			</div>

		</nav>
	</div>


</header>

<script>
	window.onunload = function() {};

	window.onpageshow = function(event) {
		if (event.persisted) {
			window.location.reload();
		}
	};
	$(document).ready(function(){
		$('.category').hover(function(){
			$('.category ul').toggleClass('hidden');
		}, function(){
			$('.category ul').toggleClass('hidden');
		});
	});

	$(document).ready(function(){
		$('li').hover(function(){
			$(this).css('color','white');
		}, function(){
			$(this).css('color','black');
			$('.logoutbtn li').css('color','red');
			$('.newbtn').css('color','#21538E');
		});
	});

	$(document).ready(function(){
		$('.profileicon').hover(function(){
			$('.profileicon ul').toggleClass('hidden');
		}, function(){
			$('.profileicon ul').toggleClass('hidden');
		});
	});
</script>
