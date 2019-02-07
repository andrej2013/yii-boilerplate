<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/** @var $migrationName string the new migration class name
 *  @var array  $tableRelations
 *  @var insolita\migrik\gii\Generator $generator
 */

echo "<?php\n";
?>

use yii\db\Schema;
use andrej2013\yiiboilerplate\TwMigration;

class <?= $migrationName ?> extends TwMigration
{
    public function safeUp()
    {
<?php if (!empty($tableRelations) && is_array($tableRelations)) :
    foreach ($tableRelations as $table) :
        foreach ($table['fKeys'] as $i => $rel) : ?>
        $this->addForeignKey('fk_<?=$table['tableName']?>_<?=$rel['pk']?>', '<?=$table['tableAlias']?>', '<?=$rel['pk']?>', '{{%<?=$rel['ftable']?>}}', '<?=$rel['fk']?>');
<?php
        endforeach;
    endforeach;
endif?>
    }

    public function safeDown()
    {
<?php if (!empty($tableRelations) && is_array($tableRelations)) :
    foreach ($tableRelations as $table) :
        foreach ($table['fKeys'] as $i => $rel) :?>
        $this->dropForeignKey('fk_<?=$table['tableName']?>_<?=$rel['pk']?>', '<?=$table['tableAlias']?>');
<?php
        endforeach;
    endforeach;
endif?>

    }
}
