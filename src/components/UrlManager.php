<?php

namespace andrej2013\yiiboilerplate\components;

use lajax\translatemanager\models\Language;
use Yii;
use codemix\localeurls\UrlManager as BaseUrlManager;

/**
 * Class UrlManager
 * @package andrej2013\yiiboilerplate
 */
class UrlManager extends BaseUrlManager
{
    public function getLanguagesForDropdown()
    {
        $languages = [];
        $currentUrl = Yii::$app->request->url;
        $currentLang = Yii::$app->language;

        foreach ($this->languages as $language) {
            if ($language != Yii::$app->language) {
                $url = str_replace("/$currentLang/", "/$language/", $currentUrl);

                // If we're on the index page, needs to be a different rule
                if ($currentUrl == "/$currentLang") {
                    $url = str_replace("/$currentLang", "/$language", $currentUrl);
                }

                $languages[] = [
                    'label' => $language,
                    'url' => $url
                ];
            }
        }

        return $languages;
    }

    /**
     * Checks for a language or locale parameter in the URL and rewrites the pathInfo if found.
     * If no parameter is found it will try to detect the language from persistent storage (session /
     * cookie) or from browser settings.
     *
     * @var \yii\web\Request $request
     */
    protected function processLocaleUrl($request)
    {
        parent::processLocaleUrl($request);

        // If not redirected by parent function, reset locale of formatter
        if (Yii::$app->has('formatter')) {
            Yii::$app->formatter->locale = Yii::$app->language;

            // Get the language id
            /*$lang = Language::findOne(['language' => Yii::$app->language, 'status' => 1]);
            if ($lang) {
                Yii::$app->formatter->locale = $lang->language_id;
            }*/
        }

        return;
    }
    
    public static function getFlag($language = null)
    {
        $language = $language??Yii::$app->language;
        switch ($language) {
            case 'en':
                $language = 'gb';
                break;
            case 'sr':
                $language = 'rs';
                break;
            case 'sl':
                $language = 'si';
                break;
        }
        return $language;
    }
}