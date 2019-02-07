<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \andrej2013\yiiboilerplate\templates\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use andrej2013\yiiboilerplate\traits\SearchTrait;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;

/**
 * <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass . "\n"?>
{

    use SearchTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            <?= implode(",\n" . str_repeat(' ', 12), $rules) ?>,
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
        $query = <?= isset($modelAlias) ? $modelAlias : $modelClass ?>::find();

        $this->parseSearchParams(<?= isset($modelAlias) ? $modelAlias : $modelClass ?>::className(), $params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => $this->parseSortParams(<?= isset($modelAlias) ? $modelAlias : $modelClass ?>::className()),
            ],
            'pagination' => [
                'pageSize' => $this->parsePageSize(<?= isset($modelAlias) ? $modelAlias : $modelClass ?>::className()),
                'params' => [
                    'page' => $this->parsePageParams(<?= isset($modelAlias) ? $modelAlias : $modelClass ?>::className()),
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        <?= implode("\n        ", $searchConditions) ?>

        return $dataProvider;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->parsePageSize(<?= isset($modelAlias) ? $modelAlias : $modelClass ?>::className());
    }
}
