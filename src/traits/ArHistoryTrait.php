<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/19/2017
 * Time: 12:23 PM
 */
namespace andrej2013\yiiboilerplate\traits;

trait ArHistoryTrait
{
    public static function getPK()
    {
        if (is_array(static::primaryKey())) {
            return static::primaryKey()[0];
        }
        return static::primaryKey();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistory()
    {
        $relation = $this->hasMany(
            \andrej2013\yiiboilerplate\models\ArHistory::className(),
            ['row_id' => static::getPK()]
        )
            ->where([
                \andrej2013\yiiboilerplate\models\ArHistory::tableName() . '.table_name' => static::tableName()
            ]);
        if (getenv('CRUD')) {
            $relation->orderBy('created_at DESC');
        }
        return $relation;
    }
}