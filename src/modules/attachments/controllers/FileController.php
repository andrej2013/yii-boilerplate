<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/22/19
 * Time: 2:53 PM
 */

namespace andrej2013\yiiboilerplate\modules\attachments\controllers;

use nemmo\attachments\controllers\FileController as BaseController;
use andrej2013\yiiboilerplate\models\File;

class FileController extends BaseController
{
    
    public function actionDownload($id)
    {
        $file = File::findOne(['id' => $id]);
        $filePath = $this->getModule()->getFilesDirPath($file->hash) . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;
        return \Yii::$app->response->sendFile($filePath, "$file->name.$file->type", ['inline' => true]);
    }
}