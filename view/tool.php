<?php ini_set('display_errors', 0);?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset = "UTF-8">
        <title>自販機管理ページ</title>
        <style>
        table {
        width: 660px;
        border-collapse: collapse;
        }
        table, tr, th, td {
        border: solid 1px;
        padding: 10px;
        text-align: center;
        }
        </style>
    </head>
    <body>
        <h1>自動販売機管理ツール</h1>
<?php foreach ($err_msg as $value) { ?>
    <p><?php print $value; ?></p>
<?php } ?>
<?php foreach ($correct_msg as $value) { ?>
    <p><?php print $value; ?></p>
<?php } ?>
        <hr size = 1 color = #000000 noshade>
        <h2>新規商品追加</h2>
        <form method = "POST" name = "merchandise_base_data" action = "../controller/tool.php" enctype="multipart/form-data">
            <p>商品名：<input type="text" name="name" value ="" size = 30></p>
            <p>価格：<input type="text" name="price" value ="" size = 30></p>
            <p>個数：<input type="text" name="item_number" value ="" size = 30></p>
            <input type= "hidden" name="MAX_FILE_SIZE" value= "2097152">
            <p><input type="file" name="new_file"></p>
            <p><select name ="release" size="1">
                <option value="0">非公開</option>
                <option value="1">公開</option>
                <!--<option value="-2">test</option>-->
            </select> </p>
            <p><input type="submit" name='addsub' value ="商品を追加"></p>
        </form>
        <hr size = 1 color = #000000 noshade>
        <h2>商品情報変更</h2>
        <p>商品一覧</p>
        <table border="1">
            <tr>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫数</th>
                <th>ステータス</th>
            </tr>
            <?php foreach($merchandise_data as $value) {?>
            <tr>
                <td><img src="<?php print $img_dir . $value['img']; ?>" width="150" heifht="150"></td>
                <td><?php print htmlspecialchars($value["drink_name"],ENT_QUOTES,'UTF-8')?></td>
                <td><?php print htmlspecialchars($value["price"],ENT_QUOTES,'UTF-8')?></td>
                <td>
                    <form method = "POST"  action = "../controller/tool.php" enctype="multipart/form-data">
                    <input type = "text" name = "item_number_sec" value = <?php print htmlspecialchars($value["stock"],ENT_QUOTES,'UTF-8'); ?> size = 5>個
                    <input type = "hidden" name = "drink_id" value ="<?php print htmlspecialchars($value["drink_id"],ENT_QUOTES,'UTF-8'); ?>">
                    <input type = "submit" name='stock_change' value = "変更">
                    </form>
                </td>
                <td>
                    <form method = "POST"  action = "../controller/tool.php">
                        <?php
                        if($value['status'] === '0'){?>
                        <input type = "submit" name = "info_release" value = "非公開→公開">
                        <input type = "hidden" name = "drink_id" value ="<?php print htmlspecialchars($value["drink_id"],ENT_QUOTES,'UTF-8'); ?>">
                        <input type = "hidden" name = "release_status" value ="<?php print htmlspecialchars($value["status"],ENT_QUOTES,'UTF-8'); ?>">
                        <?php } else if($value['status'] === '1'){ ?>
                        <input type = "submit" name = "info_release" value = "公開→非公開">
                        <input type = "hidden" name = "drink_id" value ="<?php print htmlspecialchars($value["drink_id"],ENT_QUOTES,'UTF-8'); ?>">
                        <input type = "hidden" name = "release_status" value ="<?php print htmlspecialchars($value["status"],ENT_QUOTES,'UTF-8'); ?>">
                        <?php } else { ?>
                        <input type = "hidden" name = "drink_id" value ="<?php print htmlspecialchars($value["drink_id"],ENT_QUOTES,'UTF-8'); ?>">
                        <input type = "hidden" name = "release_status" value ="2">
                        <?php } ?>
                    </form>
                </td>
            </tr><?php } ?>
        </table>
    </body>
</html>