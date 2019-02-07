<?php
namespace andrej2013\yiiboilerplate\modules\backend\widgets;

use Yii;
use dektrium\rbac\widgets\Assignments as BaseAssignments;
use andrej2013\yiiboilerplate\modules\backend\models\Assignment;

class Assignments extends BaseAssignments
{
    /** @var integer ID of the user to whom auth items will be assigned. */
    public $userId;

    /** @inheritdoc */
    public function run()
    {
        $model = Yii::createObject([
            'class'   => Assignment::className(),
            'user_id' => $this->userId,
        ]);

        if ($model->load(\Yii::$app->request->post())) {
            $model->updateAssignments();
        }

        return $this->render('@dektrium/rbac/widgets/views/form', [
            'model' => $model,
        ]);
    }
}