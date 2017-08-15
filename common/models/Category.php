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
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;;

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
            [['name', 'alias', 'remark'], 'string', 'max' => 255],
            [['name', 'alias'], 'required'],
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
            'alias' => Yii::t('app', 'Alias'),
            'sort' => Yii::t('app', 'Sort'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'remark' => Yii::t('app', 'Remark'),
        ];
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    protected static function _getCategories()
    {
        return self::find()->orderBy("sort asc,parent_id asc")->asArray()->all();
    }

    /**
     * @return array
     */
    public static function getCategories()
    {
        $categories = self::_getCategories();
        $familyTree = new FamilyTree($categories);
        $array = $familyTree->getDescendants(0);
        return ArrayHelper::index($array, 'id');
    }

    /**
     * @return array
     */
    public static function getCategoriesName()
    {
        $categories = self::getCategories();
        $data = [];
        foreach ($categories as $v){
            $data[$v['id']] = str_repeat('--', $v['level']) . $v['name'];
        }
        return $data;
    }

    /**
     * @param $id
     * @return array
     */
    public static function getDescendants($id)
    {
        $familyTree = new FamilyTree(self::_getCategories());
        return $familyTree->getDescendants($id);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $categories = self::find()->orderBy("sort asc,parent_id asc")->asArray()->all();
        $familyTree = new FamilyTree( $categories );
        $subs = $familyTree->getDescendants($this->id);
        if (! empty($subs)) {
            $this->addError('id', yii::t('app', 'Allowed not to be deleted, sub level exsited.'));
            return false;
        }
        if (Article::findOne(['cid' => $this->id]) != null) {
            $this->addError('id', yii::t('app', 'Allowed not to be deleted, some article belongs to this category.'));
            return false;
        }
        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if (! $this->getIsNewRecord() ) {
            if( $this->id == $this->parent_id ) {
                $this->addError('parent_id', yii::t('app', 'Cannot be themself sub.'));
                return false;
            }
            $familyTree = new FamilyTree(self::_getCategories());
            $descendants = $familyTree->getDescendants($this->id);
            $descendants = array_column($descendants, 'id');
            if( in_array($this->parent_id, $descendants) ){
                $this->addError('parent_id', yii::t('app', 'Cannot be themselves descendants sub'));
                return false;
            }
        }
    }

}
