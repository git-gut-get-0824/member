<?php
session_start();
// var_dump($_SESSION);

// セッションにログイン情報がなければログインページへ飛ばす
if(   empty($_SESSION['login']['user_name'])
  ||  empty($_SESSION['login']['profile_image'])
  ||  empty($_SESSION['login']['email'])
  ||  $_SESSION['login']['pswd_verify'] !== true  )
{
  header('Location: /member/login.php');
  exit;
}
 
?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TOP｜会員ページ</title>
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
  <div class="container">
    <div class="header-inner flex-space-between">
            <div class="user-info-wrapper">
              <div class="img-wrapper">
                <img class="profile" src="./profile_image/<?= $_SESSION['login']['profile_image'] ?>" alt="プロフィール画像">
              </div>
              <p class="username"><?= $_SESSION['login']['user_name'] ?></p>
            </div>
            <div class="info-wrapper">
                <a class="logout button-like" href="logout.php">ログアウト</a>
            </div>
</header>

<main>
  <div class="container">
    <div class="main-inner">
      <ul>
        <li>準備中</li>
        <li>under construction</a></li>
      </ul>
    </div>
  </div>
</main>   

<script>
  $(function(){

  //ログアウト前に確認ダイアログを表示
  $('.logout').click(function(){
    var username = $('.username').text();
    if( confirm(username + 'さん ログアウトしてもよろしいですか?') ){
        location.href="./logout.php";
    } else {
        return false;
    }
  });

})
</script>

</body>
</html>