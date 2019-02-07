<?php
/**
 * Created by PhpStorm.
 * User: ben-g
 * Date: 05.02.2016
 * Time: 12:19
 */
namespace andrej2013\yiiboilerplate\rest;

use andrej2013\yiiboilerplate\helpers\DebugHelper;
use andrej2013\yiiboilerplate\rest\exceptions\MissingCurrentDatetimeException;
use andrej2013\yiiboilerplate\TwXAuth;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use Yii;

/**
 * Class TwActiveController
 * @package andrej2013\yiiboilerplate\rest
 */
class TwActiveController extends \yii\rest\ActiveController
{
    /**
     * Override the Actions to add the Search capability
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'class' => \andrej2013\yiiboilerplate\rest\actions\IndexAction::className()
            ],
            'create' => [
                'class' => \andrej2013\yiiboilerplate\rest\actions\CreateAction::className()
            ],
            'update' => [
                'class' => \andrej2013\yiiboilerplate\rest\actions\UpdateAction::className()
            ],
            'batch' => [
                'class' => \andrej2013\yiiboilerplate\rest\actions\BatchAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ]
        ]);
    }

    /**
     * Override the verbs to add the Batch function.
     * @return array
     */
    protected function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'batch' => ['POST']
        ]);
    }

    /**
     * Add our behaviors for the API
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'only' => ['view', 'index'],  // in a controller
            // if in a module, use the following IDs for user actions
            // 'only' => ['user/view', 'user/index']
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
            'languages' => [
                'en',
                'de',
            ],
        ];

        // We only want authenticator for non-testing environments for the moment.
        // todo: add option to enable authenticator even if in test?
        if (YII_ENV != 'test') {
            $behaviors['authenticator'] = [
                'class' => TwXAuth::className(),
            ];
        }
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Expose-Headers' => [],
                'Access-Control-Max-Age' => 86400,
            ],
        ];

        $whitelist = getenv('APP_API_WHITELIST');
        if (!empty($whitelist)) {
            $ips = explode(',', $whitelist);
            if (in_array(Yii::$app->getRequest()->getUserIP(), $ips)) {
                unset($behaviors['authenticator']);
            }
        }

        return $behaviors;
    }
}
