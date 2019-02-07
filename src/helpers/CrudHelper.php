<?php

namespace andrej2013\yiiboilerplate\helpers;

use yii\helpers\ArrayHelper;
use yii\db\ColumnSchema;

class CrudHelper
{
    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getUploadFields($class)
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = new $class;
        $model->setScenario('crud');
        $safeAttributes = $model->safeAttributes();
        if (empty($safeAttributes)) {
            $safeAttributes = $model->getTableSchema()->columnNames;
        }
        $out = [];
        foreach ($safeAttributes as $attribute) {
            $column = ArrayHelper::getValue($model->getTableSchema()->columns, $attribute);
            if (self::checkIfUploaded($column)) {
                $out[] = $attribute;
            }
        }
        return $out;
    }

    /**
     * @param ColumnSchema $column
     * @return bool
     */
    public static function checkIfUploaded(ColumnSchema $column)
    {
        $comment = self::extractComments($column);
        if (preg_match('/(_upload|_file)$/i', $column->name) ||
            ($comment && ($comment->inputtype === 'upload' || $comment->inputtype === 'file'))
        ) {
            return true;
        }
        return false;
    }

    /**
     * Extract comments from database
     * @param $column
     * @return bool|mixed
     */
    public static function extractComments($column)
    {
        $output = json_decode($column->comment);
        if (is_object($output)) {
            return $output;
        }
        return false;
    }
}
