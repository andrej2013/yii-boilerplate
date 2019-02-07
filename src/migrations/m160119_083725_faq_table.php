<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

use yii\db\Schema;
use andrej2013\yiiboilerplate\Migration;

class m160119_083725_faq_table extends Migration
{
    public function up()
    {
        if ($this->db->getTableSchema('{{%faq}}', true) === null) {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

            $this->createTable(
                '{{%faq}}',
                [
                    'id' => Schema::TYPE_PK . "",
                    'title' => Schema::TYPE_STRING . "(255) NOT NULL",
                    'content' => Schema::TYPE_TEXT . " NOT NULL",
                    'language_id' => Schema::TYPE_STRING . "(5) COLLATE utf8_unicode_ci NOT NULL",
                    'place' => "enum('backend','frontend')" . " NOT NULL",
                    'level' => Schema::TYPE_INTEGER . ' NOT NULL',
                    'order' => Schema::TYPE_INTEGER,
                ],
                $tableOptions
            );

            $this->createIndex('level_idx', '{{%faq}}', 'level');
            $this->createIndex('language_id_idx', '{{%faq}}', 'language_id', 0);
            $this->createIndex('order_idx', '{{%faq}}', 'order');
            $this->addForeignKey('faq_fk_language_language_id', '{{%faq}}', 'language_id', '{{%language}}', 'language_id');
        }
    }

    public function down()
    {
        echo "m160119_083725_faq_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
