<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/4/1412:09
 */
use feehi\grid\GridView;
use backend\models\FileUsage;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-content">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{items}\n{pager}",
                    'columns'=>[
                        [
                            'class' => 'feehi\grid\CheckboxColumn',
                        ],
                        [
                            'attribute' => 'id',
                        ],
                        [
                            'attribute' => 'type',
                            'value' => function($model){
                                return FileUsage::TYPE_TEXT[$model->type];
                            }
                        ],
                        [
                            'attribute' => 'use_id',
                        ],
                        [
                            'attribute' => 'count',
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['date'],
                        ]
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>