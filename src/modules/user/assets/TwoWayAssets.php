<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */
namespace andrej2013\yiiboilerplate\modules\user\assets;

use yii\helpers\FileHelper;
use yii\web\AssetBundle;

/**
 * Configuration for `backend` client script files.
 *
 * @since 4.0
 */
class TwoWayAssets extends AssetBundle
{
    public $sourcePath = '@vendor/andrej2013/yii-boilerplate/src/modules/user/assets/web';

    public $css = [
//        'css/login.css',
    ];

    public $js = [
        'js/two-way.js',
    ];

    // we recompile the less files from 'yii\bootstrap\BootstrapAsset' and include the css in app.css
    // therefore we set bundle to false in application config
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\YiiAsset',
    ];

    public function init()
    {
        parent::init();

        // /!\ CSS/LESS development only setting /!\
        // Touch the asset folder with the highest mtime of all contained files
        // This will create a new folder in web/assets for every change and request
        // made to the app assets.
        if (getenv('APP_ASSET_FORCE_PUBLISH')) {
            $path = \Yii::getAlias($this->sourcePath);
            $files = FileHelper::findFiles($path);
            $mtimes = [];
            foreach ($files as $file) {
                $mtimes[] = filemtime($file);
            }
            touch($path, max($mtimes));
        }
    }
}
