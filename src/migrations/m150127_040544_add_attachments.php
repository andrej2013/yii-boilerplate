<?php

use yii\db\Migration;
use yii\db\Schema;

class m150127_040544_add_attachments extends Migration
{
    public function up()
    {
        if ($this->db->getTableSchema(Yii::$app->getModule('attachments')->tableName, true) === null) {
            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            }
            
            $this->createTable(Yii::$app->getModule('attachments')->tableName, [
                'id'     => Schema::TYPE_PK,
                'name'   => Schema::TYPE_STRING . ' not null',
                'model'  => Schema::TYPE_STRING . ' not null',
                'itemId' => Schema::TYPE_INTEGER . ' not null',
                'hash'   => Schema::TYPE_STRING . ' not null',
                'size'   => Schema::TYPE_INTEGER . ' not null',
                'type'   => Schema::TYPE_STRING . ' not null',
                'mime'   => Schema::TYPE_STRING . ' not null',
            ], $tableOptions);
    
            $this->createIndex('file_model', Yii::$app->getModule('attachments')->tableName, 'model');
            $this->createIndex('file_item_id', Yii::$app->getModule('attachments')->tableName, 'itemId');
        }
    }

    public function down()
    {
        $this->dropTable($this->getModule()->tableName);
    }
}
