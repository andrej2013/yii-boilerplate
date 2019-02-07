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

use tests\codeception\_pages\LoginPage;

$I = new AcceptanceTester($scenario);

$fixtures = new \tests\codeception\_support\FixtureHelper();
$fixtures->unloadFixtures();
$fixtures->loadFixtures();

$autoLogin = new \tests\codeception\_support\AutoLogin($I);
$autoLogin->adminLogin();

$crud = new \tests\codeception\_pages\CrudPage($I);

$crud->assertIndex('/<?= $generator->getControllerRoute() ?>');
$crud->assertCreate('/<?= $generator->getControllerRoute() ?>/create');

$fixtures->unloadFixtures();
