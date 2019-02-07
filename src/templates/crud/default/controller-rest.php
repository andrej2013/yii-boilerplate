<?php
/**
 * Customizable controller class.
 * @var andrej2013\yiiboilerplate\templates\crud\Generator $generator
 * @var string $controllerClassName
 */
echo "<?php\n";
?>

namespace app\modules\<?= $generator->controllerApiModule ?>\controllers;

/**
 * This is the class for REST controller "<?= $controllerClassName ?>".
 */

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use andrej2013\yiiboilerplate\rest\TwActiveController;

class <?= $controllerClassName ?> extends TwActiveController
{
    public $modelClass = '<?= $generator->modelClass ?>';
<?php if ($generator->accessFilter) : ?>

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return \Yii::$app->user->can(
                                    $this->module->id . '_' . $this->id . '_' . $action->id,
                                    ['route' => true]
                                );
                            },
                        ]
                    ]
                ]
            ]
        );
    }
<?php endif; ?>
}
