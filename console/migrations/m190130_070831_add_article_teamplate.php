<?php

use yii\db\Migration;

/**
 * Class m190130_070831_add_article_teamplate
 */
class m190130_070831_add_article_teamplate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%category}}","template", $this->string()->after("sort")->defaultValue("")->notNull()->comment("分类模板"));
        $this->addColumn("{{%category}}","article_template", $this->string()->after("template")->defaultValue("")->notNull()->comment("文章模板"));
        $this->addColumn("{{%article}}", "template", $this->string()->after("flag_picture")->defaultValue("")->notNull()->comment("文章模板"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%category}}", "template");
        $this->dropColumn("{{%category}}", "article_template");
        $this->dropColumn("{{%article}}", "template");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190130_070831_add_article_teamplate cannot be reverted.\n";

        return false;
    }
    */
}
