<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-23 09:38
 */

namespace common\services;


use backend\models\search\FriendlyLinkSearch;
use common\models\FriendlyLink;

class FriendlyLinkService extends Service implements FriendlyLinkServiceInterface
{
    public function getSearchModel(array $options=[])
    {
        return new FriendlyLinkSearch();
    }

    public function getModel($id, array $options=[])
    {
        return FriendlyLink::findOne($id);
    }

    public function newModel(array $options=[]){
        $model = new FriendlyLink();
        $model->loadDefaultValues();
        return $model;
    }

    public function getFriendlyLinks()
    {
        return FriendlyLink::find()->where(['status' => FriendlyLink::DISPLAY_YES])->orderBy(['sort'=>SORT_ASC, 'id' => SORT_DESC])->all();
    }
}