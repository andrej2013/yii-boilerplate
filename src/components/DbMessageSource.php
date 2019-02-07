<?php

namespace andrej2013\yiiboilerplate\components;

use lajax\translatemanager\models\Language;
use yii\i18n\DbMessageSource as BaseClass;

/**
 * Class DbMessageSource
 * @package andrej2013\yiiboilerplate\components
 */
class DbMessageSource extends BaseClass
{
    public static $languageMapper = [];
    public $basePath;
    /**
     * Translates the specified message.
     * If the message is not found, a [[EVENT_MISSING_TRANSLATION|missingTranslation]] event will be triggered.
     * If there is an event handler, it may provide a [[MissingTranslationEvent::$translatedMessage|fallback translation]].
     * If no fallback translation is provided this method will return `false`.
     * @param string $category the category that the message belongs to.
     * @param string $message the message to be translated.
     * @param string $language the target language.
     * @return string|boolean the translated message or false if translation wasn't found.
     */
    protected function translateMessage($category, $message, $language)
    {
        // Does this language have another mapping?
        if (!isset(self::$languageMapper[$language])) {
            if (strpos($language, '-') !== false) { // There is a -, no mapping needed
                self::$languageMapper[$language] = $language;
            } else {
                $languageModel = Language::findOne(['language' => $language, 'status' => 1]);
                if ($languageModel) {
                    self::$languageMapper[$language] = $languageModel->language_id;
                }
            }
        }
        $languageId = self::$languageMapper[$language];

        return parent::translateMessage($category, $message, $languageId);
    }
}