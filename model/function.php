<?php
//汎用
function get_db_connect(){
    try{
    $dbhl = new PDO(DSN,USERNAME,PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => DB_CHARSET));
    $dbhl->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbhl->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
     die ('接続できませんでした。理由：'.$e->getMessage());
    }
    return $dbhl;
} 
function get_as_array($dbh,$sql){
    try{
        $stmt = $dbh->prepare($sql);
        $stmt -> execute();
        $rows = $stmt->fetchall();
    }catch (PDOException $e){
        throw $e;
    }
    return $rows;
}
function get_drink_master($dbh){
    $sql = 'SELECT * FROM drink_master as m INNER JOIN drink_stock as s WHERE m.drink_id = s.drink_id';
    return get_as_array($dbh,$sql);
}
//tool関係
function tool_get_errmsg(){
    global $err_msg,$name,$price,$new_img_filename,$release,$item_number;
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addsub']) === TRUE){
        if(isset($_POST['name']) === TRUE){
            $name = $_POST['name'];
        }
        if(isset($_POST['price']) === TRUE){
            $price = $_POST['price'];
        }
        if(isset($_POST['item_number']) === TRUE){
            $item_number = $_POST['item_number'];
        }
        if(isset($_POST['release']) === TRUE){
            $release = $_POST['release'];
        }
        if(isset($_POST['drink_id']) === TRUE){
            $drink_id = $_POST['drink_id'];
        }
        if($name === ''){
            $err_msg[] = '名前が入力されていません。';
        }else if(strlen($name) > 60){
            $err_msg[] = '名前は20文字までです。';
        }else if(ctype_space($name) === TRUE || $name === '　'){
            $err_msg[] = '空白のみの名前は利用できません。';
        }
        if($price === ''){
            $err_msg[] = '価格が入力されていません。';
        }else if(preg_match('/[^0-9]/',$price)){
            $err_msg[] = '価格は半角数字、整数かつ0以上で設定してください。';
        }else if(strlen($price) > 11){
            $err_msg[] = '価格は11桁までです。';
        }else if(ctype_space($price) === TRUE){
            $err_msg[] = '空白のみの価格は入力できません。';
        }
        if($item_number === ''){
            $err_msg[] = '個数が入力されていません。';
        }else if(preg_match('/[^0-9]/',$item_number)){
            $err_msg[] = '個数は半角数字、整数かつ0以上で設定してください。';
        }else if(strlen($item_number) > 11 ){
            $err_msg[] = '個数は11桁までです。';
        }else if(ctype_space($item_number) === TRUE){
            $err_msg[] = '空白のみの個数は入力できません。';
        }else if($item_number < 0){
            $err_msg[] = '個数は0以上を入力してください。';
        }
        if($release > '2'|| $release < '0'){
            $err_msg[] = '不正な入力値です。';
        }
    } 
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stock_change']) === TRUE){
        if(isset($_POST['item_number_sec']) === TRUE){
            $item_number_sec = $_POST['item_number_sec'];
        }
        if(strlen($item_number_sec) > 11 ){
            $err_msg[] = '在庫数の変更は10桁までです。';
        }else if(ctype_space($item_number_sec) === TRUE){
            $err_msg[] = '在庫数変更に空白のみの入力できません。';
        }else if(preg_match('/[^0-9]/',$item_number_sec)){
            $err_msg[] = '在庫数は半角数字、整数かつ0以上で設定してください。';
        }else if($item_number_sec === ''){
            $err_msg[] = '在庫変更数が入力されていません。';
        }else if($item_number_sec < 0){
            $err_msg[] = '在庫は0以上を入力してください。';
        }
    }
    // var_dump($name,$price,$new_img_filename,$release,$item_number);
}
function tool_get_img(){
    global $err_msg;
    global $img_dir;
    global $new_img_filename;
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addsub']) === TRUE){
        //ファイルサイズチェック
        if(is_uploaded_file($_FILES['new_file']['tmp_name']) === TRUE){
            $extension = pathinfo($_FILES['new_file']['name'],PATHINFO_EXTENSION);
            //ファイル拡張子チェック
            if($extension === 'jpg' || $extension === 'JPG' || $extension === 'JPEG'|| $extension === 'jpeg'|| $extension === 'png' || $extension === 'PNG'){
                    $new_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;
            } else {
              $err_msg[] = 'ファイル形式が異なります。画像ファイルはjpg/jpegあるいはpngのみ利用可能です。';
            }
            //ファイルMIME対タイプチェック
            if (!$ext = array_search(
            mime_content_type($_FILES['new_file']['tmp_name']),
            array(
                // 'gif' => 'image/gif',
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
            ),
            true
            )) {
                $err_msg[] = 'ファイル形式が不正です';
            }
            if(count($err_msg) === 0){
                    if (is_file($img_dir . $new_img_filename) !== TRUE) {
                        if (move_uploaded_file($_FILES['new_file']['tmp_name'], $img_dir . $new_img_filename) !== TRUE) {
                            $err_msg[] = 'ファイルアップロードに失敗しました';
                        }
                    } else {
                            $err_msg[] = 'ファイルアップロードに失敗しました。再度お試しください。';
                    }   
            }
        }else if($_FILES['new_file']['error'] === 2 ||$_FILES['new_file']['error'] === 1) {
            $err_msg[] = 'ファイルサイズが大きすぎます。2MB以下にしてください。';
        } else {
            $err_msg[] = 'ファイルを選択してください';
        }
    }
}
function tool_insert_drink_master($dbh){
    global $err_msg, $drink_id,$name,$price,$new_img_filename,$release,$item_number,$correct_msg;
    if($_SERVER['REQUEST_METHOD'] === 'POST' && count($err_msg) === 0 && isset($_POST['addsub']) === TRUE){
        $dbh->beginTransaction();
        try{
            $sql = 'INSERT INTO drink_master(drink_id,drink_name,price,img,status,create_datetime,update_datetime)
                    VALUE(?,?,?,?,?,now(),now());';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1,$drink_id , PDO::PARAM_STR);
            $stmt->bindValue(2,$name , PDO::PARAM_STR);
            $stmt->bindValue(3,$price , PDO::PARAM_STR);
            $stmt->bindValue(4,$new_img_filename , PDO::PARAM_STR);
            $stmt->bindValue(5,$release , PDO::PARAM_STR);
            $stmt ->execute();
        }catch(PDOException $e){
            $err_msg[] = '接続できませんでした。';
        }
        if(count($err_msg) === 0){
            try{
                $sql = 'INSERT INTO drink_stock(drink_id,stock,create_datetime,update_datetime)
                        VALUE(?,?,now(),now())';
                $drink_id = $dbh->lastInsertId();
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1,$drink_id, PDO::PARAM_INT);
                $stmt->bindValue(2,$item_number, PDO::PARAM_INT);
                $stmt ->execute();
            }catch(PDOException $e){
                $err_msg[] = '接続できませんでした。';
            }
        }
        if(count($err_msg) === 0){
            $dbh->commit();
            $correct_msg[] = '登録が正常に完了しました。';
        }else{
            $dbh->rollBack();
        }
    }
}
function tool_update_drink_stock($dbh){
    global $err_msg,$item_number_sec,$correct_msg;
    if(isset($_POST['drink_id']) === TRUE){
            $drink_id = $_POST['drink_id'];
    }
    if($_SERVER['REQUEST_METHOD'] === 'POST' && count($err_msg) === 0 && isset($_POST['stock_change']) === TRUE){
        $sql = 'UPDATE drink_stock SET stock = ?,update_datetime = now() WHERE drink_id = ?;';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$_POST['item_number_sec'], PDO::PARAM_INT);
        $stmt->bindValue(2,$drink_id, PDO::PARAM_STR);
        $stmt ->execute();
        $correct_msg[] = '在庫数変更が正常に完了しました。';
    }
}
function tool_release_switch($dbh){
    global $err_msg,$drink_id,$correct_msg;
    if(isset($_POST['drink_id']) === TRUE){
            $drink_id = $_POST['drink_id'];
    }
    if(isset($_POST['release_status']) === TRUE){
            $release_status = $_POST['release_status'];
        if($_SERVER['REQUEST_METHOD'] === 'POST' && count($err_msg) === 0 && isset($_POST['info_release']) === TRUE){ 
            if($release_status === '0'){
            $sql = 'UPDATE drink_master SET status = ?,update_datetime = now() WHERE drink_id = ?;';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1,'1', PDO::PARAM_STR);
            $stmt->bindValue(2,$drink_id, PDO::PARAM_STR);
            $stmt ->execute();
            $correct_msg[] = 'ステータス変更が正常に完了しました。';
            }else if($release_status === '1'){ 
            $sql = 'UPDATE drink_master SET status = ?,update_datetime = now() WHERE drink_id = ?;';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1,'0', PDO::PARAM_STR);
            $stmt->bindValue(2,$drink_id, PDO::PARAM_STR);
            $stmt ->execute();
            $correct_msg[] = 'ステータス変更が正常に完了しました。';
            }else{
            $err_msg[] = 'ステータスが不正です';
            }
        }
    }
}
//result関係
function result_get_errmsg(){
    global $err_msg,$name,$release,$status,$payment,$stock;
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_and_sell']) === TRUE){
        if(isset($_POST['payment']) === TRUE){
            $payment = $_POST['payment'];
        }
        if($payment === ''){
            $err_msg[] = '代金が入力されていません。';
        }else if(preg_match('/[^0-9]/',$payment)){
            $err_msg[] = '代金は半角数字で設定してください。';
        }else if($payment === '0'){
            $err_msg[] = '代金0は入力できません。';
        }else if(strlen($payment) > 11){
            $err_msg[] = '代金は11桁までです。';
        }else if(ctype_space($payment) === TRUE){
            $err_msg[] = '空白のみの代金は入力できません。';
        }
        if(isset($_POST['choose_drink']) === FALSE){
            $err_msg[] = '商品が選択されていません。';
        }
    }return $err_msg;
}
function result_update_drink_stock($dbh){
    global $err_msg;
    if(isset($_POST['choose_drink']) === TRUE){
        $choose_drink = $_POST['choose_drink'];
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST' && count($err_msg) === 0 && isset($_POST['buy_and_sell']) === TRUE){
        $sql = 'UPDATE drink_stock SET stock = stock - 1, update_datetime = now() WHERE drink_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$choose_drink,PDO::PARAM_INT);
        $stmt->execute();
        }
}
function result_get_order_drink($dbh){
    global $err_msg;
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_and_sell']) === TRUE && count($err_msg) === 0){
        if(isset($_POST['choose_drink']) === TRUE){
        $choose_drink = $_POST['choose_drink'];
        }
    $sql = 'SELECT * FROM drink_master as m INNER JOIN drink_stock as s ON m.drink_id = s.drink_id WHERE m.drink_id = ?;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1,$choose_drink,PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchall();
    return $rows;
    }
} function result_get_charge($array){
    global $err_msg;
    if(isset($_POST['payment']) === TRUE){
            $payment = $_POST['payment'];
    }
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_and_sell']) === TRUE && count($err_msg) === 0){
        $correct_msg = array();
        foreach ($array as $value){
                    if($value['status'] === '1'){
                        if($value['stock'] > '0'){
                            if($payment >= $value['price']){
                                $charge = $payment - $value['price'];
                                $correct_msg[] = $value['drink_name'].'の購入に成功しました。';
                                $correct_msg[] =  'おつりは'.$charge.'円です。';
                            } else {
                                $charge = $value['price'] - $payment;
                                $err_msg[] = '購入には'.$charge.'円足りません。';
                            }
                        }else{
                         $err_msg[] = '現在'.htmlspecialchars($value['drink_name'],ENT_QUOTES,'UTF-8').'は在庫がございません';   
                        }
                    }else{
                        $err_msg[] = '現在'.htmlspecialchars($value['drink_name'],ENT_QUOTES,'UTF-8').'は扱っておりません';
                    }
                } return $correct_msg;
        }
    }
?>