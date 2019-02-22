<?php

namespace andrej2013\yiiboilerplate\models;

use Yii;
use \app\models\ActiveRecord;

/**
 * This is the base-model class for table "user_auth_log".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $date
 * @property integer $cookie_based
 * @property integer $duration
 * @property string  $error
 * @property string  $ip
 * @property string  $host
 * @property string  $url
 * @property string  $user_agent
 * @property integer $deleted_by
 * @property string  $deleted_at
 * @property integer $created_by
 * @property string  $created_at
 * @property integer $updated_by
 * @property string  $updated_at
 * @property string  $toString
 */
class UserAuthLog extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_auth_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['user_id', 'date', 'cookie_based', 'duration', 'deleted_by'],
                'integer',
            ],
            [
                ['deleted_at'],
                'safe',
            ],
            [
                ['error', 'ip', 'host', 'url', 'user_agent'],
                'string',
                'max' => 255,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => Yii::t('app', 'ID'),
            'user_id'      => Yii::t('app', 'User'),
            'date'         => Yii::t('app', 'Date'),
            'cookie_based' => Yii::t('app', 'Cookie Based'),
            'duration'     => Yii::t('app', 'Duration'),
            'error'        => Yii::t('app', 'Error'),
            'ip'           => Yii::t('app', 'Ip'),
            'host'         => Yii::t('app', 'Host'),
            'url'          => Yii::t('app', 'Url'),
            'user_agent'   => Yii::t('app', 'User Agent'),
            'created_by'   => Yii::t('app', 'Created By'),
            'created_at'   => Yii::t('app', 'Created At'),
            'updated_by'   => Yii::t('app', 'Updated By'),
            'updated_at'   => Yii::t('app', 'Updated At'),
            'deleted_by'   => Yii::t('app', 'Deleted By'),
            'deleted_at'   => Yii::t('app', 'Deleted At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributePlaceholders()
    {
        return [];
    }


    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [];
    }

    /**
     * Auto generated method, that returns a human-readable name as string
     * for this model. This string can be called in foreign dropdown-fields or
     * foreign index-views as a representative value for the current instance.
     *
     * @return String
     */
    public function toString()
    {
        return $this->user_id; // this attribute can be modified
    }


    /**
     * Getter for toString() function
     * @return String
     */
    public function getToString()
    {
        return $this->toString();
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return [];
    }

    public function deletable()
    {
        return false;
    }
    
    public function editable()
    {
        return false;
    }
}
