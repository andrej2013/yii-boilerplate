<?php

namespace _;

use andrej2013\yiiboilerplate\modules\backend\widgets\Menu;
//use andrej2013\yiiboilerplate\widget\Gravatar;
use Yii;

?>

    <!-- Sidebar user panel -->
<?php if (!\Yii::$app->user->isGuest): ?>
    <div class="user-panel">
        <div class="pull-left image">
<!--            --><?php //echo Gravatar::widget(
//                [
//                    'email' => !isset(\Yii::$app->user->identity->profile->gravatar_email) || (\Yii::$app->user->identity->profile->gravatar_email === null)
//                        ? \Yii::$app->user->identity->email
//                        : \Yii::$app->user->identity->profile->gravatar_email,
//                    'options' => [
//                        'alt' => \Yii::$app->user->identity->toString,
//                    ],
//                    'size' => 64,
//                    'width' => 45,
//                    'height' => 45,
//                    'image' => Yii::$app->user->identity->getProfilePicture(['width' => 128, 'height' => 128]),
//                ]
//            ); ?>
        </div>
        <div class="pull-left info">
            <p><?= \Yii::$app->user->identity->toString ?>
            </p>

            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
    </div>
<?php endif;

$developerMenuItems = [];
$modulesMenuItems = [];


// create developer menu, when user is admin
if (Yii::$app->user->identity && Yii::$app->user->can('Authority')) {

    $developerMenuItems = [
        [
            'label' => Yii::t('app', 'Users'),
            'url' => ['/user/admin'],
            'icon' => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Roles & Access'),
            'url' => ['/rbac'],
            'icon' => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Filemanager'),
            'url' => ['/elfinder/manager'],
            'icon' => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Shares'),
            'url' => ['/#'],
            'icon' => 'fa fa-cube',
            'items' => [
                [
                    'label' => Yii::t('app', 'Shares'),
                    'url' => ['/share/flysystem'],
                    'icon' => 'fa fa-cube',
                ],
                [
                    'label' => Yii::t('app', 'Shares to Roles'),
                    'url' => ['/share/flysystem-role'],
                    'icon' => 'fa fa-cube',
                ],
                [
                    'label' => Yii::t('app', 'Shares to Users'),
                    'url' => ['/share/flysystem-user'],
                    'icon' => 'fa fa-cube',
                ],
            ],
        ],
        [
            'label' => Yii::t('app', 'System Information'),
            'url' => ['/backend/system-information'],
            'icon' => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Importer'),
            'url' => ['/#'],
            'icon' => 'fa fa-cube',
            'items' => [
                [
                    'label' => Yii::t('app', 'Import Processes'),
                    'url' => ['/import/import-progress'],
                    'icon' => 'fa fa-cube',
                ],
                [
                    'label' => Yii::t('app', 'Import Logs'),
                    'url' => ['/import'],
                    'icon' => 'fa fa-cube',
                ],
            ],
        ],
        [
            'label' => Yii::t('app', 'Webshell'),
            'url' => ['/webshell'],
            'icon' => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Log Viewer'),
            'url' => ['/logreader'],
            'icon' => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Gii Generator'),
            'url' => ['/gii'],
            'icon' => 'fa fa-arrow-right',
            'template'=> '<a href="{url}" target="_blank">{icon}{label}</a>',
            'visible' => YII_ENV == 'dev' && Yii::$app->hasModule('gii') && Yii::$app->getModule('gii')->checkAccess(),
        ],
        [
            'label' => Yii::t('app', 'Schema Checker'),
            'url' => ['/backend/schema-checker'],
            'icon' => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'History'),
            'url' => ['/backend/history'],
            'icon' => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'Queued Jobs'),
            'url' => ['/queue/queue-job'],
            'icon' => 'fa fa-cube',
        ],
        [
            'label' => Yii::t('app', 'User Parameters'),
            'url' => ['/user-parameter/user-parameter'],
            'icon' => 'fa fa-cube',
            'visible' => Yii::$app->hasModule('user-parameter')
        ],
        [
            'label' => Yii::t('app', 'Adminer'),
            'url' => ['/adminer'],
            'icon' => 'fa fa-arrow-right',
            'template'=> '<a href="{url}" target="_blank">{icon}{label}</a>',
        ],
        [
            'label' => Yii::t('app', 'Translator'),
            'url' => ['/translatemanager'],
            'icon' => 'fa fa-arrow-right',
            'template'=> '<a href="{url}" target="_blank">{icon}{label}</a>',
        ],
        [
            'label' => Yii::t('app', 'Debug'),
            'url' => ['/debug'],
            'icon' => 'fa fa-arrow-right',
            'template'=> '<a href="{url}" target="_blank">{icon}{label}</a>',
        ],
    ];

    $adminMenuItems[] = [
        'url' => '/#',
        'icon' => 'fa fa-cogs',
        'label' => Yii::t('app', 'Developer'),
        'items' => $developerMenuItems,
        'options' => ['class' => 'treeview'],
        'visible' => Yii::$app->user->can('Authority'),
    ];
}

echo Menu::widget(
    [
        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
        'items' => \yii\helpers\ArrayHelper::merge(
                [],
//            \dmstr\modules\pages\models\Tree::getMenuItems('backend', true),
            $adminMenuItems
        ),
    ]
);
?>