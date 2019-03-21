<?php


/**
 * @var $model \andrej2013\yiiboilerplate\modules\faq\models\Faq
 * @var $this  \yii\web\View
 */

if ($title) {
    $this->title = $title;
}
if ($breadcrumbs) {
    $this->params['breadcrumbs'][] = $breadcrumbs;
}
$faq_id = $id ? $id : 0;
$this->registerCss("
.panel-heading .accordion-toggle:after {
    /* symbol for \"opening\" panels */
    font-family: 'Glyphicons Halflings';  /* essential for enabling glyphicon */
    content: \"\\e114\";    /* adjust as needed, taken from bootstrap.css */
    float: right;        /* adjust as needed */
    color: white;         /* adjust as needed */
}
.panel-heading .accordion-toggle.collapsed:after {
    /* symbol for \"collapsed\" panels */
    content: \"\\e080\";    /* adjust as needed, taken from bootstrap.css */
}
");
/**
 * @param $data
 * @param $id
 * @param $selectedFaqId
 */
function buildLevel($data, $id, $selectedFaqId)
{
    if (! empty($data[$id])) {
        foreach ($data[$id] as $nestedModel):?>
            <!-- Panel -->
            <div class="panel panel-primary">
                <!-- Header -->
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <a data-toggle="collapse" class="accordion-toggle collapsed"
                           data-parent="#<?= 'collapse_faq_' . $id ?>"
                           href="#collapse_faq_<?= $nestedModel->id ?>"><?= $nestedModel->title ?></a>
                    </h3>
                </div>
                <div id="<?= 'collapse_faq_' . $nestedModel->id ?>"
                     class="panel-collapse collapse <?= $selectedFaqId == $nestedModel->id ? 'in' : ''; ?>">
                    <!-- Content -->
                    <div class="panel-body">
                        <p><?= $nestedModel->content ?></p>
                        <?= buildLevel($data, $nestedModel->id, $selectedFaqId); ?>
                    </div>
                </div>
            </div>
        <?php endforeach;
    }
}

?>
<?php if ($faqs): ?>
    <div class="panel-group" id="accordion">
        <?php foreach ($faqs[0] as $model) : ?>
            <!-- Panel -->
            <div class="panel panel-primary">
                <!-- Header -->
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" class="accordion-toggle collapsed"
                           href="#collapse_faq_<?= $model->id ?>"><?= $model->title ?></a>
                    </h3>
                </div>
                <div id="<?= 'collapse_faq_' . $model->id ?>"
                     class="panel-collapse collapse <?= $faq_id == $model->id ? 'in' : ''; ?>">
                    <!-- Content -->
                    <div class="panel-body">
                        <p><?= $model->content ?></p>
                        <?= buildLevel($faqs, $model->id, $faq_id); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <h4><?= Yii::t('faq', 'No FAQ available'); ?></h4>
<?php endif; ?>


