<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/10/2017
 * Time: 1:47 PM
 */
/**
 * @property string $picture
 */
namespace andrej2013\yiiboilerplate\modules\user\models;

use andrej2013\yiiboilerplate\traits\UploadTrait;

class Profile extends \dektrium\user\models\Profile
{

    /**
     * We can upload images and files to this model, so we need our helper trait.
     */
    use UploadTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['picture'], 'string', 'max' => 255],
        ]);
    }

    /**
     * Load data into object
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        $scope = $formName === null ? $this->formName() : $formName;

        // Don't update these fields as they are handled separately.
        unset($data[$scope]['picture']);

        return parent::load($data);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'picture' => \Yii::t('app', 'Picture')
        ]);
    }

    /**
     * Get unique upload path
     * @return string
     */
    protected function getUploadPath()
    {
        $reflect = new \ReflectionClass($this);
        return $this->uploadPath . $reflect->getShortName() . '/' . $this->user_id . '/';
    }


}