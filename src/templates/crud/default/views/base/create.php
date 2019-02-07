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
<div class="box box-default">
    <div
        class="giiant-crud box-body <?= Inflector::camel2id(
            StringHelper::basename($generator->modelClass),
            '-',
            true
        ) ?>-create">

        <div class="clearfix crud-navigation">
            <div class="pull-left">
                <?= '<?= ' ?>Html::a(
                    <?= $generator->generateString('Cancel') ?>,
                    \yii\helpers\Url::previous(),
                    [
                        'class' => 'btn btn-default'
                    ]
                ) ?>
            </div>
        </div>

        <?= '<?= ' ?>$this->render('_form', [
            'model' => $model,
            'inlineForm' => $inlineForm,
            'action' => $action,
            'relatedTypeForm' => $relatedTypeForm,
        <?php if (method_exists(ltrim($generator->modelClass, '\\'), 'getLanguages')) { ?>
            'translations' => $translations,
            'languageCodes' => $languageCodes,
<?php } ?>
        ]); ?>

    </div>
</div>
