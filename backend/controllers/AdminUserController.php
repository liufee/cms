<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/31
 * Time: 17:01
 */
namespace backend\controllers;

use backend\models\AdminRoles;
use yii;
use backend\models\User;
use yii\data\ActiveDataProvider;
use backend\models\AdminRoleUser;

class AdminUserController extends BaseController
{

    public function actionIndex()
    {
        $query = User::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionCreate()
    {
        $model = new User();
        $model->setScenario('create');
        if(yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
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
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->getModel($id);
        $model->setScenario('update');
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
            $model = User::findOne(['id'=>yii::$app->user->identity->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function getModel($id = '')
    {
        return User::findOne(['id'=>$id]);
    }

    public function actionUpdateSelf()
    {
        $model = User::findOne(['id'=>yii::$app->user->identity->id]);
        $model->setScenario('self-update');
        if(yii::$app->request->isPost){
            if( $model->validate() && $model->load(yii::$app->request->post()) && $model->self_update() ){
                Yii::$app->getSession()->setFlash('success', '成功');
            }else{
                Yii::$app->getSession()->setFlash('error', '失败');
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
            $model = User::findOne(['id'=>yii::$app->user->identity->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateSelfAvatar()
    {
        $model = User::findOne(['id'=>yii::$app->user->identity->id]);
        $model->setScenario('update');
        if(yii::$app->request->isPost && $model->validate() && $model->load(yii::$app->request->post()) && $model->save()){
            return $this->redirect(['site/main']);
        }
        return $this->render('update-self-avatar', [
            'model' => $model,
        ]);
    }

    public function actionAssign($uid='')
    {
        $model = AdminRoleUser::findOne(['uid'=>$uid]);//->createCommand()->getRawSql();var_dump($model);die;
        if($model == ''){//echo 11;die;
            $model = new AdminRoleUser();
        }
        $model->uid = $uid;
        if( yii::$app->request->isPost ){
            if($model->load(yii::$app->request->post()) && $model->save()){
                Yii::$app->getSession()->setFlash('success', '成功');
            }else{//var_dump($model->getErrors());die;
                Yii::$app->getSession()->setFlash('error', '失败');
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
        }
        $temp = AdminRoles::find()->asArray()->all();
        $roles = [];
        foreach ($temp as $v){
            $roles[$v['id']] = $v['role_name'];
        }
        return $this->render('assign', [
            'model' => $model,
            'roles' => $roles,
        ]);
    }

    public function actionA()
    {echo 2;die;
        return $this->render('a');
    }
}