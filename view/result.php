<?php ini_set('display_errors', 0);?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset="utf-8">
        <title>購入結果</title>
    </head>
    <body>
        <h1>購入結果画面</h1>
<?php foreach ($err_msg as $value) { ?>
        <p><?php print $value; ?></p>
<?php } ?>
<?php if(count($err_msg) === 0) {
        foreach ($order_drink as $value){ ?>
        <img src="<?php print $img_dir . $value['img']; ?>" width="130" height="130"></p>
<?php   }
        foreach ($correct_msg as $value){ ?>
        <p><?php print htmlspecialchars($value,ENT_QUOTES,'UTF-8')?></p>
<?php   }
}?>
    <a href ="../controller/index.php">戻る</a>
    </body>
</html>