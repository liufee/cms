<?php

use yii\db\Migration;

/**
 * Class m190510_154358_addFrontendUserAccessToken
 */
class m190510_154358_addFrontendBackendUserAccessToken extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(\common\models\User::tableName(), "access_token", $this->string(42)->after("avatar")->defaultValue("")->notNull()->comment("登录token"));
        $this->addColumn(\common\models\AdminUser::tableName(), "access_token", $this->string(42)->after("avatar")->defaultValue("")->notNull()->comment("登录token"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(\common\models\User::tableName(), "access_token");
        $this->dropColumn(\common\models\AdminUser::tableName(), "access_token");
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190510_154358_addFrontendUserAccessToken cannot be reverted.\n";

        return false;
    }
    */
}
