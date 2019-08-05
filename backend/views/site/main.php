<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-31 14:17
 */

use common\widgets\JsBlock;
use yii\helpers\Url;

/**
 * @var $statics array
 * @var $this yii\web\View
 */
$this->registerCss("
     .environment .list-group-item > .badge {float: left}
     .environment  li.list-group-item strong {margin-left: 15px}
     ul#notify .list-group-item{line-height:15px}
")
?>
<div class="row">
    <div class="col-sm-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-success pull-right"><?= Yii::t('app', 'Month') ?></span>
                <h5><?= Yii::t('app', 'Articles') ?></h5>
            </div>
            <div class="ibox-content openContab" href="<?=Url::to(['article/index'])?>" title="<?= Yii::t('app', 'Articles')?>" style="cursor: pointer">
                <h1 class="no-margins"><?= $statics['ARTICLE'][0] ?></h1>
                <div class="stat-percent font-bold text-success"><?= $statics['ARTICLE'][1] ?>% <i class="fa fa-bolt"></i></div>
                <small><?= Yii::t('app', 'Total') ?></small>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right"><?= Yii::t('app', 'Today') ?></span>
                <h5><?= Yii::t('app', 'Comments') ?></h5>
            </div>
            <div class="ibox-content openContab" href="<?=Url::to(['comment/index'])?>" title="<?= Yii::t('app', 'Comments')?>" style="cursor: pointer">
                <h1 class="no-margins"><?= $statics['COMMENT'][0] ?></h1>
                <div class="stat-percent font-bold text-info"><?= $statics['COMMENT'][1] ?>% <i class="fa fa-level-up"></i></div>
                <small><?= Yii::t('app', 'Total') ?></small>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-primary pull-right"><?= Yii::t('app', 'Month') ?></span>
                <h5><?= Yii::t('app', 'Users') ?></h5>
            </div>
            <div class="ibox-content openContab" href="<?=Url::to(['user/index'])?>" title="<?= Yii::t('app', 'Users')?>" style="cursor: pointer">
                <h1 class="no-margins"><?= $statics['USER'][0] ?></h1>
                <div class="stat-percent font-bold text-navy"><?= $statics['USER'][1] ?>% <i class="fa fa-level-up"></i></div>
                <small><?= Yii::t('app', 'Total') ?></small>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-success pull-right"><?= Yii::t('app', 'Month') ?></span>
                <h5><?= Yii::t('app', 'Friendly Links') ?></h5>
            </div>
            <div class="ibox-content openContab" href="<?=Url::to(['friendly-link/index'])?>" title="<?= Yii::t('app', 'Friendly Links')?>" style="cursor: pointer">
                <h1 class="no-margins"><?= $statics['FRIEND_LINK'][0] ?></h1>
                <div class="stat-percent font-bold text-info"><?= $statics['FRIEND_LINK'][1] ?>% <i class="fa fa-level-up"></i></div>
                <small><?= Yii::t('app', 'Total') ?></small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= Yii::t('app', 'Notify') ?></h5>
                    <div class="ibox-tools">
                        <a target="_blank" href="http://api.feehi.com/cms"><?= Yii::t('app', 'More')?></a>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        <a class="close-link"><i class="fa fa-times"></i></a>
                    </div>
                </div>
                <div class="ibox-content no-padding">
                    <ul class="list-group" id="notify">
                        <li class="list-group-item">
                            <div class="sk-spinner sk-spinner-fading-circle">
                                <div class="sk-circle1 sk-circle"></div>
                                <div class="sk-circle2 sk-circle"></div>
                                <div class="sk-circle3 sk-circle"></div>
                                <div class="sk-circle4 sk-circle"></div>
                                <div class="sk-circle5 sk-circle"></div>
                                <div class="sk-circle6 sk-circle"></div>
                                <div class="sk-circle7 sk-circle"></div>
                                <div class="sk-circle8 sk-circle"></div>
                                <div class="sk-circle9 sk-circle"></div>
                                <div class="sk-circle10 sk-circle"></div>
                                <div class="sk-circle11 sk-circle"></div>
                                <div class="sk-circle12 sk-circle"></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div>
            <div class="ibox float-e-margins environment">
                <div class="ibox-title">
                    <h5><?= Yii::t('app', 'Environment') ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        <a class="close-link"><i class="fa fa-times"></i></a>
                    </div>
                </div>
                <div class="ibox-content no-padding">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <span class="badge badge-primary">&nbsp;&nbsp;</span><strong>Feehi CMS</strong>: <?= Yii::$app->getVersion() ?>
                        </li>
                        <li class="list-group-item ">
                            <span class="badge badge-info">&nbsp;&nbsp;</span> <strong>Web Server</strong>: <?= $info['OPERATING_ENVIRONMENT'] ?>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-success">&nbsp;&nbsp;</span>
                            <strong><?= Yii::t('app', 'Database Info') ?></strong>: <?= $info['DB_INFO'] ?>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-success">&nbsp;&nbsp;</span>
                            <strong><?= Yii::t('app', 'File Upload Limit') ?></strong>: <?= $info['UPLOAD_MAX_FILE_SIZE'] ?>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-success">&nbsp;&nbsp;</span>
                            <strong><?= Yii::t('app', 'Script Time Limit') ?></strong>: <?= $info['MAX_EXECUTION_TIME'] ?>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-danger">&nbsp;&nbsp;</span>
                            <strong><?= Yii::t('app', 'PHP Execute Method') ?></strong>: <?= $info['PHP_RUN_MODE'] ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div>
            <div class="ibox-title">
                <h5><?= Yii::t('app', 'Status') ?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link ui-sortable">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div>
                    <div>
                        <span><?= Yii::t('app', 'Memory Usage') ?></span>
                        <small class="pull-right">
                            <?php if (PHP_OS == 'Linux') {
                                echo $status['MEM']['NUM'];
                            } else {
                                echo Yii::t('app', 'Only supported linux system');
                            }
                            ?>
                        </small>
                    </div>
                    <div class="progress progress-small">
                        <div style="width: <?= $status['MEM']['PERCENTAGE'] ?>;" class="progress-bar progress-bar<?=$status['MEM']['PERCENTAGE']>80 ? '-danger' : ''?>"></div>
                    </div>

                    <div>
                        <span><?= Yii::t('app', 'Real Memory Usage') ?></span>
                        <small class="pull-right">
                            <?php if (PHP_OS == 'Linux') {
                                echo $status['REAL_MEM']['NUM'];
                            } else {
                                echo Yii::t('app', 'Only supported linux system');
                            }
                            ?>
                        </small>
                    </div>
                    <div class="progress progress-small">
                        <div style="width: <?= $status['REAL_MEM']['PERCENTAGE'] ?>;" class="progress-bar progress-bar<?=$status['REAL_MEM']['PERCENTAGE']>80 ? '-danger' : ''?>"></div>
                    </div>
                    <!--
                    <div>
                        <span>CPU</span>
                        <small class="pull-right">20 GB</small>
                    </div>
                    <div class="progress progress-small">
                        <div style="width: 50%;" class="progress-bar"></div>
                    </div>
                    -->
                    <div>
                        <span><?= Yii::t('app', 'Disk Usage') ?></span>
                        <small class="pull-right"><?= $status['DISK_SPACE']['NUM'] ?></small>
                    </div>
                    <div class="progress progress-small">
                        <div style="width: <?= $status['DISK_SPACE']['PERCENTAGE'] ?>%;" class="progress-bar progress-bar<?=$status['DISK_SPACE']['PERCENTAGE']>80 ? '-danger' : ''?>"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= Yii::t('app', 'Latest Comments') ?></h5>
                <div class="ibox-tools">
                    <a class="openContab" title="<?=Yii::t('app', 'Comments')?>" target="_blank" href="<?=Url::to(['comment/index'])?>"><?= Yii::t('app', 'More')?></a>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    <a class="close-link"><i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="ibox-content">
                <div>
                    <div class="feed-activity-list">
                        <?php
                        foreach ($comments as $comment) {
                            ?>
                            <div class="feed-element">
                                <a class="pull-left"><img alt="image" class="img-circle" src="https://secure.gravatar.com/avatar/<?= md5($comment->email) ?>?s=50"></a>
                                <div class="media-body ">
                                    <small class="pull-right"><?= Yii::$app->getFormatter()->asRelativeTime($comment->created_at) ?></small>
                                    <strong><?= $comment->nickname ?></strong>
                                    <br>
                                    <small class="text-muted"><?= Yii::$app->getFormatter()->asDate($comment->created_at) ?> <?=Yii::t('app', 'at')?> <a class="openContab" data-index="0" title="<?=yii::t('app',"Articles")?>" href="<?= isset($comment->article->id) ? Url::toRoute(['article/view-layer', 'id'=>$comment->article->id]) : '#' ?>"><?= isset($comment->article->title) ? $comment->article->title : '' ?></a></small>
                                    <div data-index="0" class="openContab well" href="<?=Url::toRoute(['comment/index']) ?>" title="<?= Yii::t('app', 'Comments')?>" style="cursor: pointer">
                                        <?= $comment->content ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php JsBlock::begin() ?>
<script>
$(document).ready(function () {
    var notify = $("#notify");
    $.ajax({
        dataType:"jsonp",
        url:"//api.feehi.com/cms/notify",
        success:function (dataAll) {
            data = dataAll.rows;
            notify.empty();
            var lis = "";
            for(var index in data){
                var label = '';
                if( data[index].label ){
                    label = data[index].label;
                }
                lis += "<li class='list-group-item'> \
                                <a target='_blank' class='' href=\" " + data[index].href +" \"> " + data[index].title + " </a>\
                                " + label +  "\
                                <small class='pull-right block'>" + data[index].createdAt + "</small> \
                        </li>"
            }
            notify.append(lis);
        },
        error:function (data) {
            notify.empty();
            notify.append("<li class='list-group-item'>Connect error</li>");
        }
    });
})
</script>
<?php JsBlock::end() ?>
