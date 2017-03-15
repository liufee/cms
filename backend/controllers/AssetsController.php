<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-09 22:07
 */

namespace backend\controllers;

use common\models\File as FileModel;

class AssetsController extends \yii\web\Controller
{


    public function actions()
    {
        return [
            'ueditor' => [
                'class' => 'backend\widgets\ueditor\UeditorAction',
            ],
        ];
    }

}