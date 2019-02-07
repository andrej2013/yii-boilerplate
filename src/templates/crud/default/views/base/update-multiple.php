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
 * @var array $pk
 * @var array $show
 */

$this->title = '<?= Inflector::camel2words(
     StringHelper::basename($generator->modelClass)
) ?> ' . $model-><?= $generator->getNameAttribute() ?> . ', ' . <?= $generator->generateString('Edit Multiple') ?>;
$this->params['breadcrumbs'][] = ['label' => '<?= Inflector::pluralize(
    Inflector::camel2words(StringHelper::basename($generator->modelClass))
) ?>', 'url' => ['index']];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Edit Multiple') ?>;
?>
<div class="box box-default">
    <div
        class="giiant-crud box-body <?= Inflector::camel2id(
            StringHelper::basename($generator->modelClass),
            '-',
            true
        )
        ?>-update">

        <h1>
            <?= '<?= ' . $generator->generateString(
                Inflector::camel2words(StringHelper::basename($generator->modelClass))
            ) .
            ' ?>' .
            "\n"
            ?>
        </h1>

        <div class="crud-navigation">
        </div>

        <?= '<?php ' ?>echo $this->render('_form', [
            'model' => $model,
            'pk' => $pk,
            'show' => $show,
            'multiple' => true,
            'useModal' => $useModal,
            'action' => $action,
        ]); ?>

    </div>
</div>