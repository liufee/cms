<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-31 17:07
 */
use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', 'Admin Users'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Update') . yii::t('app', 'Admin Users')],
];
/**
 * @var $model backend\models\User
 */
?>
<?php
if (yii::$app->controller->action->id == 'update') {
    echo $this->render('_form', [
        'model' => $model,
    ]);
} else {
    echo $this->render('_form-update-self', [
        'model' => $model
    ]);
}
?>
