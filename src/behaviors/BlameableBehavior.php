<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace andrej2013\yiiboilerplate\behaviors;

use Yii;
use yii\behaviors\BlameableBehavior as BaseBlameableBehavior;


class BlameableBehavior extends BaseBlameableBehavior
{

    /**
     * @inheritdoc
     *
     * In case, when the [[value]] property is `null`, the value of `Yii::$app->user->id` will be used as the value.
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            if (Yii::$app instanceof \yii\web\Application) {
                return Yii::$app->user->isGuest ? 1 : Yii::$app->user->identity->id;
            }
            return 1;
        }
        return parent::getValue($event);
    }
}
