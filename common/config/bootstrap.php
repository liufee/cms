<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');

Yii::setAlias('@uploads', '@frontend/web/uploads');//files upload directory

Yii::setAlias('@article', '@uploads/article');//article related files upload directory
Yii::setAlias('@thumb', '@article/thumb');//
Yii::setAlias('@ueditor', '@uploads/ueditor');//rich text editor ueditor files upload directory
Yii::setAlias('@friendlylink', '@uploads/friendlylink');//friendly link related files upload directory