<?php

namespace andrej2013\yiiboilerplate\modules\faq\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use andrej2013\yiiboilerplate\traits\SearchTrait;
use andrej2013\yiiboilerplate\modules\faq\models\Faq as FaqModel;

/**
 * Faq represents the model behind the search form of `app\models\Faq`.
 */
class Faq extends FaqModel
{
    use SearchTrait;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'title',
                    'content',
                    'language_id',
                    'place',
                    'level',
                    'order',
                    'created_by',
                    'created_at',
                    'updated_by',
                    'updated_at',
                    'deleted_by',
                    'deleted_at'
                ],
                'safe'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = FaqModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $this->applyIntegerFilter('id', $query);
        $this->applyIntegerFilter('level', $query);
        $this->applyIntegerFilter('order', $query);
        $this->applyIntegerFilter('created_by', $query);
        $this->applyIntegerFilter('updated_by', $query);
        $this->applyIntegerFilter('deleted_by', $query);
        $this->applyStringFilter('title', $query);
        $this->applyStringFilter('content', $query);
        $this->applyStringFilter('language_id', $query);
        $this->applyStringFilter('place', $query);
        $this->applyDateFilter('created_at', $query);
        $this->applyDateFilter('updated_at', $query);
        $this->applyDateFilter('deleted_at', $query);
        return $dataProvider;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->parsePageSize(FaqModel::class);
    }

}
