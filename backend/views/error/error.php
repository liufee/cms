<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/1415:44
 */
?>
<div class="middle-box text-center animated fadeInDown">
    <h1><?=$code ?></h1>
    <h3 class="font-bold"><?=$name?></h3>

    <div class="error-desc">
        <?=$message ?>~
        <form target="_blank" class="form-inline m-t" action="http://www.baidu.com/s" role="form">
            <div class="form-group">
                <input type="text" name="wd" class="form-control" placeholder="<?=yii::t('app', 'Please Enter the Question')?> …">
            </div>
            <button type="submit" class="btn btn-primary"><?=yii::t('app', 'Search')?></button>
        </form>
    </div>
</div>
