<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 3/1/2017
 * Time: 11:58 AM
 */

/**
 * @var \andrej2013\yiiboilerplate\templates\test\Generator $generator
 */

echo "<?php\n";
?>

// @group optional

use tests\codeception\_pages\LoginPage;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that login works');

$I->amGoingTo('try to login with correct credentials');
LoginPage::openBy($I)->login('admin', getenv('APP_ADMIN_PASSWORD'));
$I->expectTo('see user info');

// See the datagrid
$I->amOnPage('/<?= $generator->getControllerRoute() ?>');
$I->see('New', '.btn-success');


// See the create form
$I->amOnPage('/<?= $generator->getControllerRoute() ?>/create');
$I->see('Create', '.btn-success');

// Submit the form, view errors
$I->click('Create');
$I->seeElement('.has-error');
