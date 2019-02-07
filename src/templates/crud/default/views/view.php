<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var andrej2013\yiiboilerplate\templates\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass();
$model->setScenario('crud');

$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->getTableSchema()->columnNames;
}
$hiddenAttributes = [
    'created_by',
    'created_at',
    'updated_by',
    'updated_at',
    'deleted_by',
    'deleted_at',];
// Remove _by and _at attributes from generating;
$safeAttributes = array_diff($safeAttributes, $hiddenAttributes);
$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 * @var boolean $useModal
 */
$copyParams = $model->attributes;

$this->title = Yii::t('app', '<?=
Inflector::camel2words(
     StringHelper::basename($generator->modelClass)
) ?>') . ' ' . $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '<?=
Inflector::pluralize(
    Inflector::camel2words(StringHelper::basename($generator->modelClass))
) ?>'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model-><?= $generator->getNameAttribute()
?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('View') ?>;
?>
<div class="box box-default">
    <div class="giiant-crud box-body" id="<?= Inflector::camel2id(
        StringHelper::basename($generator->modelClass),
        '-',
        true
    )
    ?>-view">

        <!-- flash message -->
        <?= "<?php if (Yii::\$app->session->getFlash('deleteError') !== null) : ?>
            <span class=\"alert alert-info alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                    <span aria-hidden=\"true\">&times;</span>
                </button>
                <?= Yii::\$app->session->getFlash('deleteError') ?>
            </span>
        <?php endif; ?>" ?>

        <?="<?php if (!\$useModal) : ?>\n"?>
            <div class="clearfix crud-navigation">
                <!-- menu buttons -->
                <div class='pull-left'>
                    <?= '<?= ' ?>Yii::$app->getUser()->can(
                        Yii::$app->controller->module->id .
                        '_' .
                        Yii::$app->controller->id .
                        '_update' && $model->editable()
                    ) ?
                        Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span> ' . <?= $generator->generateString('Edit') ?>,
                            ['update', <?= $urlParams ?>],
                            ['class' => 'btn btn-info']
                        )
                    :
                        ''
                    ?>
                    <?= '<?= ' ?>Yii::$app->getUser()->can(
                        Yii::$app->controller->module->id .
                        '_' .
                        Yii::$app->controller->id .
                        '_create'
                    ) ?
                        Html::a(
                            '<span class="glyphicon glyphicon-copy"></span> ' . <?= $generator->generateString('Copy') ?>,
                            ['create', <?= $urlParams ?>, '<?= StringHelper::basename(
                                $generator->modelClass
                            ) ?>' => $copyParams],
                            ['class' => 'btn btn-success']
                        )
                    :
                        ''
                    ?>
                    <?= '<?= ' ?>Yii::$app->getUser()->can(
                        Yii::$app->controller->module->id .
                        '_' .
                        Yii::$app->controller->id .
                        '_create'
                    ) ?
                        Html::a(
                            '<span class="glyphicon glyphicon-plus"></span> ' . <?= $generator->generateString('New') ?>,
                            ['create'],
                            ['class' => 'btn btn-success']
                        )
                    :
                        ''
                    ?>
                </div>
                <div class="pull-right">
                    <?= '<?= ' ?>Html::a(
                        '<span class="glyphicon glyphicon-list"></span> ' . <?= $generator->generateString(
                            'List ' .
                            Inflector::pluralize(StringHelper::basename($generator->modelClass))
                        ) ?>,
                        ['index'],
                        ['class' => 'btn btn-default']
                    ) ?>
                </div>
            </div>
        <?= '<?php endif; ?>' ?>

        <?php
        echo "<?php \$this->beginBlock('{$generator->modelClass}'); ?>\n";
        ?>

        <?= $generator->partialView('detail_prepend', $model); ?>

        <?= '<?= ' ?>DetailView::widget([
            'model' => $model,
            'attributes' => [
<?php
foreach ($safeAttributes as $attribute) {
    $format = $generator->attributeFormat($attribute);
    if (!$format) {
        continue;
    } else {
        echo $format . ",\n";
    }
}
?>
            ],
        ]); ?>

        <?= $generator->partialView('detail_append', $model); ?>

        <hr/>

        <?= '<?= ' ?>Yii::$app->getUser()->can(
            Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_delete'
        ) && $model->deletable() ?
            Html::a(
                '<span class="glyphicon glyphicon-trash"></span> ' . <?= $generator->generateString('Delete') ?>,
                $useModal ? false : ['delete', <?= $urlParams ?>],
                [
                    'class' => 'btn btn-danger' . ($useModal ? ' ajaxDelete' : ''),
                    'data-url' => Url::toRoute(['delete', <?= $urlParams ?>]),
                    'data-confirm' => $useModal ? false : <?= $generator->generateString('Are you sure to delete this item?') ?>,
                    'data-method' => $useModal ? false : 'post',
                ]
            )
        :
            ''
        ?>

        <?= "<?php \$this->endBlock(); ?>\n\n"; ?>

        <?php

        // get relation info $ prepare add button
        $model = new $generator->modelClass();

        $items = <<<EOS
[
                        'label' => '<b class=""># ' . \$model->{$model->primaryKey()[0]} . '</b>',
                        'content' => \$this->blocks['{$generator->modelClass}'],
                        'active' => true,
                    ],
EOS;

        foreach ($generator->getModelRelations($generator->modelClass, ['has_many']) as $name => $relation) {
            // Replace name of pivot tables with numbers
            $end = substr($name, -2, 1);
            $last = substr($name, -1);
            if (is_numeric($end) && ($last == 's')) {
                $name = substr($name, 0, strlen($name)-1);
            }
            $modelName = $name;

            // build tab items
            $label = Inflector::camel2words($name);

            // Make better label for numerical relations
            if (is_numeric(substr($label, -1))) {
                $link = Inflector::camel2words(key($relation->link));
                $label = substr($label, 0, strlen($label)-2) .
                    ' ' .
                    str_ireplace(' Id', null, Inflector::pluralize($link));
                $modelName = substr($name, 0, strlen($name)-1);
            }

            echo "\n" . str_repeat(' ', 8) . "<?php \$this->beginBlock('$label'); ?>\n";
            $showAllRecords = false;
            if ($relation->via !== null) {
                $pivotName = Inflector::pluralize($generator->getModelByTableName($relation->via->from[0]));
                $pivotRelation = $model->{'get' . $pivotName}();
                $pivotPk = key($pivotRelation->link);
                $addButton = str_repeat(' ', 12) .
                    "<?= Html::a(
                '<span class=\"glyphicon glyphicon-link\"></span> ' . " .
                    $generator->generateString('Attach') .
                    " . ' " .
                    Inflector::singularize(Inflector::camel2words($label)) .
                    "',\n" .
                    str_repeat(' ', 16) .
                    "['" . $generator->createRelationRoute($pivotRelation, 'create') .
                    "', '" .
                    Inflector::singularize($pivotName) .
                    "' => ['" . key($pivotRelation->link) .
                    "'=>\$model->{$model->primaryKey()[0]}]],
                ['class'=>'btn btn-info btn-xs']
            ) ?>\n";
            } else {
                $addButton = '';
            }

            // relation list, add, create buttons
            echo str_repeat(' ', 8) . "<?php if (\$useModal !== true) : ?>\n";
            ?>
        <div style='position: relative'>
            <div style='position:absolute; right: 0px; top: 0px;'>
                <?= "<?=" ?>Html::a(
                    '<span class="glyphicon glyphicon-list"></span>' . <?=$generator->generateString('List All')?> .
                    ' ' .
                    <?=$generator->generateString(Inflector::camel2words($label))?>,
                    ['<?=$generator->createRelationRoute($relation, 'index')?>'],
                    ['class' => 'btn text-muted btn-xs']
                )
                ?>
<?php
// TODO: support multiple PKs
?>
                <?= "<?=" ?>Html::a(
                    '<span class=\"glyphicon glyphicon-plus\"></span>' . <?=$generator->generateString('New') ?> .
                    ' ' .
                    <?=$generator->generateString(Inflector::singularize(Inflector::camel2words($label))) ?>,
                    [
                        '<?=$generator->createRelationRoute($relation, 'create')?>',
                        '<?=Inflector::singularize($modelName)?>' => [
                            '<?=key($relation->link)?>' => $model-><?=$model->primaryKey()[0] . "\n"?>
                        ]
                    ],
                    ['class' => 'btn btn-success btn-xs']
                )
                ?>
<?= $addButton ?>
            </div>
        </div>
        <?= '<?php endif; ?>' . "\n" ?>
        <div class="clearfix"></div>
<?php
    // render pivot grid
if ($relation->via !== null) {
    $pjaxId = "pjax-{$pivotName}";
    $gridRelation = $pivotRelation;
    $gridName = $pivotName;
} else {
    $pjaxId = "pjax-{$name}";
    $gridRelation = $relation;
    $gridName = $name;
}
$output = $generator->relationGrid($gridName, $gridRelation, $showAllRecords);
    // render relation grid
if (!empty($output)) :
        ?>
        <div class="table-responsive">
        <?= '<?=' . $output . "?>\n" ?>
        </div>
        <?php
endif;

    echo str_repeat(' ', 8) . "<?php \$this->endBlock() ?>\n\n";

    $items .="\n";
    $items .= <<<EOS
                    [
                        'content' => \$this->blocks['$label'],
                        'label' => '<small>' .
                            Yii::t('app', '$label') .
                            '&nbsp;<span class="badge badge-default">' .
                            count(\$model->get{$name}()->asArray()->all()) .
                            '</span></small>',
                        'active' => false,
                    ],
EOS;
        }
        $items .= <<<EOS

                    [
                        'content' => \\andrej2013\\yiiboilerplate\\widget\\HistoryTab::widget(['model' => \$model]),
                        'label' => '<small>' .
                            Yii::t('app', 'History') .
                            '&nbsp;<span class="badge badge-default">' .
                            count(\$model->getHistory()->asArray()->all()) .
                            '</span></small>',
                        'active' => false,
                        'visible' => Yii::\$app->user->can('Administrator'),
                    ],
EOS;
        ?>

        <?=
    // render tabs
    "<?= Tabs::widget(
            [
                'id' => 'relation-tabs',
                'encodeLabels' => false,
                'items' => [
                    $items
                ]
            ]
        );
        ?>";
?>

        <?= "<?= andrej2013\\yiiboilerplate\\RecordHistory::widget(['model' => \$model]) ?>\n" ?>
    </div>
</div>
