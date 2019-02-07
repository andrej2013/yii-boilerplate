<?php

namespace andrej2013\yiiboilerplate;

use Yii;
use yii\console\Exception;
use yii\db\ColumnSchema;
use yii\db\Migration as BaseMigration;
use yii\db\Schema;

class Migration extends BaseMigration
{

    /**
     * MySQL Conventions for Table-Names
     * - Checks conventions and throws Exception if the table-name does not follow them
     * @param string $columnName
     * @param string $method
     * @throws Exception
     */
    public function mysqlConventionsColumn($columnName, $method)
    {
        if (preg_match('/^[a-z_0-9]*$/', $columnName) != 1) {
            throw new Exception("The column name '$columnName' in your $method-Migration does not follow the tw mysql conventions. Please make sure to create columns with only lowercase letters a-z or '_'.");
        }
    }

    /**
     * MySQL Conventions for Table-Names
     * - Checks conventions and throws Exception if the table-name does not follow them
     * @param array $columns
     * @param string $method
     * @throws Exception
     */
    public function mysqlConventionsColumns($columns, $method)
    {
        foreach ($columns as $column => $type) {
            $this->mysqlConventionsColumn($column, $method);
        }
    }

    /**
     * MySQL Conventions for Table-Names
     * - Checks conventions and throws Exception if the table-name does not follow them
     * @param string $tableName
     * @param string $method
     * @throws Exception
     */
    public function mysqlConventions($tableName, $method)
    {
        if (preg_match('/\{\{\%[a-z_0-9]*\}\}/', $tableName) != 1) {
            throw new Exception("The table name '$tableName' in your $method-Migration does not follow the tw mysql conventions. Please make sure to create tables within {{% }} with only lowercase letters a-z or '_'.");
        }
    }

    /**
     * @param string $table
     * @param array $columns
     * @param null $options
     * @param boolean $enableBehaviors if behaviors-fields (created_at,...) should be added or not
     * @throws \yii\db\Exception
     * @throws \yii\console\Exception
     */
    public function createTable($table, $columns, $options = null, $enableBehaviors = true)
    {
        $this->mysqlConventions($table, 'createTable');
        $this->mysqlConventionsColumns($columns, 'createTable');
        echo "    > create table $table " . (($enableBehaviors) ? "with behaviors" : "without behaviors") . " ...";
        $time = microtime(true);
        $this->db->createCommand()->createTable($table, $columns, $options)->execute();

        // Add Behaviors if enabled
        if ($enableBehaviors) {
            $this->db->createCommand()->addColumn($table, "created_by", Schema::TYPE_INTEGER)->execute();
            $this->db->createCommand()->addColumn($table, "created_at", Schema::TYPE_DATETIME)->execute();
            $this->db->createCommand()->addColumn($table, "updated_by", Schema::TYPE_INTEGER)->execute();
            $this->db->createCommand()->addColumn($table, "updated_at", Schema::TYPE_DATETIME)->execute();
            $this->db->createCommand()->addColumn($table, "deleted_by", Schema::TYPE_INTEGER)->execute();
            $this->db->createCommand()->addColumn($table, "deleted_at", Schema::TYPE_DATETIME)->execute();
        }
        $this->createIndex('deleted_at_idx', $table, "deleted_at");

        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * @inheritdoc
     */
    public function renameTable($table, $newName)
    {
        $this->mysqlConventions($table, 'renameTable');
        $this->mysqlConventions($newName, 'renameTable');
        parent::renameTable($table, $newName);
    }

    /**
     * @inheritdoc
     */
    public function renameColumn($table, $name, $newName)
    {
        $this->mysqlConventions($table, 'renameColumn');
        parent::renameColumn($table, $name, $newName);
    }

    /**
     * @inheritdoc
     */
    public function delete($table, $condition = '', $params = [])
    {
        $this->mysqlConventions($table, 'delete');
        parent::delete($table, $condition, $params);
    }

    /**
     * @inheritdoc
     */
    public function update($table, $columns, $condition = '', $params = [])
    {
        $this->mysqlConventions($table, 'update');
        $this->mysqlConventionsColumns($columns, 'update');
        parent::update($table, $columns, $condition, $params);
    }

    /**
     * @inheritdoc
     */
    public function insert($table, $columns)
    {
        $this->mysqlConventions($table, 'insert');
        parent::insert($table, $columns);
    }

    /**
     * @inheritdoc
     */
    public function alterColumn($table, $column, $type)
    {
        $this->mysqlConventions($table, 'alterColumn');
        parent::alterColumn($table, $column, $type);
    }

    /**
     * @inheritdoc
     */
    public function addColumn($table, $column, $type)
    {
        $this->mysqlConventions($table, 'addColumn');
        $this->mysqlConventionsColumn($column, 'addColumn');
        parent::addColumn($table, $column, $type);
    }

    /**
     * @inheritdoc
     */
    public function dropColumn($table, $column)
    {
        $this->mysqlConventions($table, 'dropColumn');
        parent::dropColumn($table, $column);
    }

    /**
     * @inheritdoc
     */
    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        $this->mysqlConventions($refTable, 'addForeignKey');
        parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
    }

    /**
     * @inheritdoc
     */
    public function dropForeignKey($name, $table)
    {
        $this->mysqlConventions($table, 'dropForeignKey');
        parent::dropForeignKey($name, $table);
    }

    /**
     * @inheritdoc
     */
    public function createIndex($name, $table, $columns, $unique = false)
    {
        $this->mysqlConventions($table, 'createIndex');
        parent::createIndex($name, $table, $columns, $unique);
    }

    /**
     * @inheritdoc
     */
    public function dropIndex($name, $table)
    {
        $this->mysqlConventions($table, 'dropIndex');
        parent::dropIndex($name, $table);
    }

    /**
     * @inheritdoc
     */
    public function addPrimaryKey($name, $table, $columns)
    {
        $this->mysqlConventions($table, 'addPrimaryKey');
        parent::addPrimaryKey($name, $table, $columns);
    }

    /**
     * @inheritdoc
     */
    public function dropPrimaryKey($name, $table)
    {
        $this->mysqlConventions($table, 'dropPrimaryKey');
        parent::dropPrimaryKey($name, $table);
    }

    /**
     * @inheritdoc
     */
    public function dropTable($table)
    {
        $this->mysqlConventions($table, 'dropTable');
        parent::dropTable($table);
    }

    /**
     * Get the AuthManager
     * @return \yii\rbac\ManagerInterface
     * @throws \yii\base\Exception
     */
    public function getAuth()
    {
        $auth = Yii::$app->authManager;
        if ($auth instanceof \yii\rbac\DbManager) {
            return $auth;
        }

        throw new \yii\base\Exception('Application authManager must be an instance of \yii\rbac\DbManager');
    }
}
