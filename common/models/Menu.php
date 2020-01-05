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
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property string $id menu id
 * @property int $type menu type, frontend or backend
 * @property string $parent_id menu parent_id, if equals 0 represent first level menu
 * @property string $name menu name
 * @property string $url menu url, can be controller/action such as "site/index" or absolute uri like "https://www.feehi.com/tools/ip"
 * @property string $icon menu icon
 * @property string $sort menu sort
 * @property string $target html a tag target attribute, such as "_self" or "_blank"
 * @property integer is_absolute_url if 0, url will use the origin sample;if 1 url will be generate by Url::to(url)
 * @property integer $is_display if hidden this menu
 * @property string $created_at
 * @property string $updated_at
 */

class Menu extends \yii\db\ActiveRecord
{
    /** @var int $level menu level */
    private $level = null;

    /** @var int backend type menu */
    const TYPE_BACKEND = 0;
    /** @var int frontend type menu */
    const TYPE_FRONTEND = 1;

    /** @var int if menu hidden */
    const DISPLAY_YES = 1;
    const DISPLAY_NO = 0;

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'afterValidateEvent']);
        $this->on(self::EVENT_BEFORE_DELETE, [$this, 'beforeDeleteEvent']);
    }

    /**
     * get menu table name
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
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
            [['sort'], 'integer'],
            [['parent_id', 'sort'], 'default', 'value' => 0],
            [['sort'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['is_display'], 'integer'],
            [['name', 'url', 'icon'], 'string', 'max' => 255],
            [['type', 'is_absolute_url'], 'in', 'range' => [0, 1]],
            [['target'], 'in', 'range' => ['_blank', '_self', '_parent', '_top']],
            [['name'], 'required'],
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

    public function beforeSave($insert)
    {
        if (!$this->is_absolute_url) {
            $result = $this->convertRelativeUrlToJSONString();
            if (!$result) {echo 111;exit;
                return false;
            }
        }
        return parent::beforeSave($insert);
    }

    public function getLevel(){
        return $this->level;
    }

    public function setLevel($level){
        $this->level = $level;
    }

    /**
     * get menu url
     *
     * @return string
     * @throws Exception
     */
    public function getMenuUrl()
    {
        if ($this->is_absolute_url) {
            return $this->url;
        }
        $urlComponents = json_decode($this->url, true);
        if ($urlComponents === null) {
            //compatible old cms version
            $urlComponents[0] = $this->url;
        }
        return Url::to($urlComponents);
    }

    /**
     * convert relative url to json string
     * relative url format should like "/controller/action?p1=v1&p2=v2#fragment"
     * @return bool
     * @var string $urlDComponents will be encode to a json string for storage. when decode this json string can pass to Url::to($urlComponents) generate uri
     *
     */
    private function convertRelativeUrlToJSONString()
    {
        $urlComponents = [$this->url];
        if (strlen($this->url) > 0) {
            if (strpos($this->url, "/") !== 0) {
                $this->url = "/" . $this->url;
            }
            $urlComponents = [];
            $parsedUrl = parse_url($this->url);
            if (!isset($parsedUrl["path"]) || $parsedUrl["path"] === "") {
                $this->addError("url", Yii::t('app', 'Url is not a correct format. It should be like controller/action/?p1=v1&p2=v2'));
                return false;
            }
            $urlComponents[0] = $parsedUrl["path"];
            if (isset($parsedUrl["query"]) && $parsedUrl["query"] !== "") {
                parse_str($parsedUrl["query"], $query);
                if (!empty($query)) {
                    $urlComponents = array_merge($urlComponents, $query);
                }
            }

            if (isset($parsedUrl["fragment"]) && $parsedUrl["fragment"] !== "") {
                $urlComponents["#"] = $parsedUrl["fragment"];
            }
        }
        $this->url = json_encode($urlComponents);
        if ($this->url === false) {
            $this->addError("url", Yii::t('app', 'Url is not a correct format. convert to json error. url components ' . print_r($urlComponents, true)));
            return false;
        }
        return true;
    }

    /**
     * convert json string to relative url
     * when edit this menu, should convert json string to the origin format for admin user edit
     *
     */
    public function convertJSONStringToRelativeUrl()
    {
        $urlComponents = json_decode($this->url, true);
        if( $urlComponents === null ){//compatible old version that stored not json format
            return $this->url;
        }
        $url = "";
        if (isset($urlComponents[0])) {
            $url .= $urlComponents[0];
            unset($urlComponents[0]);
        }
        $fragment = "";
        if (isset($urlComponents["#"])) {
            $fragment = "#" . $urlComponents["#"];
            unset($urlComponents["#"]);
        }
        if (!empty($urlComponents)) {
            $url .= "?" . urldecode(http_build_query($urlComponents)) . $fragment;
        }
        return $url;
    }

    /**
     * @param $type
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function _getMenus($type)
    {
        static $menus = null;
        if ($menus === null) $menus = self::find()->where(['type' => $type])->orderBy("sort asc,parent_id asc")->all();
        foreach ($menus as &$menu) {
            $menu['name'] = Yii::t('menu', $menu['name']);
        }
        return $menus;
    }

    /**
     * get menus with menu name has prefix level characters
     *
     * @param int $type
     * @return array
     */
    public static function getMenusWithNameHasPrefixLevelCharacters($type = self::TYPE_BACKEND)
    {
        $menus = self::_getMenus($type);
        $familyTree = new FamilyTree($menus);
        $array = $familyTree->getDescendants(0);
        foreach ($array as $k => $menu) {
            /** @var Menu $menu */
            if (isset($menus[$k + 1]['level']) && $menus[$k + 1]['level'] == $menu['level']) {
                $name = ' ├' . $menu['name'];
            } else {
                $name = ' └' . $menu['name'];
            }
            if (end($menus)->id == $menu->id) {
                $sign = ' └';
            } else {
                $sign = ' │';
            }
            $menu->name = str_repeat($sign, $menu['level'] - 1) . $name;
        }
        return ArrayHelper::index($array, 'id');
    }

    /**
     * get menu names
     *
     * @param int $type
     * @return array
     */
    public static function getMenusName($type = self::TYPE_BACKEND)
    {
        $menus = self::_getMenus($type);
        $familyTree = new FamilyTree($menus);
        $array = $familyTree->getDescendants(0);
        return array_column($array, "name");
    }

    /**
     * validate
     *
     * @param $event
     * @return bool
     */
    public function afterValidateEvent($event)
    {
        if (!$event->sender->getIsNewRecord()) {//if not create a new menu
            if ($event->sender->id == $event->sender->parent_id) {//cannot set menu to its own sub menu
                $event->sender->addError('parent_id', Yii::t('app', 'Cannot be themselves sub'));
                return false;
            }
            $familyTree = new FamilyTree(Menu::_getMenus($event->sender->type));
            $descendants = $familyTree->getDescendants($event->sender->id);
            $descendants = ArrayHelper::getColumn($descendants, 'id');
            if (in_array($event->sender->parent_id, $descendants)) {//cannot set menu to its own descendants sub menu
                $event->sender->addError('parent_id', Yii::t('app', 'Cannot be themselves descendants sub'));
                return false;
            }
        }
    }

    /**
     * check menu can be delete
     *
     * @param $event
     */
    public function beforeDeleteEvent($event)
    {
        $menus = Menu::_getMenus($event->sender->type);
        $familyTree = new FamilyTree($menus);
        $subs = $familyTree->getDescendants($event->sender->id);
        if (!empty($subs)) {
            $event->sender->addError('id', Yii::t('app', 'Sub Menu exists, cannot be deleted'));
            $event->isValid = false;
        }
    }

    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

}
