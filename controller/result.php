<?php
require_once('../conf/const.php');
require_once('../model/function.php');

try{
$dbh = get_db_connect();
$err_msg = result_get_errmsg();
$merchandise_data = get_drink_master($dbh);
$order_drink = result_get_order_drink($dbh);
$correct_msg = result_get_charge($order_drink);
result_update_drink_stock($dbh);
} catch(Exception $e) {
    $err_msg[] = $e->getMessage();
}
include_once('../view/result.php');
?>