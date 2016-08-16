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
        <div class="row">
            <?php
            if( Yii::$app->getSession()->hasFlash('success') ) {
                $successTitle = yii::t('app', 'Success');
                $info = Yii::$app->getSession()->getFlash('success');
                $str =<<<EOF
              toastr.options = {
                  "closeButton": true,
                  "debug": true,
                  "progressBar": true,
                  "positionClass": "toast-top-center",
                  "showDuration": "400",
                  "hideDuration": "1000",
                  "timeOut": "7000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
              };
              toastr.success("{$successTitle}", "{$info}");
EOF;
                $this->registerJs($str);
            }
            if( Yii::$app->getSession()->hasFlash('error') ) {
                $errorTitle = yii::t('app', 'Error');
                $info = Yii::$app->getSession()->getFlash('error');
                $str =<<<EOF
              toastr.options = {
                  "closeButton": true,
                  "debug": true,
                  "progressBar": true,
                  "positionClass": "toast-top-full-width",
                  "showDuration": "400",
                  "hideDuration": "1000",
                  "timeOut": "7000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
              };
              toastr.error("{$errorTitle}", "{$info}");
EOF;
                $this->registerJs($str);
            }
            ?>
        </div>
        <?= $content?>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
