<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-23 13:50
 */


namespace common\services;

use backend\models\search\AdminLogSearch;
use common\models\AdminLog;
use yii\base\Exception;

class LogService extends Service implements LogServiceInterface
{

    public function getSearchModel(array $query, array $options=[])
    {
        return new AdminLogSearch();
    }

    public function getModel($id, array $options = [])
    {
        return AdminLog::findOne($id);
    }

    public function newModel(array $options = [])
    {
        throw new Exception("Not need new model");
    }
}