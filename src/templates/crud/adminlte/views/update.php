<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var andrej2013\yiiboilerplate\templates\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 */

$this->title = Yii::t('app', '<?= Inflector::camel2words(
     StringHelper::basename($generator->modelClass)
) ?>') . ' ' . $model->toString . ', ' . <?= $generator->generateString('Edit') ?>;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '<?= Inflector::pluralize(
    Inflector::camel2words(StringHelper::basename($generator->modelClass))
) ?>'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->toString, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Edit') ?>;
?>
<div class="box box-<?='<?php '; ?>echo \Yii::$app->params['style']['primary_color']; ?>">
    <div
        class="giiant-crud box-body <?= Inflector::camel2id(
            StringHelper::basename($generator->modelClass),
            '-',
            true
        )
        ?>-update">

        <div class="crud-navigation">
            <?= '<?= ' ?>Html::a(
                '<span class="fa fa-eye"></span> ' . <?= $generator->generateString('View') ?>,
                ['view', <?= $urlParams ?>],
                [
                    'class' => 'btn',
                    'preset'    => Html::PRESET_PRIMARY,
                ]
            ) ?>
        </div>

        <?= '<?php ' ?>echo $this->render('_form', [
            'model' => $model,
            'hide'  => $hide,
        ]); ?>

    </div>
</div>