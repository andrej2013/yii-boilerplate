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
// Remove _by and _at attributes from generating;
$safeAttributes = array_diff($safeAttributes, $generator->hidden_attributes);

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use andrej2013\yiiboilerplate\grid\GridView;
use yii\widgets\DetailView;
use dmstr\bootstrap\Tabs;

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 */
$copyParams = $model->attributes;

$this->title = <?= $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?> . ' ' . $model->toString;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->toString, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('View') ?>;
?>
<div class="box box-<?='<?php '; ?>echo \Yii::$app->params['style']['primary_color']; ?>">
    <div class="giiant-crud box-body" id="<?= Inflector::camel2id(
        StringHelper::basename($generator->modelClass),
        '-',
        true
    )
    ?>-view">

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
                            '<span class="fa fa-pencil"></span> ' . <?= $generator->generateString('Edit') ?>,
                            ['update', <?= $urlParams ?>],
                            [
                                'class' => 'btn',
                                'preset'    => Html::PRESET_SECONDARY,
                            ]
                        )
                    :
                        ''
                    ?><?php if ($generator->generateCopyButton) { ?>
                    <?= '<?= ' ?>Yii::$app->getUser()->can(
                        Yii::$app->controller->module->id .
                        '_' .
                        Yii::$app->controller->id .
                        '_create'
                    ) ?
                        Html::a(
                            '<span class="fa fa-copy"></span> ' . <?= $generator->generateString('Copy') ?>,
                            ['create', <?= $urlParams ?>, '<?= StringHelper::basename(
                                $generator->modelClass
                            ) ?>' => $copyParams],
                            [
                                'class' => 'btn',
                                'preset' => Html::PRESET_SECONDARY,
                            ]
                        )
                    :
                        ''
                    ?><?php } ?>
                    <?= '<?= ' ?>Yii::$app->getUser()->can(
                        Yii::$app->controller->module->id .
                        '_' .
                        Yii::$app->controller->id .
                        '_create'
                    ) ?
                        Html::a(
                            '<span class="fa fa-plus"></span> ' . <?= $generator->generateString('New') ?>,
                            ['create'],
                            [
                                'class' => 'btn',
                                'preset' => Html::PRESET_PRIMARY,
                            ]
                        )
                    :
                        ''
                    ?>
                </div>
                <div class="pull-right">
                    <?= '<?= ' ?>Html::a(
                        '<span class="fa fa-list"></span> ' . <?= $generator->generateString(
                            'List ' .
                            Inflector::pluralize(StringHelper::basename($generator->modelClass))
                        ) ?>,
                        ['index'],
                        [
                            'class' => 'btn',
                            'color' => Html::TYPE_DEFAULT,
                        ]
                    ) ?>
                </div>
            </div>

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

        <?= '<?= ' ?>Yii::$app->getUser()->can(
            Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_delete'
        ) && $model->deletable() ?
            Html::a(
                '<span class="fa fa-trash"></span> ' . <?= $generator->generateString('Delete') ?>,
                ['delete', <?= $urlParams ?>],
                [
                    'class' => 'btn',
                    'preset' => Html::PRESET_DANGER,
                    'data-url' => Url::toRoute(['delete', <?= $urlParams ?>]),
                    'data-confirm' => <?= $generator->generateString('Are you sure to delete this item?') ?>,
                    'data-method' => 'post',
                ]
            )
        : ''
        ?>
        <?= "<?php \$this->endBlock(); ?>\n\n"; ?>

        <?php

        // get relation info $ prepare add button
        $model = new $generator->modelClass();

        $items[] = <<<EOS
                    [
                        'label'     => '<b> ' . \$model->toString . '</b>',
                        'content'   => \$this->blocks['{$generator->modelClass}'],
                        'active'    => true,
                    ],
EOS;
        $allPivots = [];
        foreach ($generator->getModelRelations($generator->modelClass, ['has_many']) as $name => $relation) {
            if ($relation->via !== null) {
                $allPivots[] = Inflector::pluralize($generator->getModelByTableName($relation->via->from[0]));
            }
        }
        foreach ($generator->getModelRelations($generator->modelClass, ['has_many']) as $name => $relation) {
            if (in_array($name, $allPivots)) {
                continue;
            }
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

            echo "\n" . "<?php \$this->beginBlock('$label'); ?>\n";
            $showAllRecords = false;
            $attachButton = null;
            if ($relation->via !== null) {
                $pivotName = Inflector::pluralize($generator->getModelByTableName($relation->via->from[0]));
                $pivotRelation = $model->{'get' . $pivotName}();
                $pivotPk = key($pivotRelation->link);
                $attachButton = "Html::a('<span class=\"fa fa-link\"></span> ' . " .
                    $generator->generateString('Attach') .
                    " . ' ' ." .
                    $generator->generateString(Inflector::singularize(Inflector::camel2words($label))) .
                    ",\n" .
                    "['" . $generator->createRelationRoute($pivotRelation, 'create') .
                    "', '" .
                    Inflector::singularize($pivotName) .
                    "' => ['" . key($pivotRelation->link) .
                    "'=>\$model->{$model->primaryKey()[0]}], 'hide' => '".key($pivotRelation->link)."'],
                [
                    'class'=>'btn',
                    'preset' => Html::PRESET_PRIMARY,
                    'data-pjax' => 0,
                ]
            )";
            }

            // relation list, add, create buttons
            ?>
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
$output = $generator->relationGrid($gridName, $gridRelation, $showAllRecords, $attachButton);
    // render relation grid

        ?>
        <div class="table-responsive">
        <?= '<?=' . $output . "?>\n" ?>
        </div>
        <?php
    echo "<?php \$this->endBlock() ?>\n\n";

    $items[] = <<<EOS
                    [
                        'content' => \$this->blocks['$label'],
                        'label' => '<small>' .
                            Yii::t('app', '$label') .
                            '&nbsp;<span class="badge badge-default">' .
                            \$model->get{$name}()->count() .
                            '</span></small>',
                        'active' => false,
                    ],
EOS;
        }
        $items[] = <<<EOS
                    [
                        'content' => \\andrej2013\\yiiboilerplate\\widget\\HistoryTab::widget(['model' => \$model]),
                        'label' => '<small>' .
                            Yii::t('app', 'History') .
                            '&nbsp;<span class="badge badge-default">' .
                            \$model->getHistory()->count() .
                            '</span></small>',
                        'active' => false,
                        'visible' => Yii::\$app->user->can('Administrator'),
                    ],
EOS;
        $model = new $generator->modelClass;
        if ($model->getBehavior('fileBehavior')) {
            $items[] = <<<EOS
                    [
                        'content' => \\andrej2013\\yiiboilerplate\\widget\\Attachment::widget([
                            'model' => \$model,
                            'type'  => \\andrej2013\\yiiboilerplate\\widget\\Attachment::PREVIEW,
                        ]),
                        'label' => '<small>' .
                            Yii::t('app', 'Attachments') .
                            '</small>',
                        'active' => false,
                    ],
EOS;

        }
        $items = implode("\n", $items);
        ?>
        <div class="nav-tabs-custom">
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
        </div>
        <?= "<?= andrej2013\\yiiboilerplate\\widget\\RecordHistory::widget(['model' => \$model]) ?>\n" ?>
    </div>
</div>
