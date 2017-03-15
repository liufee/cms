<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property string $id
 * @property string $name
 * @property string $sort
 * @property string $created_at
 * @property string $updated_at
 * @property string $remark
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
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
            [['sort', 'parent_id', 'created_at', 'updated_at'], 'integer'],
            [['sort'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['name', 'remark'], 'string', 'max' => 255],
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
            'parent_id' => Yii::t('app', 'Category Id'),
            'name' => Yii::t('app', 'Name'),
            'sort' => Yii::t('app', 'Sort'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'remark' => Yii::t('app', 'Remark'),
        ];
    }

    public static function getAsArray($cur_id = 0, $level = 0)
    {
        $categories = self::find()->orderBy("sort asc,parent_id asc")->asArray()->all();
        $data = [];
        foreach ($categories as $key => $category) {
            if ($category['parent_id'] != $cur_id) {
                continue;
            }
            $category['level'] = $level;
            $data[] = $category;
            $data = array_merge($data, self::getAsArray($category['id'], $level + 1));
        }
        return $data;
    }

    public static function getOptions($id = '')
    {
        $categories = self::find()->all();
        if ($id) {
            $options = '<option value="0">' . yii::t('app', 'uncategoried') . '</option>';
        } else {
            $options = '<option selected value="0">' . yii::t('app', 'uncategoried') . '</option>';
        }
        foreach ($categories as $key => $category) {
            if ($category->parent_id != 0) {
                continue;
            }
            $selected = '';
            if ($id == $category->id) {
                $selected = ' selected ';
            }
            $options .= "<option {$selected}  value='{$category->id}'>{$category->name}</option>";
            unset($categories[$key]);
            $options .= self::_getOptionsSub($categories, $category->id, '&nbsp;&nbsp;&nbsp;&nbsp;', $id);
        }
        return $options;
    }

    private static function _getOptionsSub($categories, $cur_id, $tag, $id)
    {
        $options = '';
        foreach ($categories as $key => $category) {
            if ($category->parent_id != $cur_id) {
                continue;
            }
            $selected = '';
            if ($id == $category->id) {
                $selected = ' selected ';
            }
            $options .= "<option {$selected} value='{$category->id}'>{$tag}{$category->name}</option>";
            unset($categories[$key]);
            $options .= self::_getOptionsSub($categories, $category->id, $tag . $tag, $id);
        }
        return $options;
    }

    public static function getArray()
    {
        $obj = self::find()->orderBy("sort asc,parent_id asc")->all();
        $results = [];
        foreach ($obj as $key => $value) {
            foreach ($value as $k => $v) {
                $temp[$k] = $v;
            }
            $results[$key] = $temp;
        }
        $data = [];
        foreach ($results as $key => $result) {
            if ($result['parent_id'] != 0) {
                continue;
            }
            $result['level'] = 0;
            $data[$result['id']] = $result;
            unset($results[$key]);
            $temp = self::_getSubArray($results, $result['id'], 1);
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

    private static function _getSubArray($results, $cur_id, $level)
    {
        $return = '';
        foreach ($results as $key => $result) {
            if ($result['parent_id'] != $cur_id) {
                continue;
            }
            $result['level'] = $level;
            $return[] = $result;
            unset($results[$key]);
            $subMenu = self::_getSubArray($results, $result['id'], $level + 1);
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

    public static function getType()
    {
        $data = self::getAsArray();
        foreach ($data as &$v) {
            $tag = '';
            for ($i = 0; $i < $v['level']; $i++) {
                $tag .= '-';
            }
            $v['name'] = $tag . $v['name'];
        }
        $data = ArrayHelper::map($data, 'id', 'name');
        $data[0] = yii::t('app', 'uncategoried');
        return $data;
    }

    /*迭代获取家谱树 */
    public static function getSubTree($id, $level = 1)
    {
        $categories = self::find()->orderBy("sort asc,parent_id asc")->asArray()->all();
        $subs = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $id) {
                $category['level'] = $level;
                $subs[] = $category;
                $subs = array_merge($subs, self::getSubTree($categories, $category['id'], $level + 1));
            }
        }
        return $subs;
    }

    public static function getParentCategory()
    {
        $data = self::getArray();
        $newData = [];
        while (list($key, $val) = each($data)) {
            $newData[$val['id']] = str_repeat("---", $val['level']) . $val['name'];
        }
        return $newData;
    }

    public function beforeDelete()
    {
        $parent = self::getSubTree($this->id);
        if (! empty($parent)) {
            $this->addError('id', yii::t('app', 'Allowed not to be deleted, sub level exsited.'));
            return false;
        }
        if (Article::findOne(['cid' => $this->id]) != null) {
            $this->addError('id', yii::t('app', 'Allowed not to be deleted, some article belongs to this category.'));
            return false;
        }
        return parent::beforeDelete();
    }

    public function afterValidate()
    {
        if (! $this->getIsNewRecord() && $this->id == $this->parent_id) {
            $this->addError('parent_id', yii::t('app', 'Cannot be themself sub.'));
            return false;
        }
    }
}
