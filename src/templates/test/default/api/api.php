<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 3/1/2017
 * Time: 11:59 AM
 */

/**
 * @var \andrej2013\yiiboilerplate\templates\test\Generator $generator
 */

echo "<?php\n";
?>

$I = new ApiTester($scenario);
$I->wantTo('list of <?= $generator->getModelShortName() ?> via API');
$I->sendGET('/<?= $generator->getControllerRoute() ?>');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200 $I->seeResponseIsJson();


$I = new ApiTester($scenario);
$I->wantTo('create a <?= $generator->getModelShortName() ?> via API');
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendPOST('/<?= $generator->getControllerRoute() ?>', [<?= $generator->getRequiredQuery() ?>]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200 $I->seeResponseIsJson();
$I->seeResponseContains('"<?= $generator->getPrimaryKeyNames()[0] ?>":');
