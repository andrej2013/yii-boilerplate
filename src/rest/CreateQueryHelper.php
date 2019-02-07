<?php
/**
 * Created by PhpStorm.
 * User: harry
 * Date: 14-9-13
 * Time: 下午7:22
 */

namespace andrej2013\yiiboilerplate\rest;

use Yii;
use yii\helpers\ArrayHelper;
use andrej2013\rest\CreateQueryHelper as BaseQueryHelper;


class CreateQueryHelper extends BaseQueryHelper
{
    /**
     * @var array
     */
    public static $exclude_field = [
        'fields', 'expand', 'sort', 'page', 'per-page', 'expand-fields', 'r', 'PHPSESSID', 'group'
    ];

    /**
     * @param $modelClass
     * @param array $ignore
     * @return mixed
     */
    public static function createQuery($modelClass, $ignore = [])
    {
        $model = $modelClass::find();
        $wheres = ['and'];
        $filter_fields = self::getQueryParams($ignore);
        $condition_transform_functions = self::conditionTransformFunctions();
        foreach ($filter_fields as $key => $value) {
            if ($value == '' || in_array($key, self::$exclude_field)) {
                continue;
            }
            $field_key = $key;
            if (!strpos($key, '.')) {
                $field_key = $modelClass::tableName() . '.' . $key;
            } else {
                $relation_model = substr($field_key, 0, strrpos($key, '.'));
                $model->joinWith($relation_model);
                if (strpos($relation_model, '.')) {
                    $temp = substr($field_key, strrpos($field_key, '.'));
                    $field_key = substr($relation_model, strrpos($relation_model, '.') + 1) . $temp;
                    $field_key = str_replace($relation_model, $relation_model::tableName(), $field_key);
                } else {
                    // Build the relation's tale name
                    $baseModel = new $modelClass;
                    $relationModel = $baseModel->getRelation($relation_model);
                    $relationModel = new $relationModel->modelClass;
                    $field_key = str_replace($relation_model . '.', $relationModel->tableName() . '.', $field_key);
                }
            }

            $type = 'EQUAL';
            if (preg_match("/^[A-Z]+_/", $value, $matches) && array_key_exists(trim($matches[0], '_'), $condition_transform_functions)) {
                $type = trim($matches[0], '_');
                $value = str_replace($matches[0], '', $value);
            }

            $wheres = ArrayHelper::merge($wheres, [$condition_transform_functions[$type]($field_key, $value)]);
        }
        if (count($wheres) > 1) {
            $model->andWhere($wheres);
        }


        return $model;
    }

    /**
     * @param $ignore
     * @return array
     */
    private static function getQueryParams($ignore)
    {
        $pairs = explode("&", urldecode(Yii::$app->getRequest()->queryString));
        $vars = [];
        foreach ($pairs as $pair) {
            if ($pair == '') {
                continue;
            }
            $nv = explode("=", $pair);
            if (count($nv) != 2) {
                continue;
            }
            $name = urldecode($nv[0]);
            $value = urldecode($nv[1]);
            if (!in_array($name, $ignore)) {
                $vars[$name] = $value;
            }
        }
        return $vars;
    }

    /**
     * @param $sort
     * @param $table
     * @param $query
     */
    public static function addOrderSort($sort, $table, &$query)
    {
        $order = null;
        if (!empty($sort)) {
            $sorts = explode(',', $sort);
            foreach ($sorts as $sort) {
                if (!strpos($sort, '.')) {
                    preg_match('/\w+\s+(DESC|ASC)/', $sort, $sort_field);
                    $type = !empty($sort_field) ? trim($sort_field[1]) : 'DESC';
                    $field = !empty($sort_field) ? trim(substr($sort, 0, -strlen($type))) : trim($sort);
                    $order[$table . '.' . $field] = $type == 'DESC' ? SORT_DESC : SORT_ASC;
                } else {
                    $sort_table = trim(substr($sort, 0, strrpos($sort, '.')));
                    preg_match('/\w+\.\w+\s+(DESC|ASC)/', $sort, $sort_field);
                    $type = trim($sort_field[1]);
                    $field = trim(substr(substr($sort, strrpos($sort, '.') + 1), 0, -strlen($type)));
                    $order[trim($sort_table) . '.' . $field] = $type == 'DESC' ? SORT_DESC : SORT_ASC;
                    $query->select[] = explode(' ', $sort_field[0])[0];
                    $query->joinWith($sort_table);
                }
            }
            $query->select[] = $table . ".*";
        }
        $query->orderBy($order);
    }

    /**
     * Group by
     */
    public static function addGroup($group, $table, &$query)
    {
        if (!empty($group)) {
            $groups = explode(',', $group);
            foreach ($groups as $group) {
                if (!strpos($group, '.')) {
                    $query->groupBy($table . '.' . $group);
                } else {
                    $query->groupBy($group);
                }
            }
        }
    }
}
