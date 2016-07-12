<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/23
 * Time: 12:08
 */
namespace backend\controllers;

use feehi\libs\Constants;
use Yii;
use backend\models\SettingWebsiteForm;
use backend\models\SettingSeoForm;
use backend\models\SettingCustomForm;
use common\models\Options;
use yii\base\Model;

/**
 * Setting controller
 */
class SettingController extends BaseController
{

    public function actionWebsite()
    {
        $model = new SettingWebsiteForm();
        if ( Yii::$app->request->isPost )
        {
            if( $model->validate() && $model->load(Yii::$app->request->post()) && $model->setWebsiteConfig() ){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            }else{
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Error'));
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
            //return $this->refresh();
        }

        $model->getWebsiteSetting();
        return $this->render('website', [
            'model'=>$model
        ]);

    }

    public function actionSeo()
    {
        $model = new SettingSeoForm();
        if (Yii::$app->request->isPost){
            if($model->validate() && $model->load(Yii::$app->request->post()) && $model->setSeoConfig()){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            }else{
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Error'));
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }

        }

        $model->getSeoSetting();
        return $this->render('seo', [
            'model'=>$model
        ]);
    }

    public function actionCustom()
    {
        $settings = Options::find()->where(['type'=>Options::TYPE_CUSTOM])->indexBy('id')->all();

        if (Model::loadMultiple($settings, Yii::$app->request->post()) && Model::validateMultiple($settings)) {
            foreach ($settings as $setting) {
                $setting->save(false);
            }
            Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        }

        return $this->render('custom', [
            'settings'=>$settings,
            'model' => new Options(),
        ]);
    }

    public function actionCustomCreate()
    {
        $model = new Options();
        $model->type = Options::TYPE_CUSTOM;
        if($model->load(yii::$app->request->post()) && $model->save()){
            Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            return $this->redirect(['custom']);
        }else{
            $errors = $model->getErrors();
            $err = '';
            foreach($errors as $v){
                $err .= $v[0].'<br>';
            }
            //Yii::$app->getSession()->setFlash('error', yii::t('app', $err));
            yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'err_msg' => $err,
            ];
        }
    }

    public function actionCustomDelete($id='')
    {
        Options::findOne(['id'=>$id])->delete();
        return $this->redirect(['custom']);
    }

    public function actionCustomUpdate($id='')
    {
        $model = Options::findOne(['id' => $id]);
        if(yii::$app->request->isPost){
            if($model->load(yii::$app->request->post()) && $model->save()){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['custom']);
            }else{
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                //Yii::$app->getSession()->setFlash('error', yii::t('app', $err));
                yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'err_msg' => $err,
                ];
            }
        }else {
            echo '<div class="" id="editForm">';
            echo '<div class="ibox-content">';
            $form = \feehi\widgets\ActiveForm::begin(['options' => ['name' => 'edit']]);
            echo $form->field($model, 'name')->textInput();
            echo $form->field($model, 'input_type')->dropDownList(\feehi\libs\Constants::getInputTypeItems());
            echo $form->field($model, 'tips')->textInput();
            echo $form->field($model, 'autoload')->dropDownList(Constants::getYesNoItems());
            echo $form->field($model, 'sort')->textInput();
            echo $form->defaultButtons();
            \feehi\widgets\ActiveForm::end();
            echo '</div>';
            echo '</div>';
        }
    }

}
