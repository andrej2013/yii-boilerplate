<?php
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
namespace andrej2013\yiiboilerplate\modules\backend\assets;

use andrej2013\yiiboilerplate\modules\backend\assets\AdminAsset;
use yii\helpers\FileHelper;
use yii\web\AssetBundle;

/**
 * Configuration for `backend` client script files.
 *
 * @since 4.0
 */
class SwitchAsset extends AssetBundle
{
    public $sourcePath = '@vendor/andrej2013/yii-boilerplate/src/modules/backend/assets/web';

    public $css = [
    ];

    public $js = [
        'js/switch.js',
    ];

    // we recompile the less files from 'yii\bootstrap\BootstrapAsset' and include the css in app.css
    // therefore we set bundle to false in application config
    public $depends = [
        AdminAsset::class,
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
