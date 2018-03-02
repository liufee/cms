<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;

/**
 * Class m180225_064210_add_view_detail_permissions
 */
class m180225_064210_add_view_detail_permissions extends Migration
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
     * @inheritdoc
     */
    public function safeUp()
    {
        $authManager = $this->getAuthManager();
        $this->execute("BEGIN;
                        INSERT INTO {$authManager->itemTable} VALUES ('/ad/view-layer:GET', '2', '广告详情', null, 0x733a37353a227b2267726f7570223a225c75386664305c75383432355c75376261315c7537343036222c22736f7274223a22393137222c2263617465676f7279223a225c75356537665c7535343461227d223b, '1519972316', '1519972316'), 
                            ('/admin-user/view-layer:GET', '2', '后台用户详情', null, 0x733a37353a227b2267726f7570223a225c75373532385c7536323337222c22736f7274223a22333137222c2263617465676f7279223a225c75353430655c75353366305c75373532385c7536323337227d223b, '1505491177', '1505626626'), 
                            ('/article/view-layer:GET', '2', '文章详情', null, 0x733a36333a227b2267726f7570223a225c75353138355c7535626239222c22736f7274223a22323037222c2263617465676f7279223a225c75363538375c7537616530227d223b, '1505491177', '1505626626'), 
                            ('/banner/view-layer:GET', '2', 'banner详情', null, 0x733a36393a227b2267726f7570223a225c75386664305c75383432355c75376261315c7537343036222c22736f7274223a22383137222c2263617465676f7279223a2262616e6e6572227d223b, '1505491177', '1505626626'), 
                            ('/category/view-layer:GET', '2', '分类详情', null, 0x733a36333a227b2267726f7570223a225c75353138355c7535626239222c22736f7274223a22323137222c2263617465676f7279223a225c75353230365c7537633762227d223b, '1505491177', '1505626626'), 
                            ('/comment/view-layer:GET', '2', '评论详情', null, 0x733a36333a227b2267726f7570223a225c75353138355c7535626239222c22736f7274223a22323233222c2263617465676f7279223a225c75386263345c7538626261227d223b, '1505491177', '1505626626'), 
                            ('/friendly-link/view-layer:GET', '2', '友情链接详情', null, 0x733a38373a227b2267726f7570223a225c75353363625c75363063355c75393466655c7536336135222c22736f7274223a22353037222c2263617465676f7279223a225c75353363625c75363063355c75393466655c7536336135227d223b, '1505491177', '1505626626'), 
                            ('/frontend-menu/view-layer:GET', '2', '前台菜单详情', null, 0x733a37353a227b2267726f7570223a225c75383364635c7535333535222c22736f7274223a22313037222c2263617465676f7279223a225c75353234645c75353366305c75383364635c7535333535227d223b, '1505491177', '1505626626'), 
                            ('/menu/view-layer:GET', '2', '后台菜单详情', null, 0x733a37353a227b2267726f7570223a225c75383364635c7535333535222c22736f7274223a22313137222c2263617465676f7279223a225c75353430655c75353366305c75383364635c7535333535227d223b, '1505491177', '1505626626'), 
                            ('/page/view-layer:GET', '2', '单页详情', null, 0x733a36333a227b2267726f7570223a225c75353138355c7535626239222c22736f7274223a22323337222c2263617465676f7279223a225c75353335355c7539383735227d223b, '1505491177', '1505626626'),      
                            ('/rbac/permission-view-layer:GET', '2', '权限详情', null, 0x733a37353a227b2267726f7570223a225c75363734335c75393635305c75376261315c7537343036222c22736f7274223a22343037222c2263617465676f7279223a225c75363734335c7539363530227d223b, '1505491177', '1505626626'), 
                            ('/rbac/role-view-layer:GET', '2', '角色详情', null, 0x733a37353a227b2267726f7570223a225c75363734335c75393635305c75376261315c7537343036222c22736f7274223a22343137222c2263617465676f7279223a225c75383964325c7538323732227d223b, '1505491177', '1505626626'), 
                            ('/user/view-layer:GET', '2', '前台用户详情', null, 0x733a37353a227b2267726f7570223a225c75373532385c7536323337222c22736f7274223a22333036222c2263617465676f7279223a225c75353234645c75353366305c75373532385c7536323337227d223b, '1505491177', '1505626626');                                                                                           
                        COMMIT;"
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180225_064210_add_view_detail_permissions cannot be reverted.\n";

        return false;
    }
    */
}
