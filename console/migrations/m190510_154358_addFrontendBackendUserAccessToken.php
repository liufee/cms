<?php

use common\models\AdminUser;
use common\models\User;
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
        $userAccessToken = $this->string(42)->after("avatar")->defaultValue("")->notNull();
        $adminUserAccessToken = $this->string(42)->after("avatar")->defaultValue("")->notNull();

        if ($this->db->driverName === 'mysql') {
            $userAccessToken->comment("token");
            $adminUserAccessToken->comment("token");
        }

        $this->addColumn(User::tableName(), "access_token", $userAccessToken);
        $this->addColumn(AdminUser::tableName(), "access_token", $adminUserAccessToken);
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
