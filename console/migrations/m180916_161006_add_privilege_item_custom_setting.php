<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;

/**
 * Class m180916_161006_add_privilege_item_custom_setting
 */
class m180916_161006_add_privilege_item_custom_setting extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $authManager = $this->getAuthManager();
        $this->execute("BEGIN;
                        INSERT INTO {$authManager->itemTable} VALUES
                                                       ('/setting/custom-create:GET', '2', '创建自定义设置项(查看)', null, 0x733A38313A227B2267726F7570223A225C75386262655C7537663665222C22736F7274223A22303135222C2263617465676F7279223A225C75383165615C75356239615C75346534395C75386262655C7537663665227D223B, '1537117837', '1537117837'),
                                                       ('/setting/custom-update:GET', '2', '修改自定义设置项(查看)', null, 0x733A38313A227B2267726F7570223A225C75386262655C7537663665222C22736F7274223A22303137222C2263617465676F7279223A225C75383165615C75356239615C75346534395C75386262655C7537663665227D223B, '1537117837', '1537117837'),
                                                       ('/setting/custom-update:POST', '2', '修改自定义设置项(确定)', null, 0x733A38313A227B2267726F7570223A225C75386262655C7537663665222C22736F7274223A22303138222C2263617465676F7279223A225C75383165615C75356239615C75346534395C75386262655C7537663665227D223B, '1537117837', '1537117837'),
                                                       ('/setting/custom-delete:POST', '2', '删除自定义设置项(确定)', null, 0x733A38313A227B2267726F7570223A225C75386262655C7537663665222C22736F7274223A22303139222C2263617465676F7279223A225C75383165615C75356239615C75346534395C75386262655C7537663665227D223B, '1537117837', '1537117837');
                             COMMIT;"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;
        $this->db->createCommand()->delete($authManager->itemTable, ['in', 'name', ['/setting/custom-create:GET', '/setting/custom-update:GET', '/setting/custom-update:POST', '/setting/custom-delete:POST']])->execute();
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180916_161006_add_privilege_item_custom_setting cannot be reverted.\n";

        return false;
    }
    */
}
