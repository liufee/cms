<?php
namespace backend\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Base controller is the class of backend controllers
 */
class BaseController extends Controller
{

    public function actionDelete($id)
    {
        if(yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $ids = explode(',', $id);
            $errorIds = [];
            foreach ($ids as $one){
                $model = $this->getModel($one);
                if($model) {
                    if (!$result = $model->delete()){
                        $errorIds[] = $one;
                    }
                }
            }
            if(count($errorIds) == 0){
                return $this->redirect(yii::$app->request->headers['referer']);
            }else{
                return ['status'=>0, 'msg'=>implode(',', $errorIds)];
            }
        }else {
            $model = $this->getModel($id);
            if($model) {
                $model->delete();
            }
            return $this->redirect(yii::$app->request->headers['referer']);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->getModel($id);
        if(!$model) throw new BadRequestHttpException(yii::t('app', 'Id doesn\'t exit' ));
        if ( Yii::$app->request->isPost ) {
            if( $model->load(Yii::$app->request->post()) && $model->save() ){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['update', 'id'=>$model->primaryKey]);
            }else{
                Yii::$app->getSession()->setFlash('error', yii::t('app', 'Error'));
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSort()
    {
        if(yii::$app->request->isPost) {
            $data = yii::$app->request->post();
            if (!empty($data['sort'])) {
                foreach ($data['sort'] as $key => $value) {
                    $model = $this->getModel($key);
                    if( $model->sort != $value ){
                        $model->sort = $value;
                        $model->save();
                    }
                }
            }
        }
        $this->redirect(['index']);
    }

    public function actionChangeStatus($id='', $status=0, $field='status')
    {
        $model = $this->getModel($id);
        if(!$model) throw new BadRequestHttpException(yii::t('app', 'Id doesn\'t exit' ));
        $model->$field = $status;
        if( $model->save() ){
            return $this->redirect(yii::$app->request->headers['referer']);
        }
    }

    public function getModel($id='')
    {
        return '';
    }

}
