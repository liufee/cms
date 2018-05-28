<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-20 11:10
 */

$current1 = $current2 = $current3 = '';
switch (Yii::$app->controller->action->id) {
    case "check-environment":
        $current1 = 'current';
        break;
    case "setinfo":
        $current2 = "current";
        break;
    case "success":
        $current3 = "current";
        break;
}
?>
<div class="step">
    <ul class="unstyled">
        <li class="<?= $current1 ?>"><em>1</em><?= Yii::t('install', 'Check Environment') ?></li>
        <li class="<?= $current2 ?>"><em>2</em><?= Yii::t('install', 'Create Data') ?></li>
        <li class="<?= $current3 ?>"><em>3</em><?= Yii::t('install', 'Success') ?></li>
    </ul>
</div>
