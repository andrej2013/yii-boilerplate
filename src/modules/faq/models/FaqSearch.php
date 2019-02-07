<?php
/**
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

namespace andrej2013\yiiboilerplate\modules\faq\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use andrej2013\yiiboilerplate\modules\faq\models\Faq;

/**
 * FaqSearch represents the model behind the search form about `app\modules\faq\models\Faq`.
 */
class FaqSearch extends Faq
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title', 'content', 'language_id', 'place', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at', 'level', 'order'], 'safe'],
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
        $query = Faq::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $operator = $this->getOperator($this->id);
        if (!is_array($operator)) {
        	$query->andFilterWhere(['id' => $this->id]);
        } elseif (($operator['operator'] !== 'between')) {
        	$query->andFilterWhere([$operator['operator'], 'id', $operator['operand']]);
        } else {
        	$query->andFilterWhere([$operator['operator'], 'id', $operator['start'], $operator['end']]);
        }

        $operator = $this->getOperator($this->created_by);
        if (!is_array($operator)) {
        	$query->andFilterWhere(['created_by' => $this->created_by]);
        } elseif (($operator['operator'] !== 'between')) {
        	$query->andFilterWhere([$operator['operator'], 'created_by', $operator['operand']]);
        } else {
        	$query->andFilterWhere([$operator['operator'], 'created_by', $operator['start'], $operator['end']]);
        }

        $operator = $this->getOperator($this->updated_by);
        if (!is_array($operator)) {
        	$query->andFilterWhere(['updated_by' => $this->updated_by]);
        } elseif (($operator['operator'] !== 'between')) {
        	$query->andFilterWhere([$operator['operator'], 'updated_by', $operator['operand']]);
        } else {
        	$query->andFilterWhere([$operator['operator'], 'updated_by', $operator['start'], $operator['end']]);
        }

        $operator = $this->getOperator($this->deleted_by);
        if (!is_array($operator)) {
        	$query->andFilterWhere(['deleted_by' => $this->deleted_by]);
        } elseif (($operator['operator'] !== 'between')) {
        	$query->andFilterWhere([$operator['operator'], 'deleted_by', $operator['operand']]);
        } else {
        	$query->andFilterWhere([$operator['operator'], 'deleted_by', $operator['start'], $operator['end']]);
        }

        $operator = $this->getOperator($this->level);
        if (!is_array($operator)) {
        	$query->andFilterWhere(['level' => $this->level]);
        } elseif (($operator['operator'] !== 'between')) {
        	$query->andFilterWhere([$operator['operator'], 'level', $operator['operand']]);
        } else {
        	$query->andFilterWhere([$operator['operator'], 'level', $operator['start'], $operator['end']]);
        }

        $operator = $this->getOperator($this->order);
        if (!is_array($operator)) {
        	$query->andFilterWhere(['order' => $this->order]);
        } elseif (($operator['operator'] !== 'between')) {
        	$query->andFilterWhere([$operator['operator'], 'order', $operator['operand']]);
        } else {
        	$query->andFilterWhere([$operator['operator'], 'order', $operator['start'], $operator['end']]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'language_id', $this->language_id])
            ->andFilterWhere(['like', 'place', $this->place]);

        if (isset($this->created_at) && $this->created_at != '') {
        	$date_explode = explode(" TO ", $this->created_at);
        	$date1 = trim($date_explode[0]);
        	$date2 = trim($date_explode[1]);
        	$query->andFilterWhere(['between', 'created_at', $date1, $date2]);
        }
        if (isset($this->updated_at) && $this->updated_at != '') {
        	$date_explode = explode(" TO ", $this->updated_at);
        	$date1 = trim($date_explode[0]);
        	$date2 = trim($date_explode[1]);
        	$query->andFilterWhere(['between', 'updated_at', $date1, $date2]);
        }
        if (isset($this->deleted_at) && $this->deleted_at != '') {
        	$date_explode = explode(" TO ", $this->deleted_at);
        	$date1 = trim($date_explode[0]);
        	$date2 = trim($date_explode[1]);
        	$query->andFilterWhere(['between', 'deleted_at', $date1, $date2]);
        }
        return $dataProvider;
    }
}
