<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 3/1/2017
 * Time: 11:57 AM
 */

/**
 * @var \andrej2013\yiiboilerplate\templates\test\Generator $generator
 * @var \yii\db\ActiveRecord $model
 */

$model = $generator->modelClass;
$model = new $model;
echo "<?php\n";
?>
namespace tests\codeception\unit\models;

use yii\codeception\TestCase;
use tests\codeception\fixtures\<?= $generator->getModelShortName() ?>Fixtures;
use <?= $generator->modelClass ?>;

class <?= $generator->getModelShortName() ?>Test extends TestCase
{
    /**
     * Global unit testing configuration
     */
    public $appConfig = '@tests/codeception/_config/unit.php';

    /**
     * Fixtures needed to test this model
     */
    public function fixtures()
    {
        return [
<?php if (!empty($relations = $generator->getRelationModels())) :
    foreach ($relations as $relation) :
        echo str_repeat(' ', 12) . "'$relation' => \\tests\\codeception\\fixtures\\{$relation}Fixtures::className(),\n";
    endforeach;
endif;
?>
            '<?= $generator->getModelShortName() ?>' => <?= $generator->getModelShortName() ?>Fixtures::className(),
        ];
    }

    /**
     * @Inherit
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Test that each model can be touched. This will make sure the find and save of a model properly works.
     */
    public function testTouchAll()
    {
        $data = <?= $generator->getModelShortName() ?>::find()->all();
        foreach ($data as $model) {
            $model->created_by = 2;
            $model->save(false);
            $this->assertEquals(2, <?= $generator->getModelShortName() ?>::findOne([<?= $generator->buildPkQueryCondition() ?>])->created_by);
        }
    }

    /*
     * Test that creating an empty element generated an error.
     */
    public function testCreate()
    {
        $model = new <?= $generator->getModelShortName() ?>;
<?php if ($generator->anyRequiredColumn()) : ?>
        $this->assertFalse($model->save());
<?php endif; ?>
<?php foreach ($model->attributes as $attribute => $temp) :
    if (in_array($attribute, $generator->avoidColumns())) {
        continue;
    }
    echo str_repeat(' ', 8) . '$model->' . $attribute . " = '{$generator->getProperAttributeValue($attribute)}';\n";
endforeach; ?>
        $this->assertTrue($model->save(), true);
    }

    /*
     * Test that deleting an object works as intended
     */
    public function testDelete()
    {
<?php $query = $generator->buildPkQueryConditionExisting() ?>
        $model = <?= $generator->getModelShortName() ?>::findOne([<?= $query ?>]);

        $model->delete();

        //@TODO Find a better way to check for Soft Delete behavior
        if (in_array('andrej2013\yiiboilerplate\behaviors\SoftDelete', $model->behaviors())) {
            // Check if Soft-Delete find() will not find it
            $this->assertNull(<?= $generator->getModelShortName() ?>::findOne([<?= $query ?>]));
            // Check if record is still in database, by not checking Soft-Delete deleted_at attribute
            $this->assertNotNull(<?= $generator->getModelShortName() ?>::findDeleted()->andWhere([<?= $query ?>])->one());
        } else {
            // Check if record is really deleted from database
            $this->assertNull(<?= $generator->getModelShortName() ?>::findDeleted()->andWhere([<?= $query ?>])->one());
        }
    }
}
