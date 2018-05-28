<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="content-wrap">
    <div style="text-align:center;padding:10px 0;font-size:16px;background-color:#ffffff;height: 460px">
        <h2 style="font-size:36px;margin-bottom:10px;"><?= Html::encode($this->title) ?></h2>
        <p align="center"><?= nl2br(Html::encode($message)) ?></p>
        <div style="margin-top: 20px">
            <p>
                <?= Yii::t('frontend', 'The above error occurred while the Web server was processing your request.') ?>
            </p>
            <p>
                <?= Yii::t('frontend', 'Please contact us if you think this is a server error. Thank you.') ?>
            </p>
        </div>
    </div>
</div>