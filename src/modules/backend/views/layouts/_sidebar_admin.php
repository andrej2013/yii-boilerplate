<?php

namespace _;

use andrej2013\yiiboilerplate\modules\backend\widgets\Menu;
//use andrej2013\yiiboilerplate\widget\Gravatar;
use Yii;

?>

    <!-- Sidebar user panel -->
    <?php if (! \Yii::$app->user->isGuest) { ?>
    <div class="user-panel">
        <div class="pull-left image">
            <img src="<?= Yii::$app->user->identity->getProfilePicture() ?>" class="img-circle" alt="User Image"/>
        </div>
        <div class="pull-left info">
            <p><?php echo Yii::$app->user->identity->toString; ?></p>

            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
    </div>
    <?php } ?>
<?php
$developerMenuItems = [];
$modulesMenuItems = [];


// create developer menu, when user is admin
if (Yii::$app->user->identity && Yii::$app->user->can('Authority')) {

    $developerMenuItems = [
        [
            'label' => Yii::t('app', 'Users'),
            'url'   => ['/user/admin'],
            'icon'  => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Roles & Access'),
            'url'   => ['/rbac'],
            'icon'  => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Webshell'),
            'url'   => ['/webshell'],
            'icon'  => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Log Viewer'),
            'url'   => ['/logreader'],
            'icon'  => 'fa fa-cube',
        ],
        [
            'label'    => Yii::t('app', 'Gii Generator'),
            'url'      => ['/gii'],
            'icon'     => 'fa fa-arrow-right',
            'template' => '<a href="{url}" target="_blank">{icon}{label}</a>',
            'visible'  => YII_ENV == 'dev' && Yii::$app->hasModule('gii') && Yii::$app->getModule('gii')
                                                                                      ->checkAccess(),
        ],
        [
            'label' => Yii::t('app', 'History'),
            'url'   => ['/backend/history'],
            'icon'  => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'User Authentication Log'),
            'url'   => ['/backend/user-auth-log'],
            'icon'  => 'fa fa-cube',
        ],
        [
            'label'    => Yii::t('app', 'Adminer'),
            'url'      => ['/adminer'],
            'icon'     => 'fa fa-arrow-right',
            'template' => '<a href="{url}" target="_blank">{icon}{label}</a>',
        ],
        [
            'label'    => Yii::t('app', 'Translator'),
            'url'      => ['/translatemanager'],
            'icon'     => 'fa fa-arrow-right',
            'template' => '<a href="{url}" target="_blank">{icon}{label}</a>',
        ],
        [
            'label'    => Yii::t('app', 'Debug'),
            'url'      => ['/debug'],
            'icon'     => 'fa fa-arrow-right',
            'template' => '<a href="{url}" target="_blank">{icon}{label}</a>',
        ],
    ];

    $adminMenuItems[] = [
        'url'     => '/#',
        'icon'    => 'fa fa-cogs',
        'label'   => Yii::t('app', 'Developer'),
        'items'   => $developerMenuItems,
        'options' => ['class' => 'treeview'],
        'visible' => Yii::$app->user->can('Authority'),
    ];
}

echo Menu::widget([
    'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
    'items'   => \yii\helpers\ArrayHelper::merge([], //            \dmstr\modules\pages\models\Tree::getMenuItems('backend', true),
        $adminMenuItems),
]);
?>