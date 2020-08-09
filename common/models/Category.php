<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;
use common\libs\FamilyTree;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;


/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $alias
 * @property integer $sort
 * @property string $template
 * @property string $article_template
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 */
class Category extends \yii\db\ActiveRecord
{

    use FamilyTree;

    public $level;

    public $prefix_level_name;

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
            [['parent_id'], 'default', 'value' => 0],
            [['name', 'alias', 'remark', 'template', 'article_template'], 'string', 'max' => 255],
            [['alias'],  'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => Yii::t('app', 'Must begin with alphabet and can only includes alphabet,_,and number')],
            [['name', 'alias'], 'required'],
            [['sort'], 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent Category Id'),
            'name' => Yii::t('app', 'Name'),
            'alias' => Yii::t('app', 'Alias'),
            'sort' => Yii::t('app', 'Sort'),
            'template' => Yii::t('app', 'Category Template'),
            'article_template' => Yii::t('app', 'Article Template'),
            'remark' => Yii::t('app', 'Remark'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getItems()
    {
        return self::_getCategories();
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    protected static function _getCategories()
    {
        return self::find()->orderBy(['sort'=>SORT_ASC, "parent_id"=>SORT_ASC])->all();
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $subs = $this->getDescendants($this->id);
        if (! empty($subs)) {
            $this->addError('id', Yii::t('app', 'Allowed not to be deleted, sub level existed.'));
            return false;
        }
        if (Article::findOne(['cid' => $this->id]) != null) {
            $this->addError('id', Yii::t('app', 'Allowed not to be deleted, some article belongs to this category.'));
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
                $this->addError('parent_id', Yii::t('app', 'Cannot be themselves sub'));
                return false;
            }
            $descendants = $this->getDescendants($this->id);
            $descendants = ArrayHelper::getColumn($descendants, 'id');
            if( in_array($this->parent_id, $descendants) ){
                $this->addError('parent_id', Yii::t('app', 'Cannot be themselves descendants sub'));
                return false;
            }
        }
        parent::afterValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        self::_generateUrlRules();
        parent::afterSave($insert, $changedAttributes);
    }

    private function _generateUrlRules()
    {
        $categories = self::_getCategories();
        $data = [];
        foreach ($categories as $v){
            $parents = $this->getAncestors($v['id']);
            $url = '';
            if(!empty($parents)){
                $parents = array_reverse($parents);
                foreach ($parents as $parent) {
                    $url .= '/' . $parent['alias'];
                }
            }
            $url .= '/<cat:' . $v['alias'] . '>';
            $data[$url] = 'article/index';
        }
        $json = json_encode($data);
        $path = Yii::getAlias('@frontend/runtime/cache/');
        if( !file_exists($path) ) FileHelper::createDirectory($path);
        file_put_contents($path . 'category.txt', $json);
    }

    public function getUrlRules()
    {
        $file = Yii::getAlias('@frontend/runtime/cache/category.txt');
        if( !file_exists($file) ){
            $this->_generateUrlRules();
        }
        return json_decode(file_get_contents($file), true);
    }

    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

}
