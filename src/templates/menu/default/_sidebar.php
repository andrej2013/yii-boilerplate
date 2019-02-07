<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/8/2017
 * Time: 2:32 PM
 */

/**
 * @var $generator \andrej2013\yiiboilerplate\templates\menu\Generator
 */

echo "<?php\n";
?>

namespace _;

use Yii;

// This is the dashboard sidebar
$adminMenuItems = [];

$adminMenuItems = [
    [
        'label' => Yii::t('app', 'Dashboard'),
        'url' => ['/backend'],
        'visible' => !Yii::$app->user->isGuest,
        'icon' => 'fa fa-dashboard',
    ],
    [
        'label' => Yii::t('app', 'Pages'),
        'url' => ['/pages'],
        'icon' => 'fa fa-tree',
        'visible' => Yii::$app->user->can('Editor')
    ],
    [
        'label' => Yii::t('app', 'Users'),
        'url' => ['/user/admin'],
        'icon' => 'fa fa-user',
        'visible' => Yii::$app->user->can('Administrator')
    ],
];

<?php foreach ($generator->controllers as $controller) : ?>
$adminMenuItems[] = [
    'label' => Yii::t('app', '<?= $generator->getMenuName($controller) ?>'),
    'url' => ['/<?= $generator->getUrl($controller) ?>'],
    'icon' => 'fa fa-bars',
    'visible' => Yii::$app->user->can('Administrator')
];
<?php endforeach ?>

echo $this->render('@andrej2013-backend-views/layouts/_sidebar_admin', ['adminMenuItems' => $adminMenuItems]);
