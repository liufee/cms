<?php
namespace common\models;

use yii\data\ActiveDataProvider;

class ArticleSearch extends Article{

    public $create_start_at;
    public $create_end_at;
    public $update_start_at;
    public $update_end_at;

    public function rules()
    {
        return [
            [['title','author_name','cid'], 'string'],
            [['created_at', 'updated_at'], 'string'],
            [['create_start_at', 'create_end_at', 'update_start_at', 'update_end_at'], 'string'],
            [['status', 'flag_headline', 'flag_recommend', 'flag_slide_show', 'flag_special_recommend', 'flag_roll', 'flag_bold', 'flag_picture'], 'integer'],
        ];
    }

    public function search($params, $type=self::ARTICLE)
    {//var_dump(Category::getAsArray());die;
        $query = Article::find()->select([])->where(['type'=>$type]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        $this->load($params);//var_dump($params);die;
        if(!$this->validate()){var_dump($this->getErrors());die;
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['status'=>$this->status])
            ->andFilterWhere(['flag_headline'=>$this->flag_headline])
            ->andFilterWhere(['flag_recommend'=>$this->flag_recommend])
            ->andFilterWhere(['flag_slide_show'=>$this->flag_slide_show])
            ->andFilterWhere(['flag_special_recommend'=>$this->flag_special_recommend])
            ->andFilterWhere(['flag_roll'=>$this->flag_roll])
            ->andFilterWhere(['flag_bold'=>$this->flag_bold])
            ->andFilterWhere(['flag_picture'=>$this->flag_picture])
            ->andFilterWhere(['like', 'author_name', $this->author_name]);
        $create_start_at_unixtimestamp = $create_end_at_unixtimestamp = $update_start_at_unixtimestamp = $update_end_at_unixtimestamp = '';
        if($this->create_start_at != '') $create_start_at_unixtimestamp = strtotime($this->create_start_at);
        if($this->create_end_at != '') $create_end_at_unixtimestamp = strtotime($this->create_end_at);
        if($this->update_start_at != '') $update_start_at_unixtimestamp = strtotime($this->update_start_at);
        if($this->update_end_at != '') $update_end_at_unixtimestamp = strtotime($this->update_end_at);
        if($create_start_at_unixtimestamp != '' && $create_end_at_unixtimestamp == '') {
            $query->andFilterWhere(['>', 'created_at', $create_start_at_unixtimestamp]);
        }elseif ($create_start_at_unixtimestamp == '' && $create_end_at_unixtimestamp != ''){
            $query->andFilterWhere(['<', 'created_at', $create_end_at_unixtimestamp]);
        }else{
            $query->andFilterWhere(['between', 'created_at', $create_start_at_unixtimestamp, $create_end_at_unixtimestamp]);
        }

        if($update_start_at_unixtimestamp != '' && $update_end_at_unixtimestamp == '') {
            $query->andFilterWhere(['>', 'updated_at', $update_start_at_unixtimestamp]);
        }elseif ($update_start_at_unixtimestamp == '' && $update_end_at_unixtimestamp != ''){
            $query->andFilterWhere(['<', 'updated_at', $update_start_at_unixtimestamp]);
        }else{
            $query->andFilterWhere(['between', 'updated_at', $update_start_at_unixtimestamp, $update_end_at_unixtimestamp]);
        }

        $cids = Category::getSubTree($this->cid);//var_dump($cids);die;
        if(count($cids) <= 1){
            $query->andFilterWhere(['cid'=>$this->cid]);
        }else{
            $array = [];
            foreach ($cids as $v){
                $array[] = $v['id'];
            }
            $query->andFilterWhere(['cid'=>$array]);
        }
        return $dataProvider;
    }

}