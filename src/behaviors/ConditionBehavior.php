<?php
/**
 * Created by PhpStorm.
 * User: ntesic
 * Date: 2/27/19
 * Time: 10:27 AM
 */

namespace andrej2013\yiiboilerplate\behaviors;

class ConditionBehavior extends SoftDeleteQueryBehavior
{
    public $conditions = [];

    public function addConditions()
    {
        return $this->addFilterCondition($this->conditions);
    }

}