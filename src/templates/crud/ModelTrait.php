<?php

namespace andrej2013\yiiboilerplate\templates\crud;

/*
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use andrej2013\yiiboilerplate\templates\crud\Generator;
use andrej2013\yiiboilerplate\templates\model\Generator as ModelGenerator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use schmunk42\giiant\generators\crud\ModelTrait as BaseModelTrait;

trait ModelTrait
{
    use BaseModelTrait;

    /**
     * Finds relations of a model class.
     *
     * return values can be filtered by types 'belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'
     *
     * @param ActiveRecord $modelClass
     * @param array $types
     *
     * @return array
     */
    public function getModelRelations($modelClass, $types = ['belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'])
    {
        $reflector = new \ReflectionClass($modelClass);
        $model = new $modelClass();
        $stack = [];
        $modelGenerator = new ModelGenerator();
        foreach ($reflector->getMethods() as $method) {
            if (in_array(substr($method->name, 3), $this->skipRelations)) {
                continue;
            }
            // look for getters
            if (substr($method->name, 0, 3) !== 'get') {
                continue;
            }
            if (isset($model->ignoredRelations)) {
                if (in_array($method->name, $model->ignoredRelations)) {
                    continue;
                }
            }
            // skip class specific getters
            $skipMethods = [
                'getRelation',
                'getBehavior',
                'getFirstError',
                'getOperator',
                'getAttributeHint',
                'getAttribute',
                'getAttributeLabel',
                'getOldAttribute',
                'getFileUrl',
                'getFileType',
                'getUploadPath',
                'getHistory',
            ];
            if (in_array($method->name, $skipMethods)) {
                continue;
            }
            // check for relation
            try {
                $relation = @call_user_func(array($model, $method->name));
                if ($relation instanceof \yii\db\ActiveQuery) {
                    #var_dump($relation->primaryModel->primaryKey);
                    if ($relation->multiple === false) {
                        $relationType = 'belongs_to';
                    } elseif ($this->isPivotRelation($relation)) { # TODO: detecttion
                        $relationType = 'pivot';
                    } else {
                        $relationType = 'has_many';
                    }

                    if (in_array($relationType, $types)) {
                        $name = $modelGenerator->generateRelationName(
                            [$relation],
                            $model->getTableSchema(),
                            lcfirst(substr($method->name, 3)),
                            $relation->multiple
                        );
                        $stack[$name] = $relation;
                    }
                }
            } catch (Exception $e) {
                Yii::error('Error: ' . $e->getMessage(), __METHOD__);
            }
        }

        return $stack;
    }

    /**
     * @param $relation
     * @param $action
     * @return string
     */
    public function createRelationPermission($relation, $action)
    {
        $route = $this->pathPrefix.Inflector::camel2id(
                $this->generateRelationTo($relation),
                '-',
                true
            ) . '_' . $action;

        return $route;
    }
    
    public function createRelationRoute($relation, $action = null)
    {
        $route = $this->pathPrefix.Inflector::camel2id(
                $this->generateRelationTo($relation),
                '-',
                true
            ).($action ? ('/'.$action) : '');

        return $route;
    }
    
    public function createRoute($action = null)
    {
        $class = new \ReflectionClass($this->modelClass);
        $route = Inflector::variablize($class->getShortName());
        $route = $this->pathPrefix.Inflector::camel2id(
                $route,
                '-',
                true
            ).($action ? ('/'.$action) : '');
        return $route;
    }
    
}
