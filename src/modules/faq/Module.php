<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\faq;
use yii\base\InvalidParamException;
use yii\web\NotFoundHttpException;

/**
 * Class Module.
 *
 * @author Tobias Munk <tobias@diemeisterei.de>
 */
class Module extends \yii\base\Module
{
    /**
     * @var string The controller namespace to use
     */
    public $controllerNamespace = 'andrej2013\yiiboilerplate\modules\faq\controllers';

    /**
     * @var string source language for translation
     */
    public $sourceLanguage = 'en-US';

    /**
     * @var null|array The roles which have access to module controllers, eg. ['admin']. If set to `null`, there is no accessFilter applied
     */
    public $accessRoles = null;

    /**
     * @var string Directory URL address, where images files are stored. 'http://my_site_name/upload/faq/'
     */
    public $imagesUrl = '';

    /**
     * @var string Alias or absolute path to directory where images files are stored. '@frontend/web/upload/faq/'
     */
    public $imagesPath =  '';

    /**
     * @var string Language selector for ImperaviWidget
     */
    public $imperaviLanguage = 'en';


    /**
     * Init module
     */
    public function init()
    {
        parent::init();
    }
}
