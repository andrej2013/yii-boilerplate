<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/23/2017
 * Time: 9:01 AM
 */

namespace andrej2013\yiiboilerplate\traits;

use andrej2013\yiiboilerplate\components\Helper;
use andrej2013\yiiboilerplate\modules\userParameter\models\UserParameter;
use yii\db\ActiveQuery;
use yii\db\conditions\AndCondition;
use yii\helpers\ArrayHelper;
use Yii;

trait SearchTrait
{

    public function getCondition($search_string)
    {
        $condition = [];
        if (is_array($search_string)) {
            $condition = [
                'operator'  => 'IN',
                'attribute' => $search_string,
            ];
        } else if (strpos($search_string, '<>') === 0 || strpos($search_string, '!=') === 0 || strpos($search_string, '!') === 0) {
            $condition = [
                'operator'  => '!=',
                'attribute' => str_replace(['<>', '!=', '!'], '', $search_string),
            ];
        } else if (strpos($search_string, '=>') === 0 || strpos($search_string, '>=') === 0) {
            $condition = [
                'operator'  => '>=',
                'attribute' => str_replace(['=>', '>='], '', $search_string),
            ];
        } else if (strpos($search_string, '>') === 0) {
            $condition = [
                'operator'  => '>',
                'attribute' => str_replace(['>'], '', $search_string),
            ];
        } else if (strpos($search_string, '<=') === 0 || strpos($search_string, '=<') === 0) {
            $condition = [
                'operator'  => '<=',
                'attribute' => str_replace(['<=', '=<'], '', $search_string),
            ];
        } else if (strpos($search_string, '<') === 0) {
            $condition = [
                'operator'  => '<',
                'attribute' => str_replace(['<'], '', $search_string),
            ];
        } else if (strpos(strtolower($search_string), ' to ') !== false || strpos(strtolower($search_string), ' between ') !== false || strpos(strtolower($search_string), '...') !== false) {
            $delimiters = [
                ' to ',
                ' TO ',
                ' between ',
                ' BETWEEN ',
                '...',
            ];
            foreach ($delimiters as $delimiter) {
                $parts = explode($delimiter, $search_string);
                $condition = [
                    'operator'  => 'BETWEEN',
                    'attribute' => $parts,
                ];
                if (count($parts) >= 2) {
                    break;
                }
            }
        } else if (strpos(strtolower($search_string), ' and ') !== false) {
            $delimiters = [
                ' and ',
                ' And ',
                ' AND ',
            ];
            foreach ($delimiters as $delimiter) {
                $parts = explode($delimiter, $search_string);
                $condition = [
                    'condition' => 'AND',
                    'operator'  => 'LIKE',
                    'attribute' => $parts,
                ];
                if (count($parts) >= 2) {
                    break;
                }
            }
        } else if (strpos(strtolower($search_string), ' or ') !== false) {
            $delimiters = [
                ' or ',
                ' Or ',
                ' OR ',
            ];
            foreach ($delimiters as $delimiter) {
                $parts = explode($delimiter, $search_string);
                $condition = [
                    'condition' => 'OR',
                    'operator'  => 'LIKE',
                    'attribute' => $parts,
                ];
                if (count($parts) >= 2) {
                    break;
                }
            }
        } else {
            $condition = [
                'operator'  => '=',
                'attribute' => $search_string,
            ];
        }
        return $condition;
    }

    /**
     * @param             $attribute
     * @param ActiveQuery $query
     * @param null        $attribute_alias
     * @param null        $table_alias
     */
    public function applyIntegerFilter($attribute, &$query, $attribute_alias = null, $table_alias = null)
    {
        $conditions = $this->getCondition($this->$attribute);
        $attribute_copy = $attribute;
        $field_name = $attribute_alias ? $attribute_alias : $attribute;
        $field_name = ($table_alias ? $table_alias : static::tableName()) . '.' . $field_name;
        switch ($conditions['operator']) {
            case 'IN':
            case '=':
                $query->andFilterWhere([$field_name => $conditions['attribute']]);
                break;
            case 'LIKE':
                $values = [];
                foreach ($conditions['attribute'] as $value) {
                    $values[] = [
                        'LIKE',
                        $field_name,
                        $value,
                    ];
                }
                $query->andFilterWhere([
                    $conditions['condition'],
                    $values,
                ]);
                break;
            case 'BETWEEN':
                $query->andFilterWhere([
                    'BETWEEN',
                    $field_name,
                    $conditions['attribute'][0],
                    $conditions['attribute'][1],
                ]);
                break;
            default:
                if ($conditions) {
                    $query->andFilterWhere([
                        $conditions['operator'],
                        $field_name,
                        $conditions['attribute'],
                    ]);
                }
        }
    }

    /**
     * @param             $attribute
     * @param ActiveQuery $query
     * @param null        $attribute_alias
     * @param null        $table_alias
     */
    public function applyStringFilter($attribute, &$query, $attribute_alias = null, $table_alias = null)
    {
        $conditions = $this->getCondition($this->$attribute);
        $attribute_copy = $attribute;
        $field_name = $attribute_alias ? $attribute_alias : $attribute;
        $field_name = ($table_alias ? $table_alias : static::tableName()) . '.' . $field_name;
        switch ($conditions['operator']) {
            case '=':
                $query->andFilterWhere([
                    'LIKE',
                    $field_name,
                    $conditions['attribute'],
                ]);
                break;
            case 'LIKE':
                $values = [];
                foreach ($conditions['attribute'] as $value) {
                    $values[] = [
                        'LIKE',
                        $field_name,
                        $value,
                    ];
                }
                $query->andFilterWhere([
                    $conditions['condition'],
                    $values,
                ]);
                break;
            case '!=':
                $query->andFilterWhere([
                    $conditions['operator'],
                    $field_name,
                    $conditions['attribute'],
                ]);
                break;
            case 'IN':
                $query->andFilterWhere([
                    $conditions['operator'],
                    $field_name,
                    $conditions['attribute'],
                ]);
                break;
            default:
                $query->andFilterWhere([
                    $conditions['operator'],
                    $field_name,
                    $conditions['attribute'],
                ]);
                break;
        }
    }

    /**
     * @param             $attribute
     * @param ActiveQuery $query
     * @param null        $attribute_alias
     * @param null        $table_alias
     * @param bool        $date_time
     */
    public function applyDateFilter($attribute, &$query, $attribute_alias = null, $table_alias = null, $date_time = false)
    {
        $field_name = $attribute_alias ? $attribute_alias : $attribute;
        $field_name = ($table_alias ? $table_alias : static::tableName()) . '.' . $field_name;
        if (isset($this->$attribute) && $this->$attribute) {
            $out_format = 'Y-m-d';
            $out_format .= $date_time ? ' H:i:s' : '';
            $input_format = $date_time ? Yii::$app->formatter->momentJsDateTimeFormat : Yii::$app->formatter->momentJsDateFormat;
            $input_dates = explode(' TO ', $this->$attribute);
            $out_dates = [];
            foreach ($input_dates as $input_date) {
                $convert = \DateTime::createFromFormat(Helper::convertMomentToPHPFormat($input_format), trim($input_date));
                $out_dates[] = $convert->format($out_format);
            }
            $query->andFilterWhere([
                'BETWEEN',
                $field_name,
                $out_dates[0],
                $out_dates[1],
            ]);
        }
    }

    /**
     * @param $model
     * @return int
     */
    public function parsePageParams($model)
    {
        return Yii::$app->request->get('page', 1);
        $value = Yii::$app->get('userUi')
                          ->get('Grid', $model)['page'];
        return isset($value) ? $value : Yii::$app->request->get('page', 1);
    }

    /**
     * Determine the page size for the grid based on the model
     * @param $model
     * @return int
     */
    public function parsePageSize($model)
    {
        // First try using the user's userUi model
        return Yii::$app->request->get('pageSize', 20);
    }
}
