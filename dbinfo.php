<?php
  //notesScavenger
  $dsn = 'mysql:dbname=main; host=localhost; charset=utf8mb4';
  $sqluser = 'notesscavenger';
  $sqlpass = 'skskyuzawa';

  //データベースに接続
  try{
		$dbh = new PDO($dsn, $sqluser, $sqlpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
  catch (PDOException $e){
    print $e->getMessage();
    exit();
  }


?>
