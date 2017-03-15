<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-09-01 15:58
 */
namespace backend\controllers;

use backend\models\File;
use backend\models\FileSearch;
use backend\models\FileUsage;
use yii\data\ActiveDataProvider;

class FileController extends BaseController
{

    public function getIndexData()
    {
        $searchModel = new FileSearch();
        $dataProvider = $searchModel->search(yii::$app->request->getQueryParams());
        return [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
    }

    public function actionViewLayer($id)
    {
        $query = FileUsage::find()->where(['fid' => $id])->select([]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        return $this->render('view', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function getModel($id = '')
    {
        if ($id == '') {
            $model = new File();
        } else {
            $model = File::findOne(['id' => $id]);
            if ($model == null) {
                return null;
            }
        }
        return $model;
    }

}