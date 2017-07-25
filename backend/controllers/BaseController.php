<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\Controller;

/**
 * Base controller is the whole backend controllers parent class, and supported basic operation(such as crud,sort...).
 */
class BaseController extends Controller
{

    /**
     * index列表页
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->render('index', $this->getIndexData());
    }

    /**
     * create创建页
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = $this->getModel();
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->validate() && $model->save()) {
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $model->loadDefaultValues();
        $array = array_merge(['model' => $model], $this->getCreateData());
        return $this->render('create', $array);
    }

    /**
     * delete删除
     *
     * @param $id
     * @return array|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDelete($id)
    {
        if (yii::$app->getRequest()->getIsAjax()) {//AJAX删除
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            if (! $id) {
                return ['code' => 1, 'message' => yii::t('app', "Id doesn't exit")];
            }
            $ids = explode(',', $id);
            $errorIds = [];
            $model = null;
            foreach ($ids as $one) {
                $model = $this->getModel($one);
                if ($model) {
                    if (! $result = $model->delete()) {
                        $errorIds[] = $one;
                    }
                }
            }
            if (count($errorIds) == 0) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0];
                }
                if ($err != '') {
                    $err = '.' . $err;
                }
                return ['code' => 1, 'message' => 'id ' . implode(',', $errorIds) . $err];
            }
        } else {
            if (! $id) {
                throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));
            }
            $model = $this->getModel($id);
            if ($model) {
                $model->delete();
            }
            return $this->redirect(yii::$app->request->headers['referer']);
        }
    }

    /**
     * update修改页
     *
     * @param $id
     * @return string|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        if (! $id) throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));

        $model = $this->getModel($id);
        if (! $model) throw new BadRequestHttpException(yii::t('app', "Cannot find model by $id"));

        if (Yii::$app->getRequest()->getIsPost()) {
            if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->save()) {
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['update', 'id' => $model->getPrimaryKey()]);
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 排序操作
     *
     */
    public function actionSort()
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $data = yii::$app->getRequest()->post();
            if (! empty($data['sort'])) {
                foreach ($data['sort'] as $key => $value) {
                    $model = $this->getModel($key);
                    if ($model->sort != $value) {
                        $model->sort = $value;
                        $model->save(false);
                    }
                }
            }
        }
        $this->redirect(['index']);
    }

    /**
     * 改变某个字段状态操作
     *
     * @param string $id
     * @param int $status
     * @param string $field
     * @return array|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionChangeStatus($id = '', $status = 0, $field = 'status')
    {
        if (yii::$app->getRequest()->getIsAjax()) {
            yii::$app->getResponse()->format = Response::FORMAT_JSON;
        }
        if (! $id) throw new BadRequestHttpException(yii::t('app', "Id doesn't exit"));

        $model = $this->getModel($id);
        if (! $model) throw new BadRequestHttpException(yii::t("app", "Cannot find model by $id"));

        $model->$field = $status;

        if (yii::$app->getRequest()->getIsAjax()) {
            if ($model->save(false)) {
                return ['code' => 0, 'message' => yii::t('app', 'Success')];
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                return ['code' => 1, 'message' => $err];
            }
        } else {
            $model->save();
            return $this->redirect(yii::$app->getRequest()->headers['referer']);
        }
    }

    /**
     * @param string $id
     * @return \yii\db\ActiveRecord
     */
    public function getModel($id = '')
    {
        return new ActiveRecord();
    }

    /**
     * @return array
     */
    public function getIndexData()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getCreateData()
    {
        return [];
    }

}
