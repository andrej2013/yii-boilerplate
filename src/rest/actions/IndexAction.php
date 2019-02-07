<?php
/**
 * tw-yii2-rest package
 */

namespace andrej2013\yiiboilerplate\rest\actions;

use yii;
use yii\rest\IndexAction as Action;
use yii\data\ActiveDataProvider;
use andrej2013\yiiboilerplate\rest\CreateQueryHelper;

/**
 * Class IndexAction
 * @package andrej2013\yiiboilerplate\rest\actions
 */
class IndexAction extends Action
{
    /**
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider()
    {
        $modelClass = $this->modelClass;

        $sort = yii::$app->request->get('sort','');
        $group = yii::$app->request->get('group','');

        $query = CreateQueryHelper::createQuery($this->modelClass);
        CreateQueryHelper::addOrderSort($sort, $modelClass::tableName(), $query);
        CreateQueryHelper::addGroup($group, $modelClass::tableName(), $query);

        return new ActiveDataProvider([
            'query' => $query->distinct(),
            'pagination' => isset($_GET['page'])? [] : false
        ]);
    }
}
