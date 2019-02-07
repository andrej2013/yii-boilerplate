<?php
/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 2/20/2017
 * Time: 9:38 PM
 */

namespace andrej2013\yiiboilerplate\components;

use InvalidArgumentException;

class Sentry
{
    /**
     * Parse Raven/Sentry DSN string and returning array with components
     * @param $dsn
     * @return array
     */
    public static function parseDSN($dsn)
    {
        $url = parse_url($dsn);
        $scheme = (isset($url['scheme']) ? $url['scheme'] : '');
        if (!in_array($scheme, array('http', 'https'))) {
            throw new InvalidArgumentException('Unsupported Sentry DSN scheme: ' .
                (!empty($scheme) ? $scheme : '<not set>'));
        }
        $netloc = (isset($url['host']) ? $url['host'] : null);
        $netloc.= (isset($url['port']) ? ':'.$url['port'] : null);
        $rawpath = (isset($url['path']) ? $url['path'] : null);
        if ($rawpath) {
            $pos = strrpos($rawpath, '/', 1);
            if ($pos !== false) {
                $path = substr($rawpath, 0, $pos);
                $project = substr($rawpath, $pos + 1);
            } else {
                $path = '';
                $project = substr($rawpath, 1);
            }
        } else {
            $project = null;
            $path = '';
        }
        $username = (isset($url['user']) ? $url['user'] : null);
        $password = (isset($url['pass']) ? $url['pass'] : null);
        if (empty($netloc) || empty($project) || empty($username) || empty($password)) {
            throw new InvalidArgumentException('Invalid Sentry DSN: ' . $dsn);
        }

        return array(
            'scheme' => $scheme,
            'server' => $netloc,
            'project'    => $project,
            'public_key' => $username,
            'secret_key' => $password,
        );
    }
}
