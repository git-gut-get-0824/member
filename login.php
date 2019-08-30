<?php

// login.php 単独ファイルでニックネームとパスワードをpost値で受け取って認証を行う
// セッションの開始
// トークン発行
// post値でユーザー検索
// パスワードを照合
// ニックネームと姓名をログインセッションとして保存
// 同時にクッキーにも保存
// new-member.phpへリダイレクト


// 1. セッションの開始
session_start();

// 2. トークン発行
// 3でセッションの値をチェックするので, それより前に発行してしまうとトークン一致確認が上手くいかない

// 3. post値でユーザー検索
if(   !empty($_POST['token'])                   // トークンが空じゃない
      && $_SESSION['token'] == $_POST['token']  // トークンが一致
      && !empty($_POST['user_name'])            // 名前がちゃんと入っている
      && !empty($_POST['user_password'])        // パスワード      〃
){
         
    //トークン一致 & ユーザー名とパスワードが送信されたらDB接続して認証
    require_once('connect.php');
    try {
        //ニックネームで検索
        $sql= "SELECT * FROM member WHERE user_name = ?";
        $stmh = $dbh->prepare($sql);
        $stmh->bindValue(1, $_POST['user_name'], PDO::PARAM_STR );
        $stmh->execute();
        $rec = $stmh->fetchAll();
       
        if( !isset($rec[0]) ){
            // ニックネームが登録されていない(==SELECTの結果1件も出てこない) => パスワード照合不要
            $body = "<p>入力値に誤りがあります</p>";
        }  
        else {
            // ニックネームが登録されている => パスワード照合
            if ( password_verify($_POST['user_password'], $rec[0]['user_password']) ){      
                
                // パスワードが正しい
                    // セッションにログイン情報を保存
                    $_SESSION['login']['user_name'] = $rec[0]['user_name']; //2次元配列にしておくと後で破棄が楽
                    $_SESSION['login']['profile_image'] = $rec[0]['profile_image'];
                    $_SESSION['login']['email'] = $rec[0]['email'];
                    $_SESSION['login']['pswd_verify'] = true;

                    //クッキーにも保存
                    setcookie('login[0]', $rec[0]['user_name'], time()+60*60*24*10, "/");
                    setcookie('login[1]', $rec[0]['profile_image'], time()+60*60*24*10, "/");
                    setcookie('login[2]', $rec[0]['email'], time()+60*60*24*10, "/");

                    //new-member.phpへリダイレクト
                    header("Location: new-member.php");

            } else {

                //パスワードが間違っている
                $body = "<p>入力値に誤りがあります</p>";
            }
        }

    } catch (PDOException $Exception) {
        echo "エラー：" . $Exception->getMessage();
    }
        
} else {
    //postされる前の処理
    $token = sha1(uniqid(mt_rand(), true)); //トークン生成
    $_SESSION['token'] = $token;            //セッション変数に代入
}

?>




<?php
    $title = 'ログイン';        //ページタイトル
    $headerTitle = 'ログイン';  //ヘッダータイトル
    include_once('header.php');
?>

<main>
<div class="container">
<div class="main-inner">
<?= @$body ?>
<form id="main-form" action="" method="post">
    <div class="form-parts">
    <label for="user_name">ニックネーム</label>
    <input id="user_name" type="text" name="user_name" required>
    </div>
    
    <div class="form-parts">
    <label for="user_id">パスワード</label>
    <input id="user_password" type="password" name="user_password" required>
    </div>

    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
    
    <input type="submit" value="LOGIN">
</form>    
</div>
</div>
</main>


</body>
</html>