<?php

//GETパラメータに値がないとき
if( empty($_GET['k']) ){ 
    echo "正しい処理が行われませんでした。";
    echo '<meta http-equiv="refresh" content="3; URL=https://go-at.net/signup.php">';
    exit;
} 

//GETパラメータに値があるとき
session_start();
$token = sha1(uniqid(mt_rand(), true)); //トークン生成
$_SESSION['token'] = $token;            //セッション変数に代入

require_once('connect.php');
try {
    //GETパラメータでsignupテーブルを検索
    $sql= "SELECT email FROM signup WHERE token = ?";
    $stmh = $dbh->prepare($sql);
    $stmh->bindValue(1, $_GET['k'], PDO::PARAM_STR );
    $stmh->execute();
    $rec = $stmh->fetchAll();

    if( empty($rec[0]) ){
        // SELECTの結果1件も出てこない -> GETパラメータが正しくないのでsignupページに飛ばす
        echo "正しい処理が行われませんでした。";
        echo '<meta http-equiv="refresh" content="3; URL=https://go-at.net/signup.php">';
        exit;

    } else {
       //メールアドレスは変数に格納  name="email"のvalue値に埋め込む
        $email = $rec[0]['email'];
    } 

} catch (PDOException $Exception) {
    print "エラー：" . $Exception->getMessage();
}

?>





<?php
    $title = '会員登録';        //ページタイトル
    $headerTitle = '会員登録';  //ヘッダータイトル
    include_once('header.php');
?>   

<main>
<div class="container">
<div class="main-inner">
<form id="main-form" action="confirm.php" method="post" onsubmit="return formValidation();">
<!-- onsubmit時, formValidation関数を呼び出して,戻り値をreturnする
        trueなら送信, falseなら送信できない 
    -->
<div class="form-parts">
    <label for="user_name" class="hissu">ニックネーム</label>
    <input id="user_name" type="text" name="user_name" placeholder="taro12(5文字以上)" required>
    <div class="alert"></div>
    <div class="alert-chofuku"></div>
    <div class="sub-info">※5文字以上(サービス上で使用する名前です)</div>
</div>

<div class="form-parts">
    <label>プロフィール画像</label>
        <div class="button-like modal-open">画像を追加する</div>
        <div id="_profileAvatarImage" class="avatarContainer">
                <span class="sub-info">※閲覧数、投資コンタクト率を上げるためにお好きな画像をアップしてください。</span>
                <div class="alert-upload"></div>
                <!--ここに画像が映る-->
                    <img style="width:100px">
                    <input type="hidden" name="profile_image" id="profileImage" value="" required="">
        </div>
</div>

<div class="form-parts">
    <label for="seimei" class="hissu">お名前</label>
    <input id="seimei" type="text" name="seimei" required>
    <div class="alert"></div>
</div>

<div class="form-parts">
    <label for="phone" class="hissu">電話番号</label>
    <input id="phone"" type="text" name="phone" placeholder="09012345678" required>
    <div class="alert"></div>
    <div class="sub-info">※ハイフンは不要です</div>
</div>

<div class="form-parts">
    <label for="email" class="hissu">メールアドレス</label>
    <input id="email" type="text" name="email" value="<?= $email ?>" required>
    <div class="alert"></div>
    <div class="alert-chofuku-email"></div>
</div>

<div class="form-parts">
    <label for="user_password" class="hissu">パスワード</label>
    <input id="user_password" type="password" name="user_password" required>
    <div class="alert"></div>
    <div class="sub-info">※英字&数字混合で8~25文字</div>
</div>

<div class="form-parts">
    <label for="user_password_confirm" class="hissu">パスワード確認</label>
    <input id="user_password_confirm" type="password" name="user_password_confirm" required>
    <div class="alert"></div>
</div>

<input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
<input type="submit" value="確認">
</form>



<!-- モーダル 画像登録フォーム -->
<div class="modal">
    <div class="mordal-inner">
        <form id="upload-form"> 
        <input id="profile_image" type="file" name="profile_image">
        <br>
        <button type="button" id="fileSubmit" onclick="file_upload()">アップロード</button>
        <input type="button" id="cancelSubmit" value="キャンセル">
        <p>(最大5MBまで。JPG,GIF,PNGが使えます)</p>
        </form>
    </div>
</div>



</div>
</div>
</main>





<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script src="script/form-validation.js"></script>
<script src="script-ajax.js"></script>
<script>

// 画像アップロードフォーム(モーダルウインドウ)
    
    //画像を追加ボタンクリックで表示
    $('.modal-open').click(function(){
        $('.modal').fadeIn(300);
    });
    
    //アップロードorキャンセルボタンクリックで閉じる
    $('#fileSubmit, #cancelSubmit').click(function(){
        $('.modal').fadeOut(300);
    });


// Ajax 画像アップロード
function file_upload(){
    // フォームデータを取得
    var formdata = new FormData($('#upload-form').get(0));

    // POSTでアップロード
    $.ajax({
        url  : "ajax-img-upload.php",
        type : "POST",
        data : formdata,
        cache       : false,
        contentType : false,
        processData : false,
        dataType    : "html"
    })
    .done(function(data, textStatus, jqXHR){
        //画像ファイルではない
        if( data == 123 ){
            $('.alert-upload').html(icon + '&nbsp;' + '画像でないファイルはアップロードできません');
            $('.alert-upload').show(300);
        }
        else {

            var dir = '/member/profile_image/'; //画像ファイルの保存ディレクトリ
            $('.alert-upload').html("");
            $('.alert-upload').hide(300);
            $('#_profileAvatarImage').show(300).children('span').html('');
            $('#_profileAvatarImage img').attr('src',dir + data);
            $('[name=profile_image]').val(data).attr({type:'text' ,readonly:'readonly'});
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown){
        alert("fail");
    });
}
</script>



</body>
</html>