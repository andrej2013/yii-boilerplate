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

/**
 * Class TwActiveQuery
 * Use to set default scopes for all models
 *
 * @package andrej2013\yiiboilerplate\models
 */
class ActiveQuery extends BaseActiveQuery
{
    /**
     * Get all models who's timestamp has been updated since a date.
     * Models who don't have the timestamp behavior will need to override this method in the Query class.
     * @param $updatedAt
     * @return mixed
     */
    public function updated(DateTime $updatedAt = null)
    {
        if (getenv('CRUD') && !empty($updatedAt)) {
            return $this->andWhere(['>=', $this->getPrimaryTableName() . '.updated_at', $updatedAt->format('Y-m-d H:i:s')]);
        }
        // If we don't have a datetime, send everything, it's probably the first sync but don't send deleted items
        if (getenv('SOFT_DELETE')) {
            $this->andWhere([$this->getPrimaryTableName() . '.deleted_at' => null]);
        }
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
        if (getenv('CRUD')) {
            $this->andWhere(['>=', $this->getPrimaryTableName() . '.updated_at', $date->format('Y-m-d 00:00:00')]);
        }
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
     * @param array $params the parameters (name => value) to be bound to the query.
     * @return $this the query object itself
     * @see andWhere()
     * @see orWhere()
     * @see QueryInterface::where()
     */
    public function where($condition, $params = [])
    {
        return $this->andWhere($condition, $params);
    }
}
