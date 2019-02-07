<?php

namespace andrej2013\yiiboilerplate\traits;

use app\components\Helper;
use andrej2013\yiiboilerplate\helpers\DebugHelper;
use Yii;
use Exception;
use ReflectionClass;
use yii\caching\TagDependency;

trait UploadTrait
{
    /**
     * @var string
     */
    protected $uploadPath = '/uploads/';

    /**
     * Get the uploaded file url from the cloud storage (for images it gets the processed url)
     * @param $attribute
     * @param null $options
     * @param bool $publish
     * @return mixed
     * @throws Exception
     */
    public function getFileUrl($attribute, $options = null, $publish = false)
    {
        if (empty($attribute) || !$this->hasAttribute($attribute)) {
            throw new Exception("getFileUrl: Unknown attribute " . $attribute . " on model " . get_class($this));
        }

        if (empty($this->$attribute)) {
            return null;
        }

        $path = $this->getUploadPath() . $this->$attribute;

        if ($publish) {
            if ($this->getFileType($attribute) == 'image') {
                return Helper::getImageUrl($path, $options);
            } else {
                return Helper::getPublicFileUrl($path);
            }
        }
        return Helper::getFile("elfinder", "fs", $path);
    }

    /**
     * Get file type for sending to upload preview widget
     * @param $attribute
     * @return string
     * @throws Exception
     */
    public function getFileType($attribute)
    {
        if (empty($attribute) || !$this->hasAttribute($attribute)) {
            throw new Exception("getFileUrl: Unknown attribute " . $attribute . " on model " . get_class($this));
        }
        $path = $this->getUploadPath() . $this->$attribute;

        // Check cache
        $cacheKey = md5($path . '_mime');
        $mimeType = Yii::$app->fs_cache->get($cacheKey);

        if ($mimeType === false) {
            $mimeType = '';
            if (Yii::$app->get('fs')->has($path)) {
                switch (Yii::$app->get('fs')->getMimetype($path)) {
                    case 'application/pdf':
                        $mimeType = 'pdf';
                        break;
                    case 'image/png':
                    case 'image/jpg':
                    case 'image/jpeg':
                        $mimeType = 'image';
                        break;
                    default:
                        $mimeType = 'other';
                }
            }

            if ($mimeType != '') {
                // Build dependency key (changed on upload of new file)
                $this->setFileTypeCache($cacheKey, $mimeType, $attribute);
            }
        }
        return $mimeType;
    }

    /**
     * Set the file type cache
     * @param $cacheKey
     * @param $mimeType
     * @param $attribute
     */
    protected function setFileTypeCache($cacheKey, $mimeType, $attribute)
    {
        $path = $this->getUploadPath() . $this->$attribute;
        $dependencyKey = md5($path . '_timestamp');

        Yii::$app->fs_cache->set($cacheKey, $mimeType, 0, new TagDependency(['tags' => $dependencyKey]));
    }

    /**
     * Upload an uploaded file to the cloud storage.
     * @param $attribute
     * @param null $filePath
     * @param string $language Language to where to lookup in form scope for -1 for deleting
     * @throws Exception
     */
    public function uploadFile($attribute, $filePath, $fileName, $language = null)
    {
        $fileName[$attribute] = Helper::cleanFileName($fileName[$attribute]);
        if (empty($attribute) ||  !$this->hasAttribute($attribute)) {
            throw new Exception("uploadFile: Unknown attribute " . $attribute . " on model " . get_class($this));
        }

        // No file uploaded
        if (empty($filePath[$attribute])) {
            // Get form scope based on language if passed as argument, so we know where to lookup if file is removed
            $formScope = $language === null ?
                $_POST[$this->formName()] :
                $_POST[$this->formName()][$language][$this->formName()];
            if ($formScope[$attribute] == -1) {
                $this->deleteFile($attribute);
                $this->$attribute = null;
                $this->save(false);
            }
            return false;
        }

        // Make sure the file was uploaded.
        if (!is_uploaded_file($filePath[$attribute])) {
            DebugHelper::dd($filePath);
            return false; // nothing to change.
        }

        // Get the local path to move the file from tmp status
        @mkdir(Yii::getAlias('@runtime') . '/upload');
        $newPath = Yii::getAlias('@runtime') . '/upload/' . $fileName[$attribute];
        move_uploaded_file($filePath[$attribute], $newPath);

        // Write the file to the filesystem.
        $stream = fopen($newPath, 'r+');
        Yii::$app->fs->putStream($this->getUploadPath() . $fileName[$attribute], $stream);

        // If the new file doesn't equal the old one, delete the old one.
        if (!empty($this->$attribute) && $this->$attribute != $fileName[$attribute]) {
            $this->deleteFile($attribute);
        }

        // Invalidate related tags
        $path = $this->getUploadPath() . $fileName[$attribute];
        $cacheKey = md5($path . '_timestamp');
        TagDependency::invalidate(Yii::$app->getCache(), $cacheKey);
        Yii::$app->fs_cache->delete($cacheKey);

        // Precache mimetype and timestamp
        $this->getFileType($attribute);
        Helper::getFileCachedModificationTime($cacheKey, $path);

        // Save the changes. Add the time() to the file for properly refreshing the cache in case the same file is
        // uploaded again but with some minor corrections in it. This also handles the mobile app sync to trigger a new
        // download of the file.
        $this->$attribute = time() . '_' . $fileName[$attribute];
        $this->$attribute = $fileName[$attribute];
        $this->save(false);

        return true;
    }

    /**
     * Get unique upload path
     * @return string
     */
    protected function getUploadPath()
    {
        $reflect = new ReflectionClass($this);
        return $this->uploadPath . $reflect->getShortName() . '/' . $this->id . '/';
    }

    /**
     * Delete previous file of model from server
     */
    protected function deleteFile($attribute)
    {
        try {
            Yii::$app->fs->delete($this->getUploadPath() . $this->$attribute);
        } catch (Exception $e) {
            // If the file couldn't be remove, silence the error.
        }
    }
}
