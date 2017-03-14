<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@feehi', dirname(dirname(__DIR__)) . '/feehi');

Yii::setAlias('@uploads', '@frontend/web/uploads');//文件上传目录
Yii::setAlias('@article', '@uploads/article');
Yii::setAlias('@thumb', '@article/thumb');
Yii::setAlias('@ueditor', '@uploads/ueditor');
Yii::setAlias('@friendlylink', '@uploads/friendlylink');