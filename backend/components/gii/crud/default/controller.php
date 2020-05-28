<?php
/**
 * This is the FeehiCMS backend template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\Inflector;
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
use common\services\<?= $modelClass ?>ServiceInterface;
use common\services\<?= $modelClass ?>Service;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;
<?php if (empty($generator->searchModelClass)){ ?>
use yii\data\ActiveDataProvider;
<?php } ?>
<?php $category = Yii::t("app", Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))); ?>
<?php
    $idSign = "";
    $closureIdParam = "";
    if( !empty($pks) ) {
        $idSign = "                'primaryKeyIdentity' => ";
        if (count($pks) === 1) {
            if ($pks[0] !== "id") {
                $idSign .= "'" . $pks[0] . "',\n";
                $closureIdParam = '$' . $pks[0];
            }else{
                $idSign = "";
                $closureIdParam = '$id';
            }
        } else {
            $idSign .= "[";
            $i = 0;
            foreach ($pks as $key) {
                $idSign .= "'" . $key . "',";
                if($i > 0){
                    $closureIdParam .= ' $' . $key . ",";
                }else{
                    $closureIdParam .= '$' . $key . ",";
                }

                $i++;
            }
            $idSign = rtrim($idSign, ",");
            $idSign .= "],\n";
            $closureIdParam = rtrim($closureIdParam, ",");
        }
    }
?>
/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends \yii\web\<?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    /**
    * @auth
    * - item group=未分类 category=<?= $category?> description-get=列表 sort=000 method=get
    * - item group=未分类 category=<?= $category?> description=创建 sort-get=001 sort-post=002 method=get,post  
    * - item group=未分类 category=<?= $category?> description=修改 sort=003 sort-post=004 method=get,post  
    * - item group=未分类 category=<?= $category?> description-post=删除 sort=005 method=post  
    * - item group=未分类 category=<?= $category?> description-post=排序 sort=006 method=post  
    * - item group=未分类 category=<?= $category?> description-get=查看 sort=007 method=get  
    * @return array
    */
    public function actions()
    {
        /** @var <?=$modelClass?>ServiceInterface $service */
        $service = Yii::$app->get(<?=$modelClass?>ServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function($query, $indexAction) use($service){
                    $result = $service->getList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        <?php if( !empty($generator->searchModelClass) ) { ?>'searchModel' => $result['searchModel'],<?php } ?>
                    ];
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'doCreate' => function($postData, $createAction) use($service){
                    return $service->create($postData);
                },
                'data' => function($createResultModel, $createAction) use($service){
                    $model = $createResultModel === null ? $service->newModel() : $createResultModel;
                    return [
                        'model' => $model,
                    ];
                }
            ],
            'update' => [
                'class' => UpdateAction::className(),
<?php if(!empty($idSign)){echo $idSign;} ?>
                'doUpdate' => function(<?php if(!empty($closureIdParam)){echo $closureIdParam;echo ", ";}?>$postData, $updateAction) use($service){
                    return $service->update(<?=$closureIdParam?>, $postData);
                },
                'data' => function(<?php if(!empty($closureIdParam)){echo $closureIdParam;echo ", ";}?>$updateResultModel, $updateAction) use($service){
                    $model = $updateResultModel === null ? $service->getDetail(<?=$closureIdParam?>) : $updateResultModel;
                    return [
                        'model' => $model,
                    ];
                }
            ],
            'delete' => [
                'class' => DeleteAction::className(),
<?php if(!empty($idSign)){echo $idSign;} ?>
                'doDelete' => function(<?php if(!empty($closureIdParam)){echo $closureIdParam;echo ", ";}?>$deleteAction) use($service){
                    return $service->delete(<?=$closureIdParam?>);
                },
            ],
            'sort' => [
                'class' => SortAction::className(),
                'doSort' => function($id, $sort, $sortAction) use($service){
                    return $service->sort($id, $sort);
                },
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
<?php if(!empty($idSign)){echo $idSign;} ?>
                'data' => function(<?php if(!empty($closureIdParam)){echo $closureIdParam;echo ", ";}?>$viewAction) use($service){
                    return [
                        'model' => $service->getDetail(<?=$closureIdParam?>),
                    ];
                },
            ],
        ];
    }
}
