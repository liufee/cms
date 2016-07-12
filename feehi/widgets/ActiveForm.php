<?php
namespace feehi\widgets;

use Yii;

class ActiveForm extends \yii\widgets\ActiveForm
{

    public $options = [
        'class' => 'form-horizontal'
    ];

    public $fieldClass = 'feehi\widgets\ActiveField';

    public function defaultButtons(array $options = [])
    {
        $options['size'] = isset($options['size']) ? $options['size'] : 4;
        echo '<div class="form-group">
                                <div class="col-sm-'.$options['size'].' col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">'.Yii::t('app', 'Save').'</button>
                                    <button class="btn btn-white" type="reset">'.Yii::t('app', 'Reset').'</button>
                                </div>
                            </div>';
    }
}
