<?php
require_once('../conf/const.php');
require_once('../model/function.php');
try{
$dbh = get_db_connect();
$merchandise_data = get_drink_master($dbh);
}catch (Exception $e){
    $err_msg[] = $e->getMessage();
}
include_once('../view/index.php')
?>