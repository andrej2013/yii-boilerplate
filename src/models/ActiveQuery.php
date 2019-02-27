<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 25/01/2017
 * Time: 08:40
 */

namespace andrej2013\yiiboilerplate\models;

use andrej2013\yiiboilerplate\helpers\DebugHelper;
use yii\db\ActiveQuery as BaseActiveQuery;
use DateTime;
use yii\db\ActiveRelationTrait;
use yii\db\Query;
use yii2tech\ar\softdelete\SoftDeleteQueryBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class TwActiveQuery
 * Use to set default scopes for all models
 *
 * @package andrej2013\yiiboilerplate\models
 */
class ActiveQuery extends BaseActiveQuery
{
    use ActiveRelationTrait;

    const ALIAS_PLACEHOLDER = '@alias';

    /**
     * Get all models who's timestamp has been updated since a date.
     * Models who don't have the timestamp behavior will need to override this method in the Query class.
     * @param $updatedAt
     * @return mixed
     */
    public function updated(DateTime $updatedAt = null)
    {
        return $this->andWhere(['>=', $this->getPrimaryTableName() . '.updated_at', $updatedAt->format('Y-m-d H:i:s')]);
        // If we don't have a datetime, send everything, it's probably the first sync but don't send deleted items
        $this->andWhere([$this->getPrimaryTableName() . '.deleted_at' => null]);
        return $this;
    }

    /**
     * Override this method in the Query class to define special filters for the sync process.
     * For example, limiting the results to the current logged in user
     * @return $this
     */
    public function api()
    {
        return $this;
    }

    /**
     * Scope on the user Id.
     * @param $userId
     * @return mixed
     */
    public function user($userId)
    {
        return $this->andWhere([$this->getPrimaryTableName() . '.user_id' => $userId]);
    }

    /**
     * Scope on the user ids
     * @param $userIds
     * @return mixed
     */
    public function users($userIds)
    {
        return $this->andWhere([$this->getPrimaryTableName() . '.user_id' => $userIds]);
    }

    /**
     * Ask for elements edited during the last two days.
     * @return $this
     */
    public function recent()
    {
        $date = new DateTime();
        $date->modify('2 days ago');
        $this->andWhere(['>=', $this->getPrimaryTableName() . '.updated_at', $date->format('Y-m-d 00:00:00')]);
        return $this;
    }

    /**
     * Sets the WHERE part of the query.
     *
     * The method requires a `$condition` parameter, and optionally a `$params` parameter
     * specifying the values to be bound to the query.
     *
     * The `$condition` parameter should be either a string (e.g. `'id=1'`) or an array.
     *
     * @inheritdoc
     *
     * @param string|array|Expression $condition the conditions that should be put in the WHERE part.
     * @param array                   $params    the parameters (name => value) to be bound to the query.
     * @return $this the query object itself
     * @see andWhere()
     * @see orWhere()
     * @see QueryInterface::where()
     */
    public function where($condition, $params = [])
    {
        return $this->andWhere($condition, $params);
    }

    /**
     * @param $builder
     * @return \yii\db\ActiveQuery|\yii\db\Query
     */
    public function prepare($builder)
    {
        $query = parent::prepare($builder);
        static::replaceAlias($query);
        return $query;
    }

    /**
     * @param \yii\db\Query $query
     */
    public static function replaceAlias(Query $query)
    {
        $alias = ArrayHelper::isAssociative($query->from) ? array_keys($query->from)[0] : $query->from[0];
        $alias = key($query->cleanUpTableNames([$alias]));
        $replaceAliasRecursively = function ($value) use ($alias, &$replaceAliasRecursively) {
            if ($value instanceof \yii\db\Expression) {
                $value->expression = $replaceAliasRecursively($value->expression);
            } else if (is_scalar($value)) {
                $value = str_replace(self::ALIAS_PLACEHOLDER, $alias, $value);
            } else if (is_array($value)) {
                $newValue = [];
                foreach ($value as $k => $v) {
                    $newKey = $replaceAliasRecursively($k);
                    $newValue[$newKey] = $replaceAliasRecursively($v);
                }
                $value = $newValue;
                unset($newValue);
            }

            return $value;
        };

        $attributes = ['where', 'orderBy', 'on', 'having', 'union'];
        foreach ($attributes as $attribute) {
            if (! empty($query->$attribute)) {
                $query->$attribute = $replaceAliasRecursively($query->$attribute);
            }
        }
    }

    /**
     * @param $callback
     * @return \Closure
     */
    public function aliasMiddleware($callback)
    {
        return function (self $query) use ($callback) {
            $callback($query);
            static::replaceAlias($query);
        };
    }

    /**
     * @param array|string $with
     * @param bool         $eagerLoading
     * @param string       $joinType
     * @return \yii\db\ActiveQuery
     */
    public function joinWith($with, $eagerLoading = true, $joinType = 'LEFT JOIN')
    {
        $result = parent::joinWith($with, $eagerLoading, $joinType);
        foreach ($this->joinWith as $i => $config) {
            foreach ($config[0] as $j => $relation) {
                if (is_callable($relation)) {
                    $this->joinWith[$i][0][$j] = $this->aliasMiddleware($relation);
                }
            }
        }
        return $result;
    }

}
