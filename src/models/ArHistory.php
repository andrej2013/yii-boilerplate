<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/19/2017
 * Time: 12:30 PM
 */

namespace andrej2013\yiiboilerplate\models;

use Yii;
use yii\db\ActiveRecord;
use andrej2013\yiiboilerplate\behaviors\History;

class ArHistory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'arhistory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table_name', 'row_id', 'event', 'created_at'], 'required'],
            [['row_id', 'event', 'created_at', 'created_by'], 'integer'],
            [['old_value', 'new_value'], 'string'],
            [['table_name', 'field_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'table_name' => Yii::t('app', 'Table Name'),
            'row_id' => Yii::t('app', 'Row ID'),
            'event' => Yii::t('app', 'Event'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'field_name' => Yii::t('app', 'Field Name'),
            'old_value' => Yii::t('app', 'Old Value'),
            'new_value' => Yii::t('app', 'New Value'),
        ];
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        switch ($this->event) {
            case History::EVENT_INSERT:
                return Yii::t('app', "Create");
                break;
            case History::EVENT_UPDATE:
                return Yii::t('app', "Update");
            case History::EVENT_DELETE:
                return Yii::t('app', "Delete");
            default:
                return "";
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        $user = User::findOne($this->created_by);
        if ($user) {
            return $user->toString;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(
            \app\models\User::className(),
            ['id' => 'created_by']
        );
    }
}
