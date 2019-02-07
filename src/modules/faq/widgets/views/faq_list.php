<?php


/* @var $model \andrej2013\yiiboilerplate\modules\faq\models\Faq */

if ($title) {
    $this->title = $title;
}
if ($breadcrumbs) {
    $this->params['breadcrumbs'][] = $breadcrumbs;
}
$faq_id = $id ? $id : 0;

/**
 * @param $data
 * @param $id
 * @param $selectedFaqId
 */
function buildLevel($data, $id, $selectedFaqId) {
    if (!empty($data[$id])) {
        foreach ($data[$id] as $nestedModel):?>
            <!-- Panel -->
            <div class="panel panel-default">
                <!-- Header -->
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse"
                           data-parent="#<?= 'collapse_faq_' . $id ?>"
                           href="#collapse_faq_<?= $nestedModel->id ?>"><?= $nestedModel->title ?></a>
                    </h4>
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
            <div class="panel panel-default">
                <!-- Header -->
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion"
                           href="#collapse_faq_<?= $model->id ?>"><?= $model->title ?></a>
                    </h4>
                </div>
                <div id="<?= 'collapse_faq_' . $model->id ?>"
                     class="panel-collapse collapse <?= $faq_id == $model->id ? 'in' : ''; ?>">
                    <!-- Content -->
                    <div class="panel-body">
                        <p><?= $model->content ?></p>
                        <?=buildLevel($faqs, $model->id, $faq_id); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <h4><?= Yii::t('faq', 'No FAQ available'); ?></h4>
<?php endif; ?>


