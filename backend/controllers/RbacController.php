<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 15:13
 */
namespace backend\controllers;

use Yii;
use backend\models\rbac\AuthType;
use backend\models\rbac\AuthRule;
use backend\models\rbac\AuthItem;
use backend\models\rbac\AuthAssignment;
use yii\data\ActiveDataProvider;
use backend\models\User;
use backend\models\rbac\AuthItemChild;
use backend\models\rbac\AuthItemChildForm;

class RbacController extends BaseController
{
    public function actionRule()
    {
        $query = AuthType::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ]
        ]);
        return $this->render('rule/rule', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRuleCreate()
    {
        $model = new AuthType();
        if (yii::$app->request->isPost && $model->load(yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['rule']);
        } else {
            return $this->render('rule/create', [
                'model' => $model
            ]);
        }
    }

    public function actionRuleUpdate($id)
    {
        $model = AuthType::findOne(['name' => $id]);
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'rule'
            ]);
        }

        return $this->render('rule/update', [
            'model' => $model,
        ]);
    }

    public function actionRuleDelete($id)
    {
        $model = AuthType::findOne(['name' => $id]);
        $model->delete();
        return $this->redirect([
            'rule'
        ]);
    }

    public function actionItem()
    {
        $query = AuthItem::find()->where(['type' => 2]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'rule_name' => SORT_ASC,
                    'created_at' => SORT_ASC,
                ]
            ]
        ]);
        return $this->render('item/item', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionItemCreate()
    {
        $model = new AuthItem();
        $model->type = 2;
        if (yii::$app->request->isPost && $model->load(yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['item']);
        } else {//var_dump($model->getErrors());die;
            return $this->render('item/create', [
                'model' => $model,
            ]);
        }
    }

    public function actionItemUpdate($id)
    {
        $model = AuthItem::findOne(['name' => $id]);
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'item'
            ]);
        }

        return $this->render('item/update', [
            'model' => $model,
        ]);
    }

    public function actionItemDelete($id)
    {
        $model = AuthItem::findOne(['name' => $id]);
        $model->delete();
        return $this->redirect([
            'item'
        ]);
    }

    public function actionAssignment()
    {
        $model = new AuthItem();
        $query =$model::find()->where(['type' => 1]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ]
        ]);
        return $this->render('assignment/assignment', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAssignmentCreate()
    {
        $model = new AuthItem();
        if (yii::$app->request->isPost && $model->load(yii::$app->request->post())) {
            $model->type = 1;
            $model->save();
            return $this->redirect(['assignment']);
        } else {
            return $this->render('assignment/create', [
                'model' => $model
            ]);
        }
    }

    public function actionAssignmentUpdate($id)
    {
        $model = AuthItem::findOne(['name' => $id]);
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'assignment'
            ]);
        }

        return $this->render('assignment/update', [
            'model' => $model,
        ]);
    }

    public function actionAssignmentDelete($id)
    {
        $model = AuthItem::findOne(['name' => $id]);
        $model->delete();
        return $this->redirect([
            'assignment'
        ]);
    }

    public function actionAssignPermission()
    {
        $authItems = AuthItem::find()->where(['type'=>2])->select('name')->asArray()->all();
        $arr = [];
        foreach ($authItems as $item){
            $arr[$item['name']] = $item['name'];
        }
        $model = new AuthItemChild();
        if (Yii::$app->request->isPost) {
            $data = yii::$app->request->post('AuthItemChild');//var_dump($data['child']);die;
            $origins = AuthItemChild::find()->where(['parent'=>yii::$app->request->get('role')])->asArray()->all();
            if(isset($data['child'])) {
                foreach ($origins as $origin) {
                    if (!in_array($origin['parent'], $data['child'])) AuthItemChild::findOne(['parent' => yii::$app->request->get('role'), 'child' => $origin['child']])->delete();
                }
            }else{
                AuthItemChild::deleteAll(['parent' => yii::$app->request->get('role')]);
            }
            if(isset($data['child'])) {
                foreach ($data['child'] as $child) {
                    $model = new AuthItemChild();
                    $model->parent = yii::$app->request->get('role');
                    $model->child = $child;
                    if (AuthItemChild::find()->where(['parent' => $model->parent, 'child' => $model->child])->count()) continue;
                    $model->save();
                }//var_dump($model->getErrors());die;
            }
            return $this->redirect([
                'assignment'
            ]);

        }
        return $this->render('assign-permission', [
            'authItems' => $arr,
            'model' => $model,
        ]);
    }

    public function actionUser()
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
        return $this->render('user/user', [
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionUserCreate()
    {
        $model = new User();
        $model->setScenario('create');
        if(yii::$app->request->isPost){
            if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate() && $model->save()){
                return $this->redirect(['user']);
            }
        }
        return $this->render('user/create', [
            'model' => $model,
        ]);
    }

    public function actionUserUpdate($id)
    {
        $model = User::findOne(['id' => $id]);//var_dump($model);die;
        $model->setScenario('update');
        if(yii::$app->request->isPost){
            if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate() && $model->save()){
                return $this->redirect(['user']);
            }
        }
        return $this->render('user/update', [
            'model' => $model,
        ]);
    }

    public function actionUserDelete($id)
    {
        User::findOne(['id'=>$id])->delete();
        return $this->redirect(['user']);
    }

    public function actionAssign()
    {
        $authItems = AuthItem::find()->where(['type'=>1])->select('name')->asArray()->all();
        $arr = [];
        foreach ($authItems as $item){
            $arr[$item['name']] = $item['name'];
        }
        $model = new AuthAssignment();
        $model->created_at = time();
        if (Yii::$app->request->isPost) {
            $data = yii::$app->request->post('Assign');//var_dump($data);die;
            $origins = AuthAssignment::find()->where(['user_id'=>yii::$app->request->get('uid')])->asArray()->all();
            if(isset($data['role'])) {
                foreach ($origins as $origin) {
                    if (!in_array($origin['item_name'], $data['role'])) AuthAssignment::findOne(['user_id' => yii::$app->request->get('uid'), 'item_name' => $origin['item_name']])->delete();
                }
            }else{
                AuthAssignment::deleteAll(['user_id' => yii::$app->request->get('uid')]);
            }
            if(isset($data['role'])){
                foreach ($data['role'] as $child) {
                    $model = new AuthAssignment();
                    $model->user_id = yii::$app->request->get('uid');
                    $model->item_name = $child;
                    if (AuthAssignment::find()->where(['item_name' => $model->item_name, 'user_id' => $model->user_id])->count()) continue;
                    $model->save();
                }
            }
            return $this->redirect([
                'user'
            ]);

        }
        $model->user_id = yii::$app->request->get('uid');
        return $this->render('assign', [
            'authItems' => $arr,
            'model' => $model
        ]);
    }

}