<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\faq\models\base;

use andrej2013\yiiboilerplate\models\Language;
use app\models\User;
use Yii;
use \app\models\ActiveRecord;
use yii\db\Query;

/**
 * This is the base-model class for table "faq".
 *
 * @property integer                                  $id
 * @property string                                   $title
 * @property string                                   $content
 * @property string                                   $language_id
 * @property string                                   $place
 * @property integer                                  $deleted_by
 * @property string                                   $deleted_at
 * @property integer                                  $level
 * @property integer                                  $order
 * @property integer                                  $created_by
 * @property string                                   $created_at
 * @property integer                                  $updated_by
 * @property string                                   $updated_at
 * @property string                                   $toString
 * @property string                                   $entryDetails
 *
 * @property \andrej2013\yiiboilerplate\models\Language $language
 */
class Faq extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const PLACE_BACKEND = 'backend';
    const PLACE_FRONTEND = 'frontend';
    const ROOT_LEVEL = 0;
    var $enum_labels = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'faq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'language_id', 'place', 'level'], 'required'],
            [['content', 'place'], 'string'],
            [['deleted_by', 'level', 'order'], 'integer'],
            [['deleted_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['language_id'], 'string', 'max' => 5],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'language_id']],
            ['place', 'in', 'range' => [
                self::PLACE_BACKEND,
                self::PLACE_FRONTEND,
            ]
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
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'language_id' => Yii::t('app', 'Language'),
            'place' => Yii::t('app', 'Place'),
            'level' => Yii::t('app', 'Level'),
            'order' => Yii::t('app', 'Order'),
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
        return $this->title;     // this attribute can be modified
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
            'language',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['language_id' => 'language_id']);
    }

    public static function LanguageList($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $entries = Language::find()->limit(20)->all();
            $query = new Query();
            $query->select('id, created_at AS text')
                ->from(Language::tableName())
                ->where(['like', 'created_at', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::find($id)->name];
        }
        return $out;
    }


    /**
     * Return details for dropdown field
     * @return string
     */
    public function getEntryDetails()
    {
        return '';
    }

    public function getLevelName()
    {
        $level = self::find()->andWhere(['id' => $this->level])->one();
        if ($level)
            return $level->toString;
        return Yii::t('app', 'Root');
    }

    public static function FaqLevelList($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query();
            $query->select('id, title AS text')
                ->from(self::tableName())
                ->andWhere(['level' => self::ROOT_LEVEL])
                ->andWhere(['like', 'title', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::find($id)->name];
        }
        return $out;
    }

}
