<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-09 22:07
 */

namespace backend\controllers;

use backend\widgets\ueditor\UeditorAction;

class AssetsController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'ueditor' => [
                'class' => UeditorAction::className(),
            ],
        ];
    }

}