<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?= $this->render("/widgets/_language-js")?>
</head>
<body class="gray-bg"><style>.m-t-md{margin-top:0px}</style>
<?php $this->beginBody() ?>
    <div class="wrapper wrapper-content">
        <div class="row">
            <?php
            if( Yii::$app->getSession()->hasFlash('success') ) {
                echo Alert::widget([
                    'options' => [
                        'class' => 'alert-success', //这里是提示框的class
                    ],
                    'body' => Yii::$app->getSession()->getFlash('success'), //消息体
                ]);
            }
            if( Yii::$app->getSession()->hasFlash('error') ) {
                echo Alert::widget([
                    'options' => [
                        'class' => 'alert-warning',
                    ],
                    'body' => Yii::$app->getSession()->getFlash('error'),
                ]);
                echo Alert::widget([
                    'options' => [
                        'class' => 'alert-warning',
                    ],
                    'body' => Yii::$app->getSession()->getFlash('reason'),
                ]);
            }
            ?>
        </div>
        <?= $content?>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
