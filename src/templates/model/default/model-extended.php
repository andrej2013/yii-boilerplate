<?php
/**
 * This is the template for generating the model class of a specified table.
 *
 * @var yii\web\View $this
 * @var yii\gii\generators\model\Generator $generator
 * @var string $className class name
 */
echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
use \<?= $generator->ns ?>\base\<?= $className ?> as Base<?= $className ?>;

/**
 * This is the model class for table "<?= $tableName ?>".
 */
class <?= $className ?> extends Base<?= $className . "\n" ?>
{
    /**
     * List of additional rules to be applied to model, uncomment to use them
     * @return array
     */
    /*
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['something'], 'safe'],
        ]);
    }
    */
}
