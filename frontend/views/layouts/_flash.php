<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

use common\widgets\JsBlock;

if (Yii::$app->getSession()->hasFlash('success')) {
    $successTitle = Yii::t('app', 'Success');
    $info = Yii::$app->getSession()->getFlash('success');
    $str = <<<EOF
       toastr.options = {
          "closeButton": true,
          "debug": true,
          "progressBar": true,
          "positionClass": "toast-top-center",
          "showDuration": "400",
          "hideDuration": "1000",
          "timeOut": "1000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
       };
       toastr.success("{$successTitle}", "{$info}");
EOF;
    JsBlock::begin();
    echo $str;
    JsBlock::end();
}
if (Yii::$app->getSession()->hasFlash('error')) {
    $errorTitle = Yii::t('app', 'Error');
    $info = Yii::$app->getSession()->getFlash('error');
    $str = <<<EOF
       toastr.options = {
          "closeButton": true,
          "debug": true,
          "progressBar": true,
          "positionClass": "toast-top-center",
          "showDuration": "400",
          "hideDuration": "1000",
          "timeOut": "1000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
       };
       toastr.error("{$errorTitle}", "{$info}");
EOF;
    JsBlock::begin();
    echo $str;
    JsBlock::end();
}
?>