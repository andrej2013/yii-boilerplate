<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 3/1/2017
 * Time: 11:56 AM
 */

/**
 * @var \andrej2013\yiiboilerplate\templates\test\Generator $generator
 */

echo "<?php\n";
?>

namespace tests\codeception\fixtures;

class <?= $generator->getModelShortName()?>Fixtures extends ActiveFixture
{
    public $modelClass = '<?=$generator->modelClass ?>';
}
