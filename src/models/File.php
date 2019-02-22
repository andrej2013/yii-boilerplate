<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/22/19
 * Time: 12:55 PM
 */

namespace andrej2013\yiiboilerplate\models;

use nemmo\attachments\models\File as BaseFile;
use yii\helpers\Url;

class File extends BaseFile
{

    /**
     * @param bool $schema
     * @return string
     */
    public function getUrl($schema = false)
    {
        return Url::to(['/attachments/file/download', 'id' => $this->id], $schema);
    }

}