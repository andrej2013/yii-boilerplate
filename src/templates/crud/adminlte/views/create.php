<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var andrej2013\yiiboilerplate\templates\crud\Generator $generator
 */

echo "<?php\n";
?>

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 * @var string $relatedTypeForm
 */

$this->title = <?= $generator->generateString('Create') ?>;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '<?= Inflector::pluralize(
    Inflector::camel2words(StringHelper::basename($generator->modelClass))
) ?>'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-<?='<?php '; ?>echo \Yii::$app->params['style']['primary_color']; ?>">
    <div
        class="giiant-crud box-body <?= Inflector::camel2id(
            StringHelper::basename($generator->modelClass),
            '-',
            true
        ) ?>-create">

        <div class="clearfix crud-navigation">
            <div class="pull-left">
                <?= '<?= ' ?>Html::a(
                    '<span class="fa fa-ban"></span> '.<?= $generator->generateString('Cancel') ?>,
                    \yii\helpers\Url::previous(),
                    [
                        'class' => 'btn',
                        'preset' => Html::PRESET_DANGER,
                    ]
                ) ?>
            </div>
        </div>

        <?= '<?= ' ?>$this->render('_form', [
            'model' => $model,
        ]); ?>

    </div>
</div>
