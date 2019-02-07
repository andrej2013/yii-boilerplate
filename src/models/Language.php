<?php
/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 6/23/2017
 * Time: 12:13 PM
 */

namespace andrej2013\yiiboilerplate\models;

use Yii;
use \lajax\translatemanager\models\Language as BaseLanguage;

/**
 * This is the base-model class for table "language".
 *
 * @property string $language_id
 * @property string $language
 * @property string $country
 * @property string $name
 * @property string $name_ascii
 * @property integer $status
 *
 */
class Language extends BaseLanguage
{
    /**
     * Auto generated method, that returns a human-readable name as string
     * for this model. This string can be called in foreign dropdown-fields or
     * foreign index-views as a representative value for the current instance.
     * @return String
     */
    public function toString()
    {
        return $this->name_ascii;     // this attribute can be modified
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
     * Returns language objects.
     * @param boolean $active True/False according to the status of the language.
     * @param bool $asArray Return the languages as language object or as 'flat' array
     * @return Language|array
     */
    public static function getLanguages($active = true, $asArray = false)
    {
        if ($active) {
            return Language::find()->where(['status' => static::STATUS_ACTIVE])->asArray($asArray)->all();
        } else {
            return Language::find()->asArray($asArray)->all();
        }
    }
}
