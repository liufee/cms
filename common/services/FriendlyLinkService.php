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
    public function getSearchModel(array $query, array $options=[])
    {
        return new FriendlyLinkSearch();
    }

    public function getModel($id, array $options=[])
    {
        return FriendlyLink::findOne($id);
    }

    public function getNewModel(array $options=[]){
        $model = new FriendlyLink();
        $model->loadDefaultValues();
        return $model;
    }
}