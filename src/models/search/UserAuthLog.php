<?php
/**
 * /home/ntesic/www/yii2-my-starter-kit/src/../runtime/giiant/e0080b9d6ffa35acb85312bf99a557f2
 *
 * @package default
 */


namespace andrej2013\yiiboilerplate\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use andrej2013\yiiboilerplate\traits\SearchTrait;
use andrej2013\yiiboilerplate\models\UserAuthLog as UserAuthLogModel;

/**
 * UserAuthLog represents the model behind the search form of `andrej2013\yiiboilerplate\models\UserAuthLog`.
 */
class UserAuthLog extends UserAuthLogModel
{
	use SearchTrait;

	/**
	 *
	 * @inheritdoc
	 * @return unknown
	 */
	public function rules() {
		return [
			[
				[
					'id',
					'user_id',
					'date',
					'cookie_based',
					'duration',
					'error',
					'ip',
					'host',
					'url',
					'user_agent',
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
	 *
	 * @inheritdoc
	 * @return unknown
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}


	/**
	 * Creates data provider instance with search query applied
	 *
	 *
	 * @param array   $params
	 * @return ActiveDataProvider
	 */
	public function search($params) {
		$query = UserAuthLogModel::find();

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
		$this->applyIntegerFilter('user_id', $query);
		$this->applyIntegerFilter('date', $query);
		$this->applyIntegerFilter('duration', $query);
		$this->applyIntegerFilter('created_by', $query);
		$this->applyIntegerFilter('updated_by', $query);
		$this->applyIntegerFilter('deleted_by', $query);
		$this->applyStringFilter('cookie_based', $query);
		$this->applyStringFilter('error', $query);
		$this->applyStringFilter('ip', $query);
		$this->applyStringFilter('host', $query);
		$this->applyStringFilter('url', $query);
		$this->applyStringFilter('user_agent', $query);
		$this->applyDateFilter('created_at', $query);
		$this->applyDateFilter('updated_at', $query);
		$this->applyDateFilter('deleted_at', $query);
		return $dataProvider;
	}


	/**
	 *
	 * @return int
	 */
	public function getPageSize() {
		return $this->parsePageSize(UserAuthLogModel::class);
	}


}
