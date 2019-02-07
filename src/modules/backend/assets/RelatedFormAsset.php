<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\backend\assets;

/*
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use yii\bootstrap\BootstrapAsset;
use yii\helpers\FileHelper;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Configuration for `backend` client script files.
 *
 * @since 4.0
 */
class RelatedFormAsset extends AssetBundle
{
    public $sourcePath = '@andrej2013-boilerplate/modules/backend/assets/web';

    public $js = [
        'js/related_form_modal.js',
    ];

    public $css = [
    ];

    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
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
