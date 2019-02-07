<?php
/**
 * Customizable controller class.
 * @var andrej2013\yiiboilerplate\templates\crud\Generator $generator
 * @var string $controllerClassName
 */
echo "<?php\n";
?>

namespace <?= \yii\helpers\StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * This is the class for controller "<?= $controllerClassName ?>".
 */
class <?= $controllerClassName ?> extends \<?= \yii\helpers\StringHelper::dirname(
    ltrim(
        $generator->controllerClass,
        '\\'
    )
) . '\base\\' . $controllerClassName."\n"
?>
{
//    /**
//     * Model class with namespace
//     */
//    public $model = '<?= $generator->modelClass ?>';
//
//    /**
//     * Search Model class
//     */
//    public $searchModel = '<?= $generator->searchModelClass ?>';

//    /**
//     * Additional actions for controllers, uncomment to use them
//     * @inheritdoc
//     */
//    public function behaviors()
//    {
//        return ArrayHelper::merge(parent::behaviors(), [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'actions' => [
//                            'list-of-additional-actions',
//                        ],
//                        'roles' => ['@']
//                    ]
//                ]
//            ]
//        ]);
//    }
}
