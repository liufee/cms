<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-29 14:42
 */

namespace common\services;

use backend\models\form\AdForm;
use backend\models\search\OptionsSearch;
use common\models\Options;
use yii\base\Exception;

class AdService extends Service implements AdServiceInterface
{
    public function getSearchModel(array $query, array $options = [])
    {
        return new OptionsSearch(['type'=>Options::TYPE_AD]);
    }

    public function getModel($id, array $options = [])
    {
        return AdForm::findOne($id);
    }

    public function newModel(array $options = [])
    {
        $model = new AdForm();
        $model->loadDefaultValues();
        return $model;
    }

    public function getAdByName($name)
    {
        $model = AdForm::findOne(["type"=>Options::TYPE_AD, "name"=>$name]);
        if( $model === null ) throw new Exception("Not exists advertisement named " . $name);
        return $model;
    }

}