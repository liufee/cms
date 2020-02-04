<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-29 21:15
 */

namespace backend\models\form;


use Yii;
use yii\rbac\Permission;

class RBACPermissionForm extends yii\base\Model
{
    public $route;

    public $method;

    public $description;

    public $sort;

    public $group;

    public $category;

    public function rules(){
        return [
            [['route', 'method', 'description', 'group', 'category'], 'required'],
            [['sort'], 'number'],
            [['sort'], 'default', 'value'=>0],
            [
                ['route'],
                'match',
                'pattern' => '/^[\/].*/',
                'message' => Yii::t('app', Yii::t('app', 'Must begin with "/" like "/module/controller/action" format')),
                'on' => 'permission'
            ],
        ];
    }

    public function getName()
    {
        return $this->route . ":" . $this->method;
    }

    public function getData()
    {
        return json_encode([
            'group' => $this->group,
            'sort' => $this->sort,
            'category' => $this->category,
        ]);
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if( $values instanceof Permission){
            $temp = explode(":", $values->name);
            $this->route = $temp[0];
            $this->method = $temp[1];
            $this->description = $values->description;
            $data = json_decode($values->data, true);
            $this->sort = $data['sort'];
            $this->group = $data['group'];
            $this->category = $data['category'];
        }else{
            parent::setAttributes($values, $safeOnly);
        }
    }
}