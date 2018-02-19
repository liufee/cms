<?php
/**
 * This is the FeehiCMS backend template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
<?php if (!empty($generator->searchModelClass)): ?>
use <?=$generator->searchModelClass . ";\n"?>
<?php endif; ?>
use <?= $generator->modelClass . ";\n" ?>
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
<?php if (empty($generator->searchModelClass)){ ?>
use yii\data\ActiveDataProvider;
<?php } ?>
/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends \yii\web\<?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    <?php if (!empty($generator->searchModelClass)): ?>

                        $searchModel = new <?=StringHelper::basename($generator->searchModelClass)?>();
                        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    <?php else: ?>

                        $dataProvider = new ActiveDataProvider([
                            'query' => <?= $modelClass ?>::find(),
                        ]);

                        return [
                            'dataProvider' => $dataProvider,
                        ];
                    <?php endif; ?>

                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => <?= $modelClass ?>::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => <?= $modelClass ?>::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => <?= $modelClass ?>::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => <?= $modelClass ?>::className(),
            ],
        ];
    }
}
