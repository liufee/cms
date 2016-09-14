<?php
namespace backend\models;

use yii\data\ActiveDataProvider;

class FileSearch extends File{

    public $create_start_at;
    public $create_end_at;
    public $update_start_at;
    public $update_end_at;

    public function rules()
    {
        return [
            [['filesize', 'status', 'created_at', 'updated_at'], 'integer'],
            [['uri', 'filename', 'mime'], 'string', 'max' => 255],
        ];
    }

    public function search($params)
    {
        $query = File::find()->select([]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'filename', $this->filename])
            ->andFilterWhere(['like', 'uri', $this->uri])
            ->andFilterWhere(['filesize'=>$this->filesize])
            ->andFilterWhere(['mime'=>$this->mime])
            ->andFilterWhere(['status'=>$this->status]);
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
        return $dataProvider;
    }

}