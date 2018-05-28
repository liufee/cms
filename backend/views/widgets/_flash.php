<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

if (Yii::$app->getSession()->hasFlash('success')) {
    $successTitle = addslashes( Yii::t('app', 'Success') );
    $info = addslashes( Yii::$app->getSession()->getFlash('success') );
    $str = <<<EOF
       toastr.options = {
          "closeButton": true,
          "debug": false,
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
    $this->registerJs($str);
}
if (Yii::$app->getSession()->hasFlash('error')) {
    $errorTitle = addslashes( Yii::t('app', 'Error') );
    $info = addslashes( Yii::$app->getSession()->getFlash('error') );
    $str = <<<EOF
       toastr.options = {
          "closeButton": true,
          "debug": false,
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
    $this->registerJs($str);
}
?>