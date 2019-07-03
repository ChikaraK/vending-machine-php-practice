<?php ini_set('display_errors', 0);?>
<!DOCTYPE html>
<html lang ="ja">
    <head>
        <meta charset="UTF-8">
        <title>自動販売機</title>
    </head>
    <style>
        #flex {
            width: 600px;
        }

        #flex .drink {
            //border: solid 1px;
            width: 120px;
            height: 210px;
            text-align: center;
            margin: 10px;
            float:left;
        }

        #flex span {
            display: block;
            margin: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #submit {
            clear: both;
        }
    </style>
    <body>
        <h1>自動販売機</h1>
        <form method="POST" action="../controller/result.php">
        支払額<input type="text" name="payment" value="" size=30>
        <hr size = 1 color = #000000 noshade>
        <div id="flex">
       <?php foreach($merchandise_data as $key => $value) {
               if($value['status'] === '1' ){?>
                    <div class = "drink">
                    <span><img src="<?php print $img_dir . $value['img']; ?>" width="130" height="130"></span>
                    <span><?php print htmlspecialchars($value['drink_name'],ENT_QUOTES,'UTF-8')?></span>
                    <span><?php print htmlspecialchars($value['price'],ENT_QUOTES,'UTF-8')?>円</span>
        <?php   if($value['stock'] > 0 ){ ?>
                        <input type="radio" name="choose_drink" value ="<?php print htmlspecialchars($value["drink_id"],ENT_QUOTES,'UTF-8'); ?>">
                        </div>
                <?php   } else {  ?>
                    <?php print '売り切れ'; ?>
                        </div>
                <?php   } ?>
        <?php } ?>
    <?php } ?> 
        </div>
        <div id="submit">
        <input type="submit" name="buy_and_sell" value="購入">
        </div>
        </form>
    </body>
</html>