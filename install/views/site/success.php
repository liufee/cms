<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-19 13:11
 */

$this->title = Yii::t('install', 'Congratuations! Success installed');
?>
<section class="section">
    <div style="padding: 40px 20px;">
        <div class="text-center">
            <a style="font-size: 18px;"><?= Yii::t('install', 'Congratuations! Success installed') ?></a>
            <br>
            <br>
            <div class="alert alert-danger" style="width: 350px;display: inline-block;">
                <?= Yii::t('install', 'For your site security, please remove the directory install! and, backup common/config/conf/db.php') ?>
            </div>
            <br>
            <a target="_blank" class="btn btn-success"
               href="<?= Yii::$app->getRequest()->hostInfo ?>"><?= Yii::t('install', 'go Frontend') ?></a>
            <a target="_blank" class="btn btn-success"
               href="<?= Yii::$app->getRequest()->hostInfo ?>/admin/"><?= Yii::t('install', 'go Backend') ?></a>
        </div>
    </div>
</section>
