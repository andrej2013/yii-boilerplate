<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\user\models;

use Yii;
use \app\models\ActiveRecord;
use yii\base\Exception;
use yii\db\Query;

/**
 * This is the base-model class for table "user_tw_data".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $deleted_by
 * @property string $deleted_at
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property string $toString
 * @property string $entryDetails
 *
 * @property \app\models\User $user
 */
class UserTwData extends ActiveRecord
{

    const TYPE_BOOLEAN = 'checkbox';
    const TYPE_TEXT = 'textarea';
    const TYPE_DEFAULT = 'textInput';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_tw_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['user_id'],
                'required'
            ],
            [
                ['user_id', 'deleted_by'],
                'integer'
            ],
            [
                ['deleted_at'],
                'safe'
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\User::className(),
                'targetAttribute' => ['user_id' => 'id']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'user_id' => Yii::t('user', 'User'),
            'created_by' => Yii::t('user', 'Created By'),
            'created_at' => Yii::t('user', 'Created At'),
            'updated_by' => Yii::t('user', 'Updated By'),
            'updated_at' => Yii::t('user', 'Updated At'),
            'deleted_by' => Yii::t('user', 'Deleted By'),
            'deleted_at' => Yii::t('user', 'Deleted At'),
        ];
    }

    /**
     * Auto generated method, that returns a human-readable name as string
     * for this model. This string can be called in foreign dropdown-fields or
     * foreign index-views as a representative value for the current instance.
     * @return String
     */
    public function toString()
    {
        return $this->user->toString(); // this attribute can be modified
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
        return [
            'user',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(
            \app\models\User::className(),
            ['id' => 'user_id']
        );
    }

    public static function userList($q = null)
    {
        return \app\models\User::filter($q);
    }
    

    /**
     * Return details for dropdown field
     * @return string
     */
    public function getEntryDetails()
    {
        return $this->toString;
    }

    /**
     * @param $string
     * @return $this
     */
    public static function fetchCustomImportLookup($string)
    {
        return self::find()->andWhere([self::tableName() . '.deleted_at' => $string]);
    }

    /**
     * User for filtering results for Select2 element
     * @param null $q
     * @return array
     */
    public static function filter($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query();
            $query->select('id, deleted_at AS text')
                ->from(self::tableName())
                ->andWhere([self::tableName() . '.deleted_at' => null])
                ->andWhere(['like', 'deleted_at', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    /**
     * @param $attribute
     * @return string
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getAttributeType($attribute)
    {
        $column = $this->getTableSchema()->columns[$attribute];
        if (!$column) {
            throw new Exception("Attribute {$attribute} not exist");
        }
        // Checking for boolean
        if ($column->phpType === 'boolean' ||
            $column->dbType === 'tinyint(1)' ||
            substr($column->name, 0, 3) == 'is_' ||
            substr($column->name, 0, 4) == 'has_'
        ) {
            return self::TYPE_BOOLEAN;
        } elseif ($column->type === 'text') { // Checking for textarea
            return self::TYPE_TEXT;
        }
        return self::TYPE_DEFAULT;
    }
}
