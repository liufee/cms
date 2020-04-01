<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-03-22 15:44
 */

namespace backend\models\form;


class AssignPermissionForm extends \yii\base\Model
{
    private $roles = [];

    private $permissions = [];


    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['roles', 'permissions'], 'safe'];
        return $rules;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if( isset($values['roles']) ){
            if( !is_array($values['roles']) ){
                $this->roles = [];
            }else {
                $this->roles =$values['roles'];
            }
        }

        if( isset($values['permissions']) ){
            if( !is_array($values['permissions']) ){
                $this->permissions = [];
            }else {
                $temp = array_flip($values['permissions']);
                if( isset($temp['0']) ){
                    unset($temp['0']);
                }
                $this->permissions = $temp;
            }
        }
    }
}