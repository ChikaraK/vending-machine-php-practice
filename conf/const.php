<?php
$err_msg = array();
$correct_msg = array();
$merchandise_data = array();
$item_number = 0;
$drink_id = 0;
$img_dir = '../img/';
$new_img_filename = '';
define('USERNAME','HOGEHOGE');   // MySQLのユーザ名
define('PASSWORD', 'JXRGWLIM');       // MySQLのパスワード
define('DB_NAME' ,'HOGEHOGE');   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
define('DB_CHARSET','SET NAMES utf8');   // データベースの文字コード
define('DSN', 'mysql:dbname='.DB_NAME.';host=localhost;charset=utf8'); 
define('HTML_CHARACTER_SET', 'UTF-8'); 
?>