<?php
namespace andrej2013\yiiboilerplate\rest;
use Yii;

/**
 * Created by PhpStorm.
 * User: ben-g
 * Date: 05.02.2016
 * Time: 12:55
 */
class Token extends \dektrium\user\models\Token
{
    /**
     * @var module v1
     */
    protected $module;
    
    /** @inheritdoc */
    public function init()
    {
        $this->module = Yii::$app->getModule('v1');
    }

}