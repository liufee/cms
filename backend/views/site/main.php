<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31
 * Time: 14:17
 */
?>
<div class="row">
    <div class="col-sm-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-success pull-right"><?=Yii::t('app', 'Month')?></span>
                <h5>文章</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins"><?=$statics['ARTICLE']?></h1>
                <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i>
                </div>
                <small>总共</small>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right"><?=Yii::t('app', 'Full Year')?></span>
                <h5>评论</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins"><?=$statics['PAGE']?></h1>
                <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i>
                </div>
                <small>总共</small>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-primary pull-right"><?=Yii::t('app', 'Today')?></span>
                <h5>用户</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins"><?=$statics['USER']?></h1>
                <div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i>
                </div>
                <small>总共</small>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-danger pull-right"><?=Yii::t('app', 'Latest Month')?></span>
                <h5>友链</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins"><?=$statics['FRIEND_LINK']?></h1>
                <div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i>
                </div>
                <small>总共</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>最新评论</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">

                <div>
                    <div class="feed-activity-list">
                        <?php
                        foreach($comments as $comment){
                        ?>
                        <div class="feed-element">
                            <a class="pull-left">
                                <img alt="image" class="img-circle" src="https://secure.gravatar.com/avatar/<?=md5($comment->email)?>?s=50">
                            </a>
                            <div class="media-body ">
                                <small class="pull-right"><?=\feehi\libs\Help::tranTime($comment->created_at)?></small>
                                <strong><?=$comment->nickname?></strong>
                                <br>
                                <small class="text-muted"><?=yii::$app->formatter->asDate($comment->created_at)?> 在 <?=$comment->article->title?></small>
                                <div class="well">
                                    <?=$comment->content?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <a href="<?=yii\helpers\Url::to(['comment/index'])?>" class="btn btn-primary btn-block m"><i class="fa"></i>审核评论</a>

                </div>

            </div>
        </div>

    </div>
    <div class="col-sm-6">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?=Yii::t('app', 'Environment')?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content no-padding">
                    <ul class="list-group">
                        <style>.list-group-item>.badge{float:left} li.list-group-item strong{margin-left: 15px;}</style>
                        <li class="list-group-item">
                            <span class="badge badge-primary">&nbsp;&nbsp;</span><strong>OS</strong>: <?=$info['OPERATING_SYSTEM']?>
                        </li>
                        <li class="list-group-item ">
                            <span class="badge badge-info">&nbsp;&nbsp;</span> <strong>Web Server</strong>: <?=$info['OPERATING_ENVIRONMENT']?>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-danger">&nbsp;&nbsp;</span> <strong><?=Yii::t('app', 'PHP Execute Method')?></strong>: <?=$info['PHP_RUN_MODE']?>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-success">&nbsp;&nbsp;</span> <strong><?=Yii::t('app', 'File Upload Limit')?></strong>: <?=$info['UPLOAD_MAX_FILESIZE']?>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-success">&nbsp;&nbsp;</span> <strong><?=Yii::t('app', 'Script Time Limit')?></strong>: <?=$info['MAX_EXECUTION_TIME']?>
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-success">&nbsp;&nbsp;</span> <strong><?=Yii::t('app', 'MySQL Version')?></strong>: <?=$info['MYSQL_VERSION']?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-sm-12">

            <div class="ibox-title">
                <h5><?=Yii::t('app', 'Status')?></h5>
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
                        <span><?=Yii::t('app', 'Memory Usage')?></span>
                        <small class="pull-right"><?= $status['MEM']['NUM']?></small>
                    </div>
                    <div class="progress progress-small">
                        <div style="width: <?= $status['MEM']['PERCENTAGE']?>;" class="progress-bar"></div>
                    </div>

                    <div>
                        <span><?=Yii::t('app', 'Real Memory Usage')?></span>
                        <small class="pull-right"><?= $status['REAL_MEM']['NUM']?></small>
                    </div>
                    <div class="progress progress-small">
                        <div style="width: <?= $status['REAL_MEM']['PERCENTAGE']?>;" class="progress-bar"></div>
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
                        <span><?=Yii::t('app', 'Disk Usage')?></span>
                        <small class="pull-right"><?= $status['DISK_SPACE']['NUM']?></small>
                    </div>
                    <div class="progress progress-small">
                        <div style="width: <?= $status['DISK_SPACE']['PERCENTAGE']?>%;" class="progress-bar progress-bar-danger"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--
        <div class="col-sm-4">
            <div class="ibox-title">
                <h5><?=yii::t('app', 'Statics')?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link ui-sortable">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div>
                <table class="table" style="background-color: white;">
                    <tbody>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-danger m-r-sm"><?=$statics['ARTICLE']?></button>
                            <?=yii::t('app', 'Articles')?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary m-r-sm"><?=$statics['CATEGORY']?></button>
                            <?=yii::t('app', 'Categories')?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info m-r-sm"><?=$statics['PAGE']?></button>
                            <?=yii::t('app', 'Pages')?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-info m-r-sm"><?=$statics['USER']?></button>
                            <?=yii::t('app', 'Users')?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success m-r-sm"><?=$statics['BACKEND_MENU']?></button>
                            <?=yii::t('app', 'Backend Menus')?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger m-r-sm"><?=$statics['FRONTEND_MENU']?></button>
                            <?=yii::t('app', 'Frontend Menus')?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-warning m-r-sm"><?=$statics['ADMIN_USER']?></button>
                            <?=yii::t('app', 'Administrators')?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default m-r-sm"><?=$statics['ROLE']?></button>
                            <?=yii::t('app', 'Roles')?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning m-r-sm"><?=$statics['FRIEND_LINK']?></button>
                            <?=yii::t('app', 'Friendly Links')?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>-->
        </div>
    </div>
</div>


