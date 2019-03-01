<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/27/19
 * Time: 10:15 AM
 */

namespace andrej2013\yiiboilerplate\behaviors;
use yii2tech\ar\softdelete\SoftDeleteQueryBehavior as BaseBehavior;

class SoftDeleteQueryBehavior extends BaseBehavior
{
    /**
     * Adds given filter condition to the owner query.
     * @param array $condition filter condition.
     * @return \yii\db\ActiveQueryInterface|static owner query instance.
     */
    protected function addFilterCondition($condition)
    {
        return parent::addFilterCondition($condition);
    }
    
    /**
     * Normalizes raw filter condition adding table alias for relation database query.
     * @param array $condition raw filter condition.
     * @return array normalized condition.
     */
    protected function normalizeFilterCondition($condition)
    {
        if (method_exists($this->owner, 'getTablesUsedInFrom')) {
            $alias = \yii\db\ActiveQuery::ALIAS_PLACEHOLDER;
            foreach ($condition as $attribute => $value) {
                if (is_numeric($attribute) || strpos($attribute, '.') !== false) {
                    continue;
                }

                unset($condition[$attribute]);
                if (strpos($attribute, '[[') === false) {
                    $attribute = '[[' . $attribute . ']]';
                }
                $attribute = $alias . '.' . $attribute;
                $condition[$attribute] = $value;
            }
        }

        return $condition;
    }

}