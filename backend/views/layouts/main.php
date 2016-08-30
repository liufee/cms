<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\web\admin\AppAsset;//use backend\assets\AppAsset;
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
        <?=$this->render('/widgets/_flash') ?>
        <?= $content?>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
