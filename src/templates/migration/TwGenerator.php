<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 08.12.14
 * Time: 8:03
 */
namespace andrej2013\yiiboilerplate\templates\migration;

use Yii;
use yii\db\Expression;
use yii\db\Schema;
use yii\gii\CodeFile;

set_time_limit(0);

/**
 * Class TwGenerator
 * @package andrej2013\yiiboilerplate\templates\migration
 */
class TwGenerator extends \insolita\migrik\gii\Generator
{
    /**
     * Generate the migration file
     * @return array
     */
    public function generate()
    {
        $files = $tableRelations = $tableList = [];
        $db = $this->getDbConnection();
        $i = 10;
        if ($this->genmode == 'single') {
            foreach ($this->getTables() as $tableName) {
                $i++;
                $tableSchema = $db->getTableSchema($tableName);
                $tableCaption = $this->getTableCaption($tableName);
                $tableAlias = $this->getTableAlias($tableCaption);
                $tableIndexes = $this->genmode == 'schema' ? null : $this->generateIndexes($tableName);
                $tableColumns = $this->columnsBySchema($tableSchema);
                $tablePrimaryKeys = $this->generatePrimaryKeys($tableSchema);
                $tableRelations[] = ['fKeys' => $this->generateRelations($tableSchema),
                    'tableAlias' => $tableAlias,
                    'tableName' => $tableName
                ];
                $migrationName = 'm' . gmdate('ymd_Hi' . $i) . '_' . $tableCaption;
                $params = compact(
                    'tableName',
                    'tableSchema',
                    'tableCaption',
                    'tableAlias',
                    'tablePrimaryKeys',
                    'migrationName',
                    'tableColumns',
                    'tableIndexes'
                );
                $files[] = new CodeFile(
                    Yii::getAlias($this->migrationPath) . '/' . $migrationName . '.php',
                    $this->render('migration.php', $params)
                );
            }
            $i++;

            /**Костыль.. иначе gii глючит при попытке просмотра **/
            $migrationName = 'm' . gmdate('ymd_Hi' . $i) . '_Relations';
            //$migrationName='m' . gmdate('ymd_His') . '_Relations';
            $params = ['tableRelations' => $tableRelations, 'migrationName' => $migrationName];
            $files[] = new CodeFile(
                Yii::getAlias($this->migrationPath) . '/' . $migrationName . '.php',
                $this->render('relation.php', $params)
            );
        } else {
            foreach ($this->getTables() as $tableName) {
                $i++;
                $tableSchema = $db->getTableSchema($tableName);
                $tableCaption = $this->getTableCaption($tableName);
                $tableAlias = $this->getTableAlias($tableCaption);
                $tableIndexes = $this->generateIndexes($tableName);
                $tableColumns = $this->columnsBySchema($tableSchema);
                $tablePrimaryKeys = $this->generatePrimaryKeys($tableSchema);
                $tableRelations[] = [
                    'fKeys' => $this->generateRelations($tableSchema),
                    'tableAlias' => $tableAlias,
                    'tableName' => $tableName
                ];
                $tableList[] = [
                    'alias' => $tableAlias,
                    'indexes' => $tableIndexes,
                    'primaryKeys' => $tablePrimaryKeys,
                    'columns' => $tableColumns,
                    'name' => $tableName
                ];
            }
            $i++;
            //$migrationName='m' . gmdate('ymd_His') . '_Mass';
            $migrationName = 'm' . gmdate('ymd_Hi' . $i) . '_Mass';
            $params = ['tableList' => $tableList,
                'tableRelations' => $tableRelations,
                'migrationName' => $migrationName
            ];
            $files[] = new CodeFile(
                Yii::getAlias($this->migrationPath) . '/' . $migrationName . '.php',
                $this->render('mass.php', $params)
            );
        }

        return $files;
    }

    /**
     * @param $col
     * @return string
     */
    public function getColumnType($col)
    {
        $coldata = $append = '';

        // Detect autoIncrement
        if ($col->autoIncrement) {
            if ($col->type !== Schema::TYPE_BIGINT) {
                if ($col->unsigned) {
                    $coldata = 'Schema::TYPE_UPK';
                } else {
                    $coldata = 'Schema::TYPE_PK';
                }
            } else {
                if ($col->unsigned) {
                    $coldata = 'Schema::TYPE_UBIGPK';
                } else {
                    $coldata = 'Schema::TYPE_BIGPK';
                }
            }
        } elseif (strpos($col->dbType, 'set(') !== false) {
            $coldata = '"' . $col->dbType . '"';
        } elseif (strpos($col->dbType, 'enum(') !== false) {
            $coldata = '"' . $col->dbType . '"';
        } elseif ($col->dbType === 'tinyint(1)') {
            $coldata = 'Schema::TYPE_BOOLEAN';
        } else {
            $coldata = 'Schema::TYPE_' . strtoupper($col->type);
        }

        if ($col->size && !$col->autoIncrement) {
            $append .= ($col->scale) ? '(' . $col->size . ',' . $col->scale . ')' : '(' . $col->size . ')';
        }
        $append .= ($col->unsigned && !$col->autoIncrement) ? ' unsigned' : '';
        $append .= (!$col->allowNull && !$col->autoIncrement) ? ' NOT NULL' : '';

        if (!is_null($col->defaultValue)) {
            $append .= " DEFAULT " .
                ($col->defaultValue instanceof Expression ?
                    $col->defaultValue->expression :
                    "'" . $col->defaultValue . "'"
                );
        }
        if (!empty($col->comment)) {
            $append .= " COMMENT '" . str_replace('"', '\"', $col->comment) . "'";
        }

        return $coldata . '."' . $append . '"';
    }


    /**
     * Generate the composite primary key
     * @param $schema
     * @return array
     */
    public function generatePrimaryKeys($schema)
    {
        $primaryKeys = [];

        // Look for all primary keys
        foreach ($schema->columns as $column) {
            if ($column->isPrimaryKey) {
                $primaryKeys[] = $column->name;
            }
        }

        // Only return values if at least 2 primary keys
        if (count($primaryKeys) >= 2) {
            return $primaryKeys;
        }
        return null;
    }
}
