<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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
 * @property enum $target
 * @property enum is_absolute_url
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
            [['name', 'url', 'icon', 'method'], 'string', 'max' => 255],
            [['type', 'is_absolute_url'], 'in', 'range' => [0, 1]],
            [['target'], 'in', 'range' => ['_blank', '_self']],
            [['name'], 'required'],
            [['method'], 'required', 'on' => ['backend']],
        ];
    }

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
                'method'
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
                'is_display'
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
            'method' => Yii::t('app', 'HTTP Method'),
            'icon' => Yii::t('app', 'Icon'),
            'sort' => Yii::t('app', 'Sort'),
            'is_absolute_url' => Yii::t('app', 'Is Absolute Url'),
            'target' => Yii::t('app', 'Target'),
            'is_display' => Yii::t('app', 'Is Display'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public static function getMenuArray($type)
    {
        $model = new self();
        $obj = $model->find()->where(['type' => $type])->orderBy("sort asc,parent_id asc")->all();
        $menus = [];
        foreach ($obj as $key => $value) {
            foreach ($value as $k => $v) {
                $temp[$k] = $v;
            }
            $menus[$key] = $temp;
        }
        $data = [];
        foreach ($menus as $key => $menu) {
            if ($menu['parent_id'] != 0) {
                continue;
            }
            $menu['level'] = 0;
            $menu['name'] = $menu['name'];
            $data[$menu['id']] = $menu;
            unset($menus[$key]);
            $temp = self::_getSubMenuArray($menus, $menu['id'], 1);
            if (is_array($temp)) {
                foreach ($temp as $v) {
                    if (! is_array($v)) {
                        continue;
                    }
                    $data[$v['id']] = $v;
                }
            }
        }
        return $data;
    }

    private static function _getSubMenuArray($menus, $cur_id, $level)
    {
        $return = [];
        foreach ($menus as $key => $menu) {
            if ($menu['parent_id'] != $cur_id) {
                continue;
            }
            $menu['level'] = $level;
            $menu['name'] = $menu['name'];
            $return[] = $menu;
            unset($menus[$key]);
            $subMenu = self::_getSubMenuArray($menus, $menu['id'], $level + 1);
            if (is_array($subMenu)) {
                foreach ($subMenu as $val) {
                    if (! is_array($val)) {
                        continue;
                    }
                    $return[] = $val;
                }
            }
        }
        return $return;
    }

    public static function getParentMenu($type)
    {
        $menus = self::getMenuArray($type);
        $newMenu = [];//var_dump($menus);die;
        while (list($key, $val) = each($menus)) {
            $newMenu[$val['id']] = str_repeat("---", $val['level']) . yii::t('menu', $val['name']);;
        }
        return $newMenu;
    }

    public static function getDescendants($id, $type, $level = 1)
    {
        $nodes = [];
        $menus = Menu::getMenuArray($type);
        foreach ($menus as $key => $value) {
            if ($value['parent_id'] == $id) {
                $value['level'] = $level;
                $nodes[] = $value;
                $nodes = array_merge($nodes, self::getDescendants($value['id'], $type, $level + 1));
            }
        }
        return $nodes;
    }

    public function afterValidate()
    {
        if (! $this->getIsNewRecord() && $this->id == $this->parent_id) {
            $this->addError('parent_id', yii::t('app', 'Cannot be themself sub.'));
            return false;
        }
    }

    public function beforeDelete()
    {
        $children = self::getDescendants($this->id, $this->type);
        if (! empty($children)) {
            throw new \yii\web\ForbiddenHttpException(yii::t('app', 'Sub Menu exists, cannot be deleted'));
        }
        return true;
    }
}
