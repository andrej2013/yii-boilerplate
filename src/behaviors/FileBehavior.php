<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/22/19
 * Time: 12:54 PM
 */

namespace andrej2013\yiiboilerplate\behaviors;

use andrej2013\yiiboilerplate\models\File;
use nemmo\attachments\behaviors\FileBehavior as BaseFileBehavior;
use yii\helpers\FileHelper;
use yii\helpers\Url;

class FileBehavior extends BaseFileBehavior
{
    /**
     * @return File[]
     * @throws \Exception
     */
    public function getFiles()
    {
        $fileQuery = File::find()
                         ->where([
                             'itemId' => $this->owner->id,
                             'model'  => $this->getModule()
                                              ->getShortClass($this->owner),
                         ]);
        $fileQuery->orderBy(['id' => SORT_ASC]);

        return $fileQuery->all();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getInitialPreview()
    {
        $initialPreview = [];

        $userTempDir = $this->getModule()
                            ->getUserDirPath();
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            $initialPreview[] = Url::to(['/attachments/file/download-temp', 'filename' => basename($file)]);
        }

        foreach ($this->getFiles() as $file) {
            $initialPreview[] = Url::to($file->getUrl(true));
        }

        return $initialPreview;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getInitialPreviewConfig()
    {
        $initialPreviewConfig = [];

        $userTempDir = $this->getModule()
                            ->getUserDirPath();
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            $filename = basename($file);
            if (substr(FileHelper::getMimeType($file), 0, 5) === 'image') {
                $initialPreviewConfig[] = [
                    'caption' => $filename,
                    'url'     => Url::to([
                        '/attachments/file/delete-temp',
                        'filename' => $filename,
                    ]),
                ];
            }
        }

        foreach ($this->getFiles() as $index => $file) {
            $options = [
                'caption'     => "$file->name.$file->type",
                'url'         => Url::toRoute([
                    '/attachments/file/delete',
                    'id' => $file->id,
                ]),
                'key'         => $file->id,
                'downloadUrl' => $file->getUrl(true),
                'showDrag'    => false,
            ];
            $mime = explode('/', $file->mime);
            if ($mime[0] == 'application' || $mime[0] == 'text') {
                switch ($mime[1]) {
                    case 'pdf':
                        $options += [
                            'type' => 'pdf',
                        ];
                        break;
                    case 'vnd.ms-excel':
                    case 'vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    case 'msword':
                    case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
                        $options += [
                            'type' => 'office',
                        ];
                        break;
                    case 'text':
                        $options += [
                            'type' => 'text',
                        ];
                        break;
                    case 'html':
                        $options += [
                            'type' => 'html',
                        ];
                        break;
                }
            }
            $options += [
                'size' => file_exists($file->getPath()) ? filesize($file->getPath()) : null,
            ];
            $initialPreviewConfig[] = $options;
        }

        return $initialPreviewConfig;
    }

}