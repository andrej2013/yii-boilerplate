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
use andrej2013\yiiboilerplate\models\ArHistory as ArHistoryModel;

/**
 * ArHistory represents the model behind the search form of `app\models\ArHistory`.
 */
class ArHistory extends ArHistoryModel
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
                    'row_id',
                    'event',
                    'created_at',
                    'created_by',
                    'field_name',
                    'old_value',
                    'new_value',
                ],
                'safe',
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
        $query = ArHistoryModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (! $this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $this->applyIntegerFilter('id', $query);
        $this->applyIntegerFilter('row_id', $query);
        $this->applyIntegerFilter('event', $query);
        $this->applyIntegerFilter('created_at', $query);
        $this->applyIntegerFilter('created_by', $query);
        $this->applyStringFilter('table_name', $query);
        $this->applyStringFilter('field_name', $query);
        $this->applyStringFilter('old_value', $query);
        $this->applyStringFilter('new_value', $query);
        return $dataProvider;
    }


    /**
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->parsePageSize(ArHistoryModel::class);
    }


}
