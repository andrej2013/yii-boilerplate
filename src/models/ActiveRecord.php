<?php
/**
 * Created by andrej2013
 */

namespace andrej2013\yiiboilerplate\models;

use andrej2013\yiiboilerplate\behaviors\SoftDelete;
use andrej2013\yiiboilerplate\behaviors\History as HistoryBehavior;
use andrej2013\yiiboilerplate\traits\ArHistoryTrait;
use andrej2013\yiiboilerplate\traits\SearchTrait;
use Yii;
use andrej2013\yiiboilerplate\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord as BaseActiveRecord;
use yii\db\Expression;

/**
 * This is the andrej2013 base-model class for table "TwActiveRecord".
 *
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 */

class ActiveRecord extends BaseActiveRecord
{
    /**
     * Trait to save all edits to the history table
     */
    use ArHistoryTrait;

    /**
     * Trait to be able to easily filter on the columns of the table
     */
    use SearchTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [];
        if (getenv('CRUD')) {
            $behaviors[] = BlameableBehavior::class;
            $behaviors[] = [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ];
            $behaviors['arhistory'] = [
                'class' => HistoryBehavior::class,
                'skipAttributes' => [
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by'
                ],
                'allowEvents' => [
                    HistoryBehavior::EVENT_UPDATE,
                    HistoryBehavior::EVENT_DELETE,
                    HistoryBehavior::EVENT_INSERT,
                ]
            ];
        }
        if (getenv('SOFT_DELETE') && getenv('CRUD')) {
            $behaviors[] = SoftDelete::class;
        }
        return $behaviors;
    }

    /**
     * Get the placeholder of a field
     * @param $field
     * @return mixed
     */
    public function getAttributePlaceholder($field)
    {
        // Try the obvious palceholders
        $placeholders = $this->attributePlaceholders();
        if (!empty($placeholders[$field])) {
            return $placeholders[$field];
        }
        // Try with labels
        $labels = $this->attributeLabels();
        if (!empty($labels[$field])) {
            return $labels[$field];
        }
        // Default is to inflect the field name
        return $this->generateAttributeLabel($field);
    }

    /**
     * List of placeholders for fields
     * @return array
     */
    public function attributePlaceholders()
    {
        return [];
    }

    /**
     * SoftDeleteBehavior to find only not deleted entries
     * @param $removedDeleted bool To remove soft-deleted entries from results, false to include them in re
     * @inheritdoc
     */
    public static function find($removedDeleted = true)
    {
        $model = new \andrej2013\yiiboilerplate\models\ActiveQuery(get_called_class());
        if (getenv('SOFT_DELETE') && $removedDeleted === true) {
            $reflection = new \ReflectionClass(get_called_class());
            if ($reflection->hasMethod('tableAlias')) {
                $model->andWhere([static::tableAlias() . '.deleted_at' => null]);
            } else {
                $model->andWhere([static::tableName() . '.deleted_at' => null]);
            }
        }
        return $model;
    }

    /**
     * Find API is used in the SyncController process. Default behaviour is to use the standard find function,
     * which usually filters on the .deleted_at. However, this can be overwritten to included deleted items
     * in the apps.
     * @param $removedDeleted bool To remove soft-deleted entries from results, false to include them in re
     * @return mixed
     */
    public static function findApi($removedDeleted = true)
    {
        return static::find($removedDeleted);
    }

    /**
     * Find records that are SoftDeleted also
     * @return TwActiveQuery
     */
    public static function findDeleted()
    {
        return new \andrej2013\yiiboilerplate\models\TwActiveQuery(get_called_class());
    }

    /**
     * Export model attributes to ENV
     * @param array $attributes
     * @throws \Exception
     */
    public function toEnv(array $attributes = [])
    {
        $reflection = new \ReflectionClass($this);
        $modelName = $reflection->getShortName();
        if (empty($attribute)) {
            foreach ($this->attributes as $key => $val) {
                $attributes[] = $key;
            }
        }
        foreach ($attributes as $attribute) {
            if (!$this->hasAttribute($attribute)) {
                throw new \Exception("Model $modelName don't have attribute: $attribute");
            }
            $var = strtoupper($modelName . '_' . $attribute);
            $value = $this->{$attribute};
            // If PHP is running as an Apache module and an existing
            // Apache environment variable exists, overwrite it
            if (function_exists('apache_getenv') && function_exists('apache_setenv') && apache_getenv($var)) {
                apache_setenv($var, $value);
            }

            if (function_exists('putenv')) {
                putenv("$var=$value");
            }
            $_ENV[$var] = $value;
            $_SERVER[$var] = $value;
        }
    }

    /**
     * B(R)EAD - Determine if the model can be Read by the user.
     * To be overwritten in the model.
     * @return bool
     */
    public function readable()
    {
        return true;
    }


    /**
     * BR(E)AD - Determine if the model can be Edited by the user.
     * To be overwritten in the model.
     * @return bool
     */
    public function editable()
    {
        return true;
    }


    /**
     * BREA(D) - Determine if the model can be Deleted by the user.
     * To be overwritten in the model.
     * @return bool
     */
    public function deletable()
    {
        return true;
    }
}