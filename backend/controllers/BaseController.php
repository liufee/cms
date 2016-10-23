<?php
namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Base controller is the whole backend controllers parent class, and supported basic operation(such as crud,sort...).
 */
class BaseController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index', $this->getIndexData());
    }

    public function actionCreate(){
        $model = $this->getModel();
        if( yii::$app->request->isPost ) {
            if ( $model->load(yii::$app->request->post()) && $model->validate() && $model->save() ) {
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $model->loadDefaultValues();
        $array = array_merge(['model'=>$model], $this->getCreateData());
        return $this->render('create',$array);
    }

    public function actionDelete($id)
    {
        if(yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if(!$id) return['code'=>1, 'message' => yii::t('app', "Id doesn't exit" )];
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
            if(!$id) return $this->render('/error/error', [
                'code' => '403',
                'name' => 'Params required',
                'message' => yii::t('app', "Id doesn't exit"),
            ]);
            $model = $this->getModel($id);
            if($model) {
                $model->delete();
            }
            return $this->redirect(yii::$app->request->headers['referer']);
        }
    }

    public function actionUpdate($id)
    {
        if(!$id) return $this->render('/error/error', [
            'code' => '403',
            'name' => 'Params required',
            'message' => yii::t('app', "Id doesn't exit"),
        ]);
        $model = $this->getModel($id);
        if(!$model) return $this->render('/error/error', [
            'code' => '403',
            'name' => 'Params required',
            'message' => yii::t('app', "Id doesn't exit"),
        ]);
        if ( Yii::$app->request->isPost ) {
            if( $model->load(Yii::$app->request->post()) && $model->validate() && $model->save() ){
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
        if(!$id) return $this->render('/error/error', [
            'code' => '403',
            'name' => 'Params required',
            'message' => yii::t('app', "Id doesn't exit"),
        ]);
        $model = $this->getModel($id);
        if(!$model) return $this->render('/error/error', [
            'code' => '403',
            'name' => 'Params required',
            'message' => yii::t('app', "Id doesn't exit"),
        ]);
        $model->$field = $status;
        if( yii::$app->request->getIsAjax() ) {
            if ($model->save()) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                return ['code' => 1, 'message' => $err];
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

    public function getIndexData()
    {
        return [];
    }

    public function getCreateData()
    {
        return [];
    }

}
