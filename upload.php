<?php
	session_start();
	session_regenerate_id(true); //セッションハイジャック対策

  //セッションの確認
  if(isset($_SESSION['id']) && isset($_SESSION['name'])){
    $usercode = $_SESSION['id'];
  }else{
    header('Location:logout.php');
    exit();
  }
?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ノートを投稿</title>
    <meta name="description" content="ノートコミュニケーションサービス"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="stylesheet" type="text/css" href="css/uploadstyle.css">
		<link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@500;700;800;900&display=swap" rel="stylesheet">
		<!-- <link href="https://fonts.googleapis.com/css2?family=M+PLUS+1p:wght@500;700;800;900&display=swap" rel="stylesheet"> -->
  </head>
  <body>

    <header>
			<a href="index.php"><img src = "images/notesscavenger_logo.svg" class="title"></a>
    </header>

    <main>
      <form action="toukou.php" method="post" name="toukou" enctype="multipart/form-data">

        <p class="caption">投稿ノート</p>
        <div class="uploadarea">
					<div class="upload">
						<img src="images/notebook2_icon.svg" class="uploadicon">
						<p class="textstyle">
							ドラッグ&amp;ドロップ<br/>
							またはクリックしてファイルをアップロード
						</p>
					</div>
					<input type="file" multiple="multiple"  name="file[]" accept="image/*" required id="example" class="upbutton">
					<div id="preview"></div>
        </div>

        <div class="titleinputarea">
          <p class="caption">タイトル (20文字以内)</p>
          <input type="text" name="title" maxlength=20  required placeholder="例:2次方程式" class="inputtitle">
        </div>

        <div class="subjectselectarea">
          <p class="caption">科目</p>
          <select name="subject" class="subject">
            <option value="語学">語学系</option>
            <option value="数学">数学系</option>
            <option value="科学">科学系</option>
            <option value="社会">社会学系</option>
            <option value="英語">英語系</option>
            <option value="その他">その他</option>
          </select>
					<p class="triangle">▼</p>
        </div>

        <div class="taginputarea">
          <p class="caption">キーワード（最大3つまで）</p>
					<div class="taginput">
						<input type="text" name="tag" maxlength=10 value="" placeholder="例:解の公式" class="inputmargin">
            <input type="text" name="tag2" maxlength=10 value="" placeholder="例:平方完成" class="inputmargin">
            <input type="text" name="tag3" maxlength=10 value="" placeholder="例:因数分解">
					</div>

        </div>

        <div class="commentarea">
          <p class="caption">コメント（任意）</p>
					<textarea class="comments" maxlength=256 name="comment"  placeholder="こんにちは！今回は2次方程式についてまとめました！" ></textarea>
        </div>

        <div class="submitarea">
          <button type="submit" name="submit" class="uploadbutton">投稿</button>
          <button type="button" onclick="simplereset()" class="resetbutton">すべてリセット</button>
          <a href="index.php" class="backbutton">ホーム画面にもどる</a>
        </div>
      </form>
    </main>


    <script>

      function previewFile(file){
        const preview = document.getElementById('preview');
        const reader = new FileReader();

        preview.innerHTML=""; //画像プレビューをクリアする
        reader.onload = function(e){
          const imageUrl = event.target.result; //URLはevent.target.resultで呼び出せる
          const img = document.createElement("img"); //img要素を作成
          //img.width = 100; //画像サイズ(横)
          //img.height = 100; //画像サイズ(縦)

					img.onload = function(){
						img.width = 100;
						//img.height = img.height*(100 / oldwidth);
					}

          img.src = imageUrl; //URLをimg要素にセット
          preview.appendChild(img); //#previewの中に追加
        }
        reader.readAsDataURL(file);
      }

      function simplereset(){
        const preview = document.getElementById('preview');
        preview.innerHTML=""; //previewタグ内をクリアする
        document.toukou.reset(); //FORM内のすべての情報をクリアする
      }

      const fileInput = document.getElementById('example');
      const handleFileSelect = () => {
        const files = fileInput.files;
        const sizeLimit = 1024 * 1024 * 2; //ファイルサイズ指定(Byte単位)
        const preview = document.getElementById('preview');

        var file_max = 0; //選択された画像の合計Byte数
        var file_length_max = 10; //画像の枚数制限
        var allow_exts = new Array('image/jpg','image/jpeg','image/png');
        
        if(files.length > file_length_max){
          alert("画像は10枚までです。");
          fileInput.value = "";
          preview.innerHTML = "";
          return;
        }
        
        for(let i = 0;i < files.length;i++){
          var ext = files[i].type;
          if(allow_exts.indexOf(ext) == -1){
            alert('ファイルはjpg,png,jpegのみです');
            fileInput.value = "";
            preview.innerHTML="";
            return;
          }

          file_max = parseInt(file_max) + parseInt(files[i].size); //画像サイズの計算
          if(file_max >= sizeLimit){ //ファイルサイズが限界を越えた場合の処理
            alert('ファイルサイズは2MB以下にしてください'); //エラーメッセージ表示
            fileInput.value = ""; //inputの中身をリセット
            preview.innerHTML=""; //画像プレビューをクリアする
            return;
          }
        }

        for(let i = 0;i < files.length;i++){
          previewFile(files[i]);
        }
      }
      fileInput.addEventListener('change',handleFileSelect);
    </script>
  </body>
</html>
