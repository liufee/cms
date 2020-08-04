<?php

use common\helpers\DbDriverHelper;
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
        $categoryTemplate = $this->string()->after("sort")->defaultValue("")->notNull();
        $categoryArticleTemplate = $this->string()->after("template")->defaultValue("")->notNull();
        $articleTemplate =   $this->string()->after("flag_picture")->defaultValue("")->notNull();

        if (!DbDriverHelper::isSqlite()) {
            $categoryTemplate->comment("category page template path");
            $categoryArticleTemplate->comment("article detail page template path");
            $articleTemplate->comment("article detail page template path");
        }

        $this->addColumn("{{%category}}","template", $categoryTemplate);
        $this->addColumn("{{%category}}","article_template", $categoryArticleTemplate);
        $this->addColumn("{{%article}}", "template", $articleTemplate);
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
