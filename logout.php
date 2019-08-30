<?php
session_start();

//ここでやること
    //セッションに保存したログイン情報を破棄
    //クッキーも削除 -> 過去の日付で同じクッキーを発行
    //TOPページにリダイレクト

    unset($_SESSION['login']);
    $_SESSION['login'] = array();
    
    setcookie('login[0]', "",  time()-90, "/");
    setcookie('login[1]', "", time()-90, "/");
    setcookie('login[2]', "", time()-90, "/");   
    
    $referer = $_SERVER['HTTP_REFERER'];
    $url_arr = parse_url($referer);
    $parse = parse_url($_SERVER["HTTP_REFERER"]);   

?>



<meta http-equiv="refresh" content="3; URL=<?php 
  echo $parse["scheme"],'://',$parse["host"],"/member/";  ?>">

<?php
    $title = 'ログアウト';        //ページタイトル
    $headerTitle = 'ログアウト';  //ヘッダータイトル
    include_once('header.php');
?>

<main>
  <div class="container">
    <div class="main-inner">
      <p>ログアウトしました。TOPページに遷移します...</p>
    </div>
  </div>
</main>

</body>
</html>