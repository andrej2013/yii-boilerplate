<div class="panel panel-default">
    <div class="panel-body">
        <div class="record-history text-muted">
            <?php
            if ($created_by != null) {
                echo \yii\helpers\Html::tag('div', Yii::t("app", "Created by") . ': ' . $created_by->username . ' (' . Yii::$app->formatter->asDatetime($model->created_at) . ')', ['class' => 'col-sm-12']);
            }
            if ($updated_by != null) {
                echo \yii\helpers\Html::tag('div', Yii::t("app", "Updated by") . ': ' . $updated_by->username . ' (' . Yii::$app->formatter->asDatetime($model->updated_by) . ')', ['class' => 'col-sm-12']);
            }
            if ($deleted_by != null) {
                echo \yii\helpers\Html::tag('div', Yii::t("app", "Deleted by") . ': ' . $deleted_by->username . ' (' . Yii::$app->formatter->asDatetime($model->deleted_by) . ')', ['class' => 'col-sm-12']);
            }
            ?>
        </div>
    </div>
</div>