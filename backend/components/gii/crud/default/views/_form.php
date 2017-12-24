<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use backend\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form backend\widgets\ActiveForm */
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?="<?= \$this->render('/widgets/_ibox-title') ?>\n"?>
            <div class="ibox-content">
                <?="<?php \$form = ActiveForm::begin([
                    'options' => [
                        'class' => 'form-horizontal'
                    ]
                ]); ?>\n"?>
                <div class="hr-line-dashed"></div>
                <?php foreach ($generator->getColumnNames() as $attribute) {
                    static $i = 0;
                    if (in_array($attribute, $safeAttributes)) {
                        if($i==0){
                            echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n";
                            $i++;
                        }else{
                            echo "                        <?= " . $generator->generateActiveField($attribute) . " ?>\n";
                        }
                        echo '                        <div class="hr-line-dashed"></div>' . "\n\n";
                    }
                } ?>
                <?="        <?= \$form->defaultButtons() ?>\n"?>
                <?="    <?php ActiveForm::end(); ?>\n";?>
            </div>
        </div>
    </div>
</div>