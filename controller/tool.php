<?php
require_once('../conf/const.php');
require_once('../model/function.php');
try{
$dbh = get_db_connect();
tool_get_img();
tool_get_errmsg();
tool_insert_drink_master($dbh);
tool_update_drink_stock($dbh);
tool_release_switch($dbh);
$merchandise_data = get_drink_master($dbh);
} catch (Exception $e){
    $err_msg[] = $e->getMessage();
}
include_once('../view/tool.php');
?>