<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-20 11:10
 */

$current1 = $current2 = $current3 = '';
switch (yii::$app->controller->action->id) {
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
        <li class="<?= $current1 ?>"><em>1</em><?= yii::t('install', 'Check Environment') ?></li>
        <li class="<?= $current2 ?>"><em>2</em><?= yii::t('install', 'Create Data') ?></li>
        <li class="<?= $current3 ?>"><em>3</em><?= yii::t('install', 'Success') ?></li>
    </ul>
</div>
