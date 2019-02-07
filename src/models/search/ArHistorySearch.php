<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by PhpStorm.
 * User: Nikola
 * Date: 5/23/2017
 * Time: 8:45 AM
 */

namespace andrej2013\yiiboilerplate\models\search;

use andrej2013\yiiboilerplate\models\ArHistory;
use andrej2013\yiiboilerplate\traits\SearchTrait;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ArHistorySearch extends \andrej2013\yiiboilerplate\models\ArHistory
{
    use SearchTrait;

    /**
     *
     * @inheritdoc
     * @return unknown
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'table_name',
                    'field_name',
                    'row_id',
                    'event',
                    'old_value',
                    'new_value',
                    'created_by',
                    'created_at',
                ],
                'safe'
            ],
        ];
    }

    /**
     *
     * @inheritdoc
     * @return unknown
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ArHistory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $this->applyLikeOperator('id', $query);
        $this->applyLikeOperator('table_name', $query);
        $this->applyLikeOperator('field_name', $query);
        $this->applyLikeOperator('row_id', $query);
        $this->applyLikeOperator('event', $query);
        $this->applyLikeOperator('old_value', $query);
        $this->applyLikeOperator('new_value', $query);
        $this->applyHashOperator('created_by', $query);
        $this->applyDateOperator('created_at', $query);
        return $dataProvider;
    }
}
