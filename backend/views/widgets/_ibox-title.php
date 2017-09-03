<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-20 14:03
 */

use yii\widgets\Breadcrumbs;

?>

<div class="ibox-title">
    <?= Breadcrumbs::widget([
        'homeLink' => false,
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
    ?>
</div>
