<?php

namespace backend\models;

class FileUsage extends \common\models\FileUsage
{

    public static function getFileUseCountByFid($fid)
    {
        return self::find()->where(['fid'=>$fid])->sum('count');
    }

}
