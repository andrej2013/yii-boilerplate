<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 7/25/2017
 * Time: 12:46 PM
 */

namespace andrej2013\yiiboilerplate\modules\user\models\twoway;

interface ProviderInterface
{

    public function getCode();

    public function resendCode();

    public function checkCode();

}