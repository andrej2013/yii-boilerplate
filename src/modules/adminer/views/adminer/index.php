<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/25/2017
 * Time: 12:53 PM
 */

// Adminer Extension
$_GET["username"] = getenv('DATABASE_USER');
$_GET["db"] = getenv('DB_ENV_MYSQL_DATABASE');
$_GET['password'] = getenv('DATABASE_PASSWORD');
function adminer_object()
{
    class AdminerSoftware extends Adminer
    {
        public function credentials()
        {
            return [getenv('DB_PORT_3306_TCP_ADDR'), getenv('DATABASE_USER'), getenv('DATABASE_PASSWORD')];
        }

        public function database()
        {
            return getenv('DB_ENV_MYSQL_DATABASE');
        }

        function login($login, $password)
        {
            return true;
        }
    }

    return new AdminerSoftware;
}

require('adminer.php');