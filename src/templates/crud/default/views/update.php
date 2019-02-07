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
 * @var string $relatedTypeForm
 */

$this->title = Yii::t('app', '<?= Inflector::camel2words(
     StringHelper::basename($generator->modelClass)
) ?>') . ' ' . $model-><?= $generator->getNameAttribute() ?> . ', ' . <?= $generator->generateString('Edit') ?>;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '<?= Inflector::pluralize(
    Inflector::camel2words(StringHelper::basename($generator->modelClass))
) ?>'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model-><?= $generator->getNameAttribute()
?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Edit') ?>;
?>
<div class="box box-default">
    <div
        class="giiant-crud box-body <?= Inflector::camel2id(
            StringHelper::basename($generator->modelClass),
            '-',
            true
        )
        ?>-update">

        <div class="crud-navigation">
            <?= '<?= ' ?>Html::a(
                '<span class="glyphicon glyphicon-eye-open"></span> ' . <?= $generator->generateString('View') ?>,
                ['view', <?= $urlParams ?>],
                ['class' => 'btn btn-default']
            ) ?>
        </div>

        <?= '<?php ' ?>echo $this->render('_form', [
            'model' => $model,
            'inlineForm' => $inlineForm,
            'relatedTypeForm' => $relatedTypeForm,
<?php if (method_exists(ltrim($generator->modelClass, '\\'), 'getLanguages')) { ?>
            'translations' => $translations,
            'languageCodes' => $languageCodes,
<?php } ?>
        ]); ?>

    </div>
</div>