<?php

$arr = parse_ini_file('config.ini');

$db_user = $arr['user'];	// ユーザー名
$db_pass = $arr['pass'];	// パスワード
$db_host = $arr['host'];	// ホスト名
$db_name = $arr['dbname'];	// データベース名
$db_type = "mysql";	// データベースの種類

$dsn = "$db_type:host=$db_host;dbname=$db_name;charset=utf8";

try {
    $dbh = new PDO($dsn, $db_user,$db_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);//連想配列を指定
    // print "データベースに接続しました... <br>";
  } catch(PDOException $Exception) {
    exit('エラー :' . $Exception->getMessage());
  }

?>