<?php

namespace andrej2013\yiiboilerplate\helpers;

use yii\helpers\VarDumper;
use Yii;

/**
 * Class DebugHelper
 *
 * Add static functions here to help with debugging, but don't commit code using these functions!
 * Debugging and error handling for the frontend user should be done somewhere else.
 * @package andrej2013\yiiboilerplate\helpers
 */
class DebugHelper
{
    /**
     * Dump & Die made sexy.
     * @param $data
     */
    public static function dd($data)
    {
        self::dump($data);
        Yii::$app->end();
    }

    /**
     * Wrap var_dump around a pre tag to make it readable.
     * @param $data
     */
    public static function pre($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
    
    /**
     * Sexy dump with depth and highlighting control
     * @param $data
     */
    public static function dump($data)
    {
        VarDumper::dump($data, 10, true);
    }
}
