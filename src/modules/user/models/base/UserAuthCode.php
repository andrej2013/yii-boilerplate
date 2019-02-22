<?php

namespace andrej2013\yiiboilerplate\modules\user\models\base;

use andrej2013\yiiboilerplate\models\ActiveRecord;
use Yii;
use app\models\ActiveRecord as BaseModel;
use yii\db\Query;

/**
 * This is the base-model class for table "user_auth_code".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $code
 * @property integer $status
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
class UserAuthCode extends ActiveRecord
{

    const ALGO_MD5 = 'md5';
    const ALGO_SHA1 = 'sha1';
    const ALGO_SHA512 = 'sha512';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_auth_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['user_id', 'code'],
                'required'
            ],
            [
                ['user_id', 'status', 'deleted_by'],
                'integer'
            ],
            [
                ['deleted_at'],
                'safe'
            ],
            [
                ['code'],
                'string',
                'max' => 128
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
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'code' => Yii::t('app', 'Code'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
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
        return $this->code; // this attribute can be modified
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
            $query->select('id, code AS text')
                ->from(self::tableName())
                ->andWhere([self::tableName() . '.deleted_at' => null])
                ->andWhere(['like', 'code', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }
}
