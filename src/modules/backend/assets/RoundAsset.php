<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/6/19
 * Time: 12:01 PM
 */

namespace andrej2013\yiiboilerplate\modules\backend\assets;

use yii\web\AssetBundle;
use yii\helpers\FileHelper;

class RoundAsset extends AssetBundle
{
    public $sourcePath = '@andrej2013-boilerplate/modules/backend/assets/web';

    public $css = [
        'less/round.less',
    ];
    public $js = [
    ];
    public $depends = [
//        AdminAsset::class,
    ];

    public function init()
    {
        parent::init();
        // we recompile the less files from 'yii\bootstrap\BootstrapAsset' and include the css in app.css
        // therefore we set bundle to false
        \Yii::$app->getAssetManager()->bundles['yii\bootstrap\BootstrapAsset'] = false;

        // /!\ CSS/LESS development only setting /!\
        // Touch the asset folder with the highest mtime of all contained files
        // This will create a new folder in web/assets for every change and request
        // made to the app assets.
        if (getenv('APP_ASSET_FORCE_PUBLISH')) {
            $files = FileHelper::findFiles(\Yii::getAlias($this->sourcePath));
            $mtimes = [];
            foreach ($files as $file) {
                $mtimes[] = filemtime($file);
            }
            touch(\Yii::getAlias($this->sourcePath), max($mtimes));
        }
    }

}