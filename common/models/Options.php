<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;
use backend\models\form\AdForm;
use common\libs\Constants;
use common\helpers\FileDependencyHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%options}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property string $value
 * @property integer $input_type
 * @property string $tips
 * @property integer $autoload
 * @property integer $sort
 */
class Options extends \yii\db\ActiveRecord
{

    const TYPE_SYSTEM = 0;
    const TYPE_CUSTOM = 1;
    const TYPE_BANNER = 2;
    const TYPE_AD = 3;

    const CUSTOM_AUTOLOAD_NO = 0;
    const CUSTOM_AUTOLOAD_YES = 1;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%options}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'input_type', 'autoload', 'sort'], 'integer'],
            [['name', 'input_type', 'autoload'], 'required'],
            [['name'], 'unique'],
            [
                ['name'],
                'match',
                'pattern' => '/^[a-zA-Z][0-9_]*/',
                'message' => Yii::t('app', 'Must begin with alphabet and can only includes alphabet,_,and number')
            ],
            [['value'], 'string'],
            [['value'], 'default', 'value' => ''],
            [['name', 'tips'], 'string', 'max' => 255],
            [['sort'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'value' => Yii::t('app', 'Value'),
            'input_type' => Yii::t('app', 'Input Type'),
            'tips' => Yii::t('app', 'Tips'),
            'autoload' => Yii::t('app', 'Autoload'),
            'sort' => Yii::t('app', 'Sort'),
        ];
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return array_keys($this->attributeLabels());
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $object = Yii::createObject([
            'class' => FileDependencyHelper::className(),
            'fileName' => 'options.txt',
        ]);
        $object->updateFile();
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
    {
        if( !$insert ){
            if( $this->input_type == Constants::INPUT_IMG ) {
                $temp = explode('\\', self::className());
                $modelName = end( $temp );
                $key = "{$modelName}[{$this->id}][value]";
                $upload = UploadedFile::getInstanceByName($key);
                $old = Options::findOne($this->id);
                /* @var $cdn \feehi\cdn\TargetInterface */
                $cdn = Yii::$app->get('cdn');
                if($upload !== null){
                    $uploadPath = Yii::getAlias('@uploads/setting/custom-setting/');
                    if (! FileHelper::createDirectory($uploadPath)) {
                        $this->addError($key, "Create directory failed " . $uploadPath);
                        return false;
                    }
                    $fullName = $uploadPath . date('YmdHis') . '_' . uniqid() . '.' . $upload->getExtension();
                    if (! $upload->saveAs($fullName)) {
                        $this->addError($key, Yii::t('app', 'Upload {attribute} error: ' . $upload->error, ['attribute' => Yii::t('app', 'Picture')]) . ': ' . $fullName);
                        return false;
                    }
                    $this->value = str_replace(Yii::getAlias('@frontend/web'), '', $fullName);
                    $cdn->upload($fullName, $this->value);
                    if( $old !== null ){
                        $file = Yii::getAlias('@frontend/web') . $old->value;
                        if( file_exists($file) && is_file($file) ) unlink($file);
                        if( $cdn->exists($old->value) ) $cdn->delete($old->value);
                    }
                }else{
                    if( $this->value !== '' ){
                        $file = Yii::getAlias('@frontend/web') . $old->value;
                        if( file_exists($file) && is_file($file) ) unlink($file);
                        if( $cdn->exists($old->value) ) $cdn->delete($old->value);
                        $this->value = '';
                    }else {
                        $this->value = $old->value;
                    }
                }
            }
        }
        return parent::beforeSave($insert);
    }

    public static function getBannersByType($name)
    {
        $model = Options::findOne(['type'=>self::TYPE_BANNER, 'name'=>$name, 'autoload'=>Constants::Status_Enable]);
        if( $model == null ) throw new NotFoundHttpException("None banner type named $name");
        if( $model->value == '' ) $model->value = '[]';
        $banners = json_decode($model->value, true);
        ArrayHelper::multisort($banners, 'sort');
        /** @var $cdn \feehi\cdn\TargetInterface */
        $cdn = Yii::$app->get('cdn');
        foreach ($banners as $k => &$banner){
            if( $banner['status'] == Constants::Status_Desable ) unset($banners[$k]);
            $banner['img'] = $cdn->getCdnUrl($banner['img']);
        }
        return $banners;
    }

    public static function getAdByName($name)
    {
        $ad = AdForm::findOne(['type'=>self::TYPE_AD, 'name'=>$name]);
        $ad === null && $ad = Yii::createObject( AdForm::className() );
        return $ad;
    }

}
