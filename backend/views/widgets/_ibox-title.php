<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-20 14:03
 */

use yii\widgets\Breadcrumbs;

?>
<style>
    ul.breadcrumb li a{
        color: #337ab7;
    }
</style>
<div class="ibox-title">
    <span style="float: left;">
        <?= Breadcrumbs::widget([
            'homeLink' => false,
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ])
        ?>
    </span>
    <div class="ibox-tools">
        <a class="collapse-link ui-sortable">
            <i class="fa fa-chevron-up"></i>
        </a>
        <a class="close-link">
            <i class="fa fa-times"></i>
        </a>
    </div>
</div>
