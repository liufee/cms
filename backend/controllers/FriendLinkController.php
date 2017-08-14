<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use yii\data\ActiveDataProvider;
use backend\models\FriendLink;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\StatusAction;

/**
 * FriendLink controller
 */
class FriendLinkController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $query = FriendLink::find();
                    $dataProvider = new ActiveDataProvider([
                        'query' => $query,
                        'sort' => [
                            'defaultOrder' => [
                                'sort' => SORT_ASC,
                                'id' => SORT_ASC,
                            ],
                        ]
                    ]);
                    return [
                        'dataProvider' => $dataProvider,
                    ];
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => FriendLink::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => FriendLink::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => FriendLink::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => FriendLink::className(),
            ],
            'status' => [
                'class' => StatusAction::className(),
                'modelClass' => FriendLink::className(),
            ],
        ];
    }

}
