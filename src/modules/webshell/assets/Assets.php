<?php
namespace andrej2013\yiiboilerplate\modules\webshell\assets;

use yii\web\AssetBundle;

/**
 * WebshellAsset is an asset bundle used to include custom overrides for terminal into the page.
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class Assets extends AssetBundle
{
    public $sourcePath = '@andrej2013-boilerplate/modules/webshell/assets/web/css';

    public $css = [
        'webshell.css',
    ];

    public $depends = [
        'samdark\webshell\JqueryTerminalAsset',
    ];
}
