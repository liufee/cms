<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-10-19 14:40
 */
?>
<section class="section">
    <div class="step">
        <ul class="unstyled">
            <li class="on"><em>1</em><?= Yii::t('install', 'Check Environment') ?></li>
            <li class="on"><em>2</em><?= Yii::t('install', 'Create Data') ?></li>
            <li class="current"><em>3</em><?= Yii::t('install', 'Finish Install') ?></li>
        </ul>
    </div>
    <div class="install" id="log">
        <ul id="loginner" class="unstyled"></ul>
    </div>
    <div class="bottom text-center">
        <a href="javascript:;"><i class="fa fa-refresh fa-spin"></i>&nbsp;<?= Yii::t('install', 'Installing') ?>...</a>
    </div>
</section>
<script type="text/javascript">
    function showmsg(content, status) {
        var icon = '<i class="fa fa-check correct"></i> ';
        if (status == "error") {
            icon = '<i class="fa fa-remove error"></i> ';
        }
        $('#loginner').append("<li>" + icon + content + "</li>");
        $("#log").scrollTop(1000000000);
    }
</script>
