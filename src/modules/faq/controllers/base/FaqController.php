<?php

namespace andrej2013\yiiboilerplate\modules\faq\controllers\base;

use andrej2013\yiiboilerplate\controllers\CrudController;
use andrej2013\yiiboilerplate\modules\faq\models\Faq;
use andrej2013\yiiboilerplate\traits\DependingTrait;
use yii\helpers\ArrayHelper;

/**
 * FaqController implements the CRUD actions for Faq model.
 */
class FaqController extends CrudController
{
    use DependingTrait;
    
    public static function getSubList($cat_id, $on, $onRelation)
    {
        $models = Faq::find()
                     ->select(['id', 'name' => 'title'])
                     ->where(['language_id' => $cat_id])
                     ->asArray()
                     ->all();
        $root[0] = ['id' => (string)\andrej2013\yiiboilerplate\modules\faq\models\Faq::ROOT_LEVEL, 'name' => \Yii::t('app', 'Root')];
        return ArrayHelper::merge($root, $models);
    }
    
}
