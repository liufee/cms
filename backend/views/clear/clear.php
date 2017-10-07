<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-13 17:39
 */
use common\widgets\JsBlock;

/**
 * @var $this yii\web\View
 */

$this->title = yii::t('app', 'Clear Backend');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-content">
                <?= yii::t('app', 'Success') ?>
            </div>
        </div>
    </div>
</div>
<?php JsBlock::begin()?>
<script>
    $(document).ready(function () {
        if( parent != 'undefined' ) {
            setTimeout(function () {
                parent.closeContab($(parent.document).find(".active.J_menuTab[data-id$='clear%2Ffrontend'] i, .active.J_menuTab[data-id$='clear%2Fbackend'] i"))
            }, 1000);
        }
    })
</script>
<?php JsBlock::end() ?>