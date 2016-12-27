<?php
namespace backend\controllers;

use yii\data\ActiveDataProvider;
use backend\models\FriendLink;

/**
 * FriendLink controller
 */
class FriendLinkController extends BaseController
{


    public function getIndexData()
    {
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

    public function getModel($id="")
    {
        if($id == ''){
            $model = new FriendLink();
        }else{
            $model = FriendLink::findOne(['id'=>$id]);
        }
        return $model;
    }

}
