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
/**
* This is the template for generating CRUD service class of the specified model.
*/

<?php if (!empty($generator->searchModelClass)): ?>
use <?=$generator->searchModelClass . ";\n"?>
<?php endif; ?>
use <?= $generator->modelClass . ";\n" ?>

class <?=$modelClass?>Service extends Service implements <?=$modelClass?>Interface{
    public function getSearchModel(array $query, array $options=[])
    {
        <?php if (!empty($generator->searchModelClass)){ ?> return new  <?=$searchModelClass?>();<?php }else { ?>return null;<?php } ?>

    }

    public function getModel($id, array $options = [])
    {
        return <?=$modelClass?>::findOne($id);
    }

    public function getNewModel(array $options = [])
    {
        $model = new <?=$modelClass?>();
        $model->loadDefaultValues();
        return $model;
    }
}
