<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/1412:09
 */
use feehi\grid\GridView;
use yii\helpers\Url;
use common\models\Category;
use feehi\libs\Constants;
use yii\helpers\Html;

?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h3><?= $model->title ?></h3>
            </div>
            <div class="ibox-content">
                <?= $model->content ?>
            </div>
</div>