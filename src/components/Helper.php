<?php

namespace andrej2013\yiiboilerplate\components;

/*
 * @link http://www.diemeisterei.de/
 *
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Yii;
use Exception;
use dektrium\user\models\User as UserModel;
use andrej2013\yiiboilerplate\helpers\DebugHelper;
use yii\base\Component;
use yii\caching\TagDependency;
use yii\helpers\Html;
use yii\imagine\Image;

class Helper extends Component
{

    /**
     * @return string
     */
    public static function version()
    {
        return getenv('APP_TITLE') . ' ' . APP_VERSION . ' (' . APP_HASH . ')';
    }

    /**
     * check Application
     */
    public static function checkApplication()
    {
        if (\Yii::$app->user->can('Admin')) {
            self::checkPassword(getenv('APP_ADMIN_PASSWORD'));
        }
    }

    /**
     * Password check
     * Based upon http://stackoverflow.com/a/10753064.
     *
     * @param $pwd
     */
    private static function checkPassword($pwd)
    {
        $errors = [];

        if (strlen($pwd) < 8) {
            $errors[] = 'Password too short!';
        }

        if (!preg_match('#[0-9]+#', $pwd)) {
            $errors[] = 'Password must include at least one number!';
        }

        if (!preg_match('#[a-zA-Z]+#', $pwd)) {
            $errors[] = 'Password must include at least one letter!';
        }

        if (count($errors) > 0) {
            $msg = implode('<br/>', $errors);
            \Yii::$app->session->addFlash(
                'danger',
                "Application admin password from environment setting is not strong enough.<br/><i>{$msg}</i>"
            );
        }
    }

    /**
     * get public url of file by path
     * @param $component
     * @param $fileSystem
     * @param $path
     * @return string
     */
    public static function getFile($component, $fileSystem, $path)
    {
        if (isset(\Yii::$app->controllerMap[$component]) && \Yii::$app->controllerMap[$component]['roots']) {
            foreach (\Yii::$app->controllerMap[$component]['roots'] as $key => $root) {
                if ($root['component'] === $fileSystem) {
                    $hash = rtrim(strtr(base64_encode($path), '+/=', '-_.'), '.');
                    $volume = 'fls' . ($key + 1) . '_' . $hash;
                    return '/' . $component . '/connect?cmd=file&target=' . $volume;
                }
            }
        }
    }

    /**
     * publish a file to public bucket
     * @param $path
     * @param string $bucket
     * @return string
     */
    public static function getPublicFileUrl($path, $bucket = 'fs')
    {
        // calculate cache
        $modification_time = Yii::$app->$bucket->getTimestamp($path);
        $cache_key = md5(serialize(compact('modification_time', 'path', 'bucket')));
        $cache_path = dirname($path) . '/' . $cache_key . '/' . basename($path);

        // read from cache
        if (!Yii::$app->fs_assetsprod->has($cache_path)) {
            // load
            $cache_file_content = Yii::$app->$bucket->read($path);
            Yii::$app->fs_assetsprod->put($cache_path, $cache_file_content);
        }

        return self::getPublicUrl($path, $cache_path);
    }

    /**
     * get public url depending on CDN setting
     * @param $path
     * @param $cache_path
     * @return string
     */
    protected static function getPublicUrl($path, $cache_path)
    {
        if (getenv('CDN_ENABLED')) {
            $url = getenv('CDN_URL') . implode('/', array_map('rawurlencode', explode('/', $cache_path)));
        } else {
            $url = '/site/image?path=' . implode('/', array_map('rawurlencode', explode('/', $path)));
        }
        return $url;
    }

    /**
     * get public url of an image with optional CDN prefix
     * @param $path
     * @param null $options
     * @return string
     */
    public static function getImageUrl($path, $options = null)
    {
        $options_part = '';
        if ($options) {
            foreach ($options as $key => $value) {
                $options_part .= '&' . $key . '=' . $value;
            }
        }

        $cache_path = self::processImage($path, $options);

        return self::getPublicUrl($path . $options_part, $cache_path);
    }

    /**
     * process image with options and show it or return its cache path
     * @param $path
     * @param array $options
     * @param bool $show
     * @return bool|string
     */
    public static function processImage($path, $options = [], $show = false)
    {
        // defaults
        $bucket = 'fs'; // filesystem component: fs, fs_assetsprod, ...
        $width = 1920;
        $height = null; // if only one of widht|height is set, the other will be calculated by original ratio
        $format = null;
        $quality = 80;

        // handle options
        if (strpos($path, '/') === 0) $path = substr($path, 1);
        if (isset($options['bucket'])) $bucket = $options['bucket'];
        if (isset($options['width'])) $width = $options['width'];
        if (isset($options['height'])) $height = $options['height'];
        if (isset($options['format'])) $format = $options['format'];

        $original_image_path = $path;
        $asset_image_path = 'image-cache/' . $path;

        $ext = pathinfo($original_image_path, PATHINFO_EXTENSION);

        // convert file format
        if ($format) {
            $asset_image_path = str_replace($ext, $format, $asset_image_path);
        } // set original file format
        else {
            $format = $ext;
        }

        // Add a slash at the beginning of the url to help get the proper file
        $modificationCachePath = $original_image_path;
        if ($modificationCachePath[0] != '/') {
            $modificationCachePath = '/' . $modificationCachePath;
        }

        // Get the last timestamp of the picture
        $modificationCacheKey = md5($modificationCachePath . '_timestamp');
        $modification_time = self::getFileCachedModificationTime($modificationCacheKey, $original_image_path, $bucket);

        $cache_key = md5(serialize(compact('modification_time', 'path', 'bucket', 'width', 'height', 'format', 'quality')));
        $cache_path = dirname($asset_image_path) . '/' . $cache_key . '/' . basename($asset_image_path);

        // Check if image is cached (either in the local cache or on the prod assets server)
        $hasCacheKey = md5($cache_path . '_cachepath');
        $hasCache = Yii::$app->fs_cache->get($hasCacheKey);
        if ($hasCache === false) {
            $hasCache = Yii::$app->fs_assetsprod->has($cache_path);
            Yii::$app->fs_cache->set($hasCacheKey, $hasCache, 0, new TagDependency(['tags' => $modificationCacheKey]));
        }

        // read from cache
        if ($show && $hasCache) {
            // load
            $cache_image_content = Yii::$app->fs_assetsprod->read($cache_path);
            $image = Image::getImagine()->load($cache_image_content);
        } elseif (!$hasCache && ($width || $height) || $show) {
            // Only process the image if we are resizing it and it isn't cached
            if (!Yii::$app->$bucket->has($original_image_path)) {
                return false;
            }

            // Read from cache and write it localy (needed for resizing)
            @mkdir(Yii::getAlias('@runtime') . '/cache');
            $tmp_path = Yii::getAlias('@runtime') . '/cache/' . md5($original_image_path);
            file_put_contents($tmp_path, Yii::$app->$bucket->read($original_image_path));

            // resize
            $image = Image::thumbnail($tmp_path, $width, $height); // Todo: mode?

            // cleanup tmp file
            if (file_exists($tmp_path)) {
                unlink($tmp_path);
            }

            // save image to the cache
            if ($image) {
                Yii::$app->fs_assetsprod->put($cache_path, $image->get($format, ['quality' => $quality]));
            }
        }

        // Output image if we have it and it's required
        if ($image && $show) {
            $image->show($format, ['quality' => $quality]);
        }
        return $cache_path;
    }

    /**
     * Get (or sets) the modification time from the s3 and saves it in cache for future reference
     * @param $key
     * @param $path
     * @param string $bucket
     * @return mixed
     */
    public static function getFileCachedModificationTime($key, $path, $bucket = 'fs')
    {
        $modificationTime = Yii::$app->fs_cache->get($key);
        if ($modificationTime === false) {
            try {
                $modificationTime = Yii::$app->$bucket->getTimestamp($path);
                self::setFileCachedModificationTime($key, $modificationTime);
            } catch (Exception $e) {
                $modificationTime = 0;
            }
        }
        return $modificationTime;
    }

    /**
     * Set the cached modification time for future reference
     * @param $key
     * @param $modificationTime
     */
    public static function setFileCachedModificationTime($key, $modificationTime)
    {
        Yii::$app->fs_cache->set($key, $modificationTime);
    }

    /**
     * Remove special charcters from string (filename) so they could be saved safe in every storage component
     * @param string $fileName
     * @return string
     */
    public static function cleanFileName($fileName)
    {
        // Removes special chars leaving only letters and number and -_.,+ .
        return preg_replace('/[^A-Za-z0-9\-\.\+\_\s+]/', '', $fileName);
    }

    /**
     * @param $format
     * @return string
     */
    public static function convertMomentToPHFormat($format)
    {
        $replacements = [
            'DD' => 'd',
            'MM' => 'm',
            'YYYY' => 'Y',
            'H' => 'H',
            'mm' => 'i',
            'ss' => 's',
        ];
        $phpFormat = strtr($format, $replacements);
        return $phpFormat;
    }
    /**
     * @param $format
     * @return string
     */
    public static function convertMomentToPHPFormat($format)
    {
        $replacements = [
            'DD' => 'd',
            'MM' => 'm',
            'YYYY' => 'Y',
            'H' => 'H',
            'mm' => 'i',
            'ss' => 's',
        ];
        $phpFormat = strtr($format, $replacements);
        return $phpFormat;
    }
}
