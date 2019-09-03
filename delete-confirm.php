<?php

//ここでやること
//ログイン情報があるか確認
//もう一度パスワード認証を行う
    //正しければ削除確認ダイアログを表示
        //はいならDB接続して該当レコードをdelete

session_start();

//セッションにログイン情報がなければログインページへ飛ばす
if(   empty($_SESSION['login']['user_name'])
  ||  empty($_SESSION['login']['profile_image'])
  ||  empty($_SESSION['login']['email'])
  ||  $_SESSION['login']['pswd_verify'] !== true  )
{
  header('Location: /member/login.php');
  exit;
} 

if( !empty($_POST['user_password']) ){
    

    //パスワードがPOSTされたら
    require_once('connect.php');
    try {

        //セッションのログイン情報よりニックネームで検索してパスワードを取り出す
        $sql= "SELECT user_password FROM member WHERE user_name = ?";
        $stmh = $dbh->prepare($sql);
        $stmh->bindValue(1, $_SESSION['login']['user_name'], PDO::PARAM_STR );
        $stmh->execute();
        $rec = $stmh->fetchAll();
        var_dump(password_verify($_POST['user_password'], $rec[0]['user_password']));
        
        //パスワードを照合
        if( password_verify($_POST['user_password'], $rec[0]['user_password']) ){
            
            //正しい
            $_SESSION['delete']['pswd_verify'] = true;
            echo "<script>
                    if( confirm('削除するともとに戻せません。本当によろしいですか?') ){
                            location.href='delete.php';
                    } else {
                            location.href='new-member.php'; 
                    }
                    </script>";
                    //new-member.phpで$_SESSION['delete']['pswd_verify']は削除
        
        } else {        
    
            //正しくない
            $body = "<p>パスワードが間違っています</p>";
        }
                

                
    } catch (PDOException $Exception) {
        echo "エラー：" . $Exception->getMessage();
    }
        
} 

?>





<?php
    $title = 'パスワードの確認';        //ページタイトル
    $headerTitle = 'パスワードの確認';  //ヘッダータイトル
    include_once('header.php');
?>

<main>
<div class="container">
<div class="main-inner">
<form id="main-form" action="" method="post">
    <div class="form-parts">
    <?= @$body ?>
    <label for="user_id">パスワード</label>
    <input id="user_password" type="password" name="user_password" required>
    </div>
    <input type="submit" value="送信">
</form>    
</div>
</div>
</main>

</body>
</html>




