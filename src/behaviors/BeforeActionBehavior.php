<?php

namespace andrej2013\yiiboilerplate\behaviors;

use andrej2013\yiiboilerplate\helpers\DebugHelper;
use Yii;
use yii\base\Behavior;
use yii\web\Application;

/**
 * Class BeforeActionBehavior
 * @package app\components
 */
class BeforeActionBehavior extends Behavior
{
    /**
     * @return array
     */
    public function events()
    {
        return [
            Application::EVENT_BEFORE_ACTION => 'run'
        ];
    }

    /**
     * @param $event
     * @return bool
     */
    public function run($event)
    {
        // Do we have themes?
        if (!empty(Yii::$app->params['themes'])) {
            // Get the theme from the cookie (vs session?')
            if (Yii::$app->getRequest()->cookies['applicationTheme']) {
                $theme = Yii::$app->getRequest()->cookies->getValue('applicationTheme');

                // Make sure it's a valid theme
                if (in_array($theme, Yii::$app->params['themes'])) {
                    Yii::$app->view->theme->pathMap['@app/views'] = [
                        '@app/themes/' . $theme,
                        '@andrej2013-backend-views',
                    ];
                } else {
                    // Delete the theme
                    Yii::$app->getResponse()->cookies->remove('applicationTheme');
                }
            }
        }

        return true;
    }
}
