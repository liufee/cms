<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-02-23 10:24
 */

namespace backend\models\search;

use Yii;
use backend\models\form\AdForm;
use common\libs\Constants;
use common\models\Options;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class OptionsSearch
 * @package backend\models\search
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
class OptionsSearch extends Model implements SearchInterface
{
    public $id = null;

    public $type = null;

    public $name = null;

    public $value = null;

    public $input_type = null;

    public $tips = null;

    public $autoload = null;

    public $sort = null;


    public function rules()
    {
        return [
            [['id', 'sort'], 'integer'],
            [['type'], 'in', 'range'=>[Options::TYPE_SYSTEM, Options::TYPE_CUSTOM, Options::TYPE_BANNER, Options::TYPE_AD]],
            [['name', 'value', 'tips'], 'safe'],
            [['input_type'], 'in', 'range' => Constants::getInputTypeItems()],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function search(array $params = [], array $options = [])
    {
        switch ($this->type){
            case Options::TYPE_AD:
                $query = AdForm::find()->andWhere(['type' => $this->type]);
                break;
            default:
                $query = Options::find()->andFilterWhere(['type' => $this->type]);
        }
        $query = $query->orderBy(['id'=>SORT_DESC]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
        ]);

        if (! $this->load($params)) {
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['value' => $this->value])
            ->andFilterWhere(['input_type' => $this->input_type])
            ->andFilterWhere(['like', 'tips', $this->tips])
            ->andFilterWhere(['autoload' => $this->autoload])
            ->andFilterWhere(['sort' => $this->sort]);
        return $dataProvider;
    }
}