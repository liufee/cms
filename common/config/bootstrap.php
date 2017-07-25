<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@feehi', dirname(dirname(__DIR__)) . '/feehi');

Yii::setAlias('@uploads', '@frontend/web/uploads');//文件上传目录
Yii::setAlias('@article', '@uploads/article');//文章相关资源上传目录
Yii::setAlias('@thumb', '@article/thumb');//文章缩略图上传目录
Yii::setAlias('@ueditor', '@uploads/ueditor');//文章ueditor编辑器资源上传目录
Yii::setAlias('@friendlylink', '@uploads/friendlylink');//友情链接图片上传目录