<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->params['breadcrumbs'] = [
    ['label' => yii::t('app', '<?=Inflector::camel2words(StringHelper::basename($generator->modelClass))?>'), 'url' => Url::to(['index'])],
    ['label' => yii::t('app', 'Update') . yii::t('app', '<?=Inflector::camel2words(StringHelper::basename($generator->modelClass))?>')],
];
?>
<?= "<?= " ?>$this->render('_form', [
    'model' => $model,
]) ?>
