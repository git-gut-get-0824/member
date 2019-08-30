
<?php

header("Content-type: text/plain; charset=UTF-8");

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])
   && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

    $file_dir = "/home/pfbygs/go-at.net/public_html/member/profile_image/";
    $file_path = $file_dir . $_FILES["profile_image"]["name"];

    //画像ファイルかどうかを確認する
    $type = mime_content_type($_FILES["profile_image"]["tmp_name"]);
    if( !preg_match('/jpeg|gif|png/', $type) ){
       
        //正規の画像ファイルでない => 123を返す
        echo 123;

    } else {
        
        //正規の画像ファイルなら
        $lastDotPoint =  strrpos($_FILES["profile_image"]["name"], '.'); //拡張子直前の"."の位置を取得
        list($fileName, $kakuchoshi) = str_split($_FILES["profile_image"]["name"], $lastDotPoint); //"."の前後で分割
    
        require_once('connect.php');
        $sql= "SELECT user_id FROM member ORDER BY user_id DESC LIMIT 1"; //固定文なのでprepareせずともよい
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $rec = $stmt->fetch();
        $nextID = $rec['user_id'] + 1; //最後のidに1を足したもの==次に挿入される(今まさに登録されようとしている)id

        $newFileName = $fileName . '-' . $nextID . $kakuchoshi; //もとのファイル名に"-$nextID"を付加する
        $newFilePath = $file_dir . $newFileName;

        if(move_uploaded_file($_FILES["profile_image"]["tmp_name"], $newFilePath)){
            echo $newFileName;
        }
    } 
} // end if 1

?>