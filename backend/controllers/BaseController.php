<?php
namespace backend\controllers;

use Yii;
use yii\web\Response;
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
            Yii::$app->response->format = Response::FORMAT_JSON;
            if(!$id) throw new BadRequestHttpException(yii::t('app', 'Id doesn\'t exit' ));
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
                return ['code'=>0, 'message'=>yii::t('app', 'Success')];
            }else{
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0];
                }
                if($err != '') $err = '.'.$err;
                return ['code'=>1, 'message'=>'id '.implode(',', $errorIds).$err];
            }
        }else {
            if(!$id) throw new BadRequestHttpException(yii::t('app', 'Id doesn\'t exit' ));
            $model = $this->getModel($id);
            if($model) {
                $model->delete();
            }
            return $this->redirect(yii::$app->request->headers['referer']);
        }
    }

    public function actionUpdate($id)
    {
        if(!$id) throw new BadRequestHttpException(yii::t('app', 'Id doesn\'t exit' ));
        $model = $this->getModel($id);
        if(!$model) throw new BadRequestHttpException(yii::t('app', 'Id doesn\'t exit' ));
        if ( Yii::$app->request->isPost ) {
            if( $model->load(Yii::$app->request->post()) && $model->save() ){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['update', 'id'=>$model->primaryKey]);
            }else{
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
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
        if( yii::$app->request->getIsAjax() ) yii::$app->response->format = Response::FORMAT_JSON;
        if(!$id) throw new BadRequestHttpException(yii::t('app', 'Id doesn\'t exit' ));
        $model = $this->getModel($id);
        if(!$model) throw new BadRequestHttpException(yii::t('app', 'Id doesn\'t exit' ));
        $model->$field = $status;
        if( yii::$app->request->getIsAjax() ) {
            if ($model->save()) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                return ['code' => 1, 'message' => yii::t('app', 'Erorr')];
            }
        }else{
            $model->save();
            return $this->redirect(yii::$app->request->headers['referer']);
        }
    }

    public function getModel($id='')
    {
        return '';
    }

}
