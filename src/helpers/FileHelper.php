<?php

namespace andrej2013\yiiboilerplate\helpers;

use Exception;
use Yii;

/**
 * Class FileHelper
 * @package andrej2013\yiiboilerplate\helpers
 */
class FileHelper
{
    /**
     * Determine if a file has a specified extension or not.
     * @param $filename
     * @param array $extensions
     */
    public static function hasExtension($filename, $extensions = [])
    {
        $extensions = array_map('strtolower', $extensions);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $extensions);
    }

    /**
     * Encrypt a file
     * @param $filename
     * @param string $cipher
     * @return bool
     * @throws Exception
     */
    public static function encrypt($filename, $cipher = 'aes-128-gcm')
    {
        if (!file_exists($filename)) {
            throw new Exception("File '$filename' not found");
        }

        if (file_exists($filename) and !is_writable($filename)) {
            throw new Exception("File '$filename' is not writable");
        }

        if (!in_array($cipher, openssl_get_cipher_methods())) {
            throw new Exception("Cipher '$cipher' is not supported.");
        }

        $content = file_get_contents($filename);
        if (!$content) {
            throw new Exception("Problem while reading file '$filename'");
        }

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt($content, $cipher, "twRandomKey$2017", 0, $iv);

        $result = file_put_contents($filename, $encrypted);
        if (!$result) {
            throw new Exception("Problem while writing file '$filename'");
        }

        return true;
    }
    /**
     * Encrypt a file
     * @param $filename
     * @param string $cipher
     * @return bool
     * @throws Exception
     */
    public static function decrypt($filename, $cipher = 'aes-128-gcm')
    {
        if (!file_exists($filename)) {
            throw new Exception("File '$filename' not found");
        }

        if (file_exists($filename) and !is_writable($filename)) {
            throw new Exception("File '$filename' is not writable");
        }

        if (!in_array($cipher, openssl_get_cipher_methods())) {
            throw new Exception("Cipher '$cipher' is not supported.");
        }

        $content = file_get_contents($filename);
        if (!$content) {
            throw new Exception("Problem while reading file '$filename'");
        }

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $content = openssl_decrypt($content, $cipher, "twRandomKey$2017", 0, $iv);

        return $content;
    }
}
