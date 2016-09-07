<?php
/**
 * Ahthor: lf
 * Email: job@feehi.com
 * Blog: http://blog.feehi.com
 * Date: 2016/9/1 15:58
 */
namespace backend\controllers;

use Yii;
use backend\models\File;
use backend\models\FileSearch;
use backend\models\FileUsage;
use yii\data\ActiveDataProvider;

class FileController extends BaseController
{

    public function getIndexData()
    {
        $searchModel = new FileSearch();
        $dataProvider = $searchModel->search(yii::$app->request->queryParams);
        return [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
    }

    public function actionViewLayer($id)
    {
        $query = FileUsage::find()->where(['fid'=>$id])->select([]);
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
        if($id == ''){
            $model = new File();
        }else {
            $model = File::findOne(['id' => $id]);
            if ($model == null) return null;
        }
        return $model;
    }

}