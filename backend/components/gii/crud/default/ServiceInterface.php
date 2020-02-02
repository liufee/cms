<?php
use yii\helpers\StringHelper;

/* @var $generator yii\gii\generators\crud\Generator */
/* @var $this yii\web\View */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

echo "<?php\n";
?>
namespace common\services;

interface <?=$modelClass?>Interface extends \common\services\ServiceInterface
{
    const ServiceName = "<?=$modelClass?>Service";
}