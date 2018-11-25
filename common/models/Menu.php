<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;
use common\helpers\FamilyTree;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property string $id
 * @property int $type
 * @property string $parent_id
 * @property string $name
 * @property string $url
 * @property string $icon
 * @property string $sort
 * @property integer $target
 * @property integer is_absolute_url
 * @property string $is_display
 * @property string $created_at
 * @property string $updated_at
 */
class Menu extends \yii\db\ActiveRecord
{
    const BACKEND_TYPE = 0;
    const FRONTEND_TYPE = 1;

    const DISPLAY_YES = 1;
    const DISPLAY_NO = 0;

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'afterValidateEvent']);
        $this->on(self::EVENT_BEFORE_DELETE, [$this, 'beforeDeleteEvent']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['sort'], 'number'],
            [['parent_id', 'sort'], 'default', 'value' => 0],
            [['sort'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['is_display'], 'integer'],
            [['name', 'url', 'icon'], 'string', 'max' => 255],
            [['type', 'is_absolute_url'], 'in', 'range' => [0, 1]],
            [['target'], 'in', 'range' => ['_blank', '_self']],
            [['name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'backend' => [
                'parent_id',
                'name',
                'url',
                'icon',
                'type',
                'is_absolute_url',
                'target',
                'sort',
                'is_display',
                'created_at',
                'updated_at',
            ],
            'frontend' => [
                'parent_id',
                'name',
                'url',
                'icon',
                'type',
                'is_absolute_url',
                'target',
                'sort',
                'is_display',
                'created_at',
                'updated_at',
            ],
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
            'parent_id' => Yii::t('app', 'Parent Id'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'icon' => Yii::t('app', 'Icon'),
            'sort' => Yii::t('app', 'Sort'),
            'is_absolute_url' => Yii::t('app', 'Is Absolute Url'),
            'target' => Yii::t('app', 'Target'),
            'is_display' => Yii::t('app', 'Is Display'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @param $type
     * @return array|\yii\db\ActiveRecord[]
     */
    protected  static function _getMenus($type)
    {
        static $menus = null;
        if( $menus === null ) $menus = self::find()->where(['type' => $type])->orderBy("sort asc,parent_id asc")->asArray()->all();
        foreach ($menus as &$menu){
            $menu['name'] = Yii::t('menu', $menu['name']);
        }
        return $menus;
    }

    /**
     * @param int $type
     * @return array
     */
    public static function getMenus($type=self::BACKEND_TYPE)
    {
        $menus = self::_getMenus($type);
        $familyTree = new FamilyTree($menus);
        $array = $familyTree->getDescendants(0);
        foreach ($array as $k => &$menu){
            if( isset($menus[$k+1]['level']) && $menus[$k+1]['level'] == $menu['level'] ){
                $name = ' ├' . $menu['name'];
            }else{
                $name = ' └' . $menu['name'];
            }
            if( end($menus) == $menu ){
                $sign = ' └';
            }else{
                $sign = ' │';
            }
            $menu['treename'] = str_repeat($sign, $menu['level']-1) . $name;
        }
        return ArrayHelper::index($array, 'id');
    }

    /**
     * @param int $type
     * @return array
     */
    public static function getMenusName($type=self::BACKEND_TYPE)
    {

        $menus = self::getMenus($type);
        $menus = ArrayHelper::getColumn($menus, 'treename');
        return $menus;
    }

    /**
     * @inheritdoc
     */
    public function afterValidateEvent($event)
    {
        if( !$event->sender->getIsNewRecord() ){
            if($event->sender->id == $event->sender->parent_id) {
                $event->sender->addError('parent_id', Yii::t('app', 'Cannot be themselves sub'));
                return false;
            }
            $familyTree = new FamilyTree(Menu::_getMenus($event->sender->type));
            $descendants = $familyTree->getDescendants($event->sender->id);
            $descendants = ArrayHelper::getColumn($descendants, 'id');
            if( in_array($event->sender->parent_id, $descendants) ){
                $event->sender->addError('parent_id', Yii::t('app', 'Cannot be themselves descendants sub'));
                return false;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeDeleteEvent($event)
    {
        $menus = Menu::_getMenus($event->sender->type);
        $familyTree = new FamilyTree( $menus );
        $subs = $familyTree->getDescendants($event->sender->id);
        if (! empty($subs)) {
            $event->sender->addError('id', Yii::t('app', 'Sub Menu exists, cannot be deleted'));
            $event->isValid = false;
        }
    }

    public function getParent()
    {
        return $this->hasOne(self::className(), ['id'=>'parent_id']);
    }

    public function beforeSave($insert)
    {
        if( !$this->is_absolute_url ) {
            if( strlen($this->url) > 0 ){
                $firstCharacter = $this->url[0];
                if( in_array($firstCharacter, ['[', '{']) ){
                    $temp = @json_decode($this->url, true);
                    if( $temp === null ){
                        $this->addError("url", Yii::t('app', 'Url is not a correct json format'));
                        return false;
                    }
                }
            }
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getMenuUrl()
    {
        $url = "";
        if( !$this->is_absolute_url ) {
            if( strlen($this->url) > 0 ){
                $firstCharacter = $this->url[0];
                if( in_array($firstCharacter, ['[', '{']) ){
                    $temp = @json_decode($this->url, true);
                    if( $temp === null ){
                        Yii::error("app", "Menu id ({$this->id}) url is incorrect json format");
                    }
                    $url = Url::to($temp);
                }else{
                    $url = Url::to([$this->url]);
                }
            }
        }else{
            $url = $this->url;
        }
        return $url;
    }
}
