<?php

namespace andrej2013\yiiboilerplate\modules\v1\controllers;

use andrej2013\yiiboilerplate\helpers\DebugHelper;
use andrej2013\yiiboilerplate\rest\TwActiveController;
use andrej2013\yiiboilerplate\TwXAuth;
use yii\rest\Controller;
use Yii;
use andrej2013\yiiboilerplate\rest\exceptions\MissingCurrentDatetimeException;
use yii\web\Response;
use DateTime;
use DateTimeZone;

/**
 * Class SyncController
 * @package andrej2013\yiiboilerplate\modules\v1\controllers
 */
abstract class SyncController extends Controller
{
    /**
     * @var array list of models to add to the sync data
     */
    protected $models = [];

    /**
     * Override the Actions to add the Search capability
     */
    public function actions()
    {
        return [
            'index',
            // Need to support options for Ionic apps
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ]
        ];
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
        $behaviors['authenticator'] = [
            'class' => TwXAuth::className(),
        ];
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

        // Disable the authenticator for OPTION (Cors Preflight)
        if (Yii::$app->request->isOptions) {
            unset($behaviors['authenticator']);
        }

        return $behaviors;
    }

    /**
     * Get all new models since last sync
     * @param null $lastUpdatedAt
     * @return array sync data
     */
    public function actionIndex($lastUpdatedAt = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $lastUpdatedAt = $this->getServerLastUpdatedAt($lastUpdatedAt);

        // Calculate difference from "now" of request with "now" of server
        $serverTime = new \DateTime();
        $differenceInSeconds = ($serverTime->getTimestamp() - $this->getClientDateTime()->getTimestamp());

        // Build the data
        $syncData = [
            'difference' => $differenceInSeconds,
            'models' => []
        ];

        // Loop the models and add them
        foreach ($this->getModels() as $modelName) {
            $model = Yii::createObject($modelName);
            $syncData['models'][$model->tableName] = $modelName::find(false) // Find also soft-deleted models
                ->updated($lastUpdatedAt)
                ->api()
                ->all();
            //$newData = $table['class'];
        }

        return $syncData;
    }

    /**
     * Get the last sync with the server
     * @param $lastUpdatedAt
     * @return DateTime|null
     */
    protected function getServerLastUpdatedAt($lastUpdatedAt)
    {
        // Determine last updated at
        if (empty($lastUpdatedAt)) {
            return null;
        }

        // Replace a space with a plus. Yii does this.
        $lastUpdatedAt = new \DateTime(str_replace(' ', '+', $lastUpdatedAt));
        $lastUpdatedAt->setTimeZone(new DateTimeZone(Yii::$app->getTimeZone()));
        return $lastUpdatedAt;
    }

    /**
     * Function to be overwritten in app sync controller to define the models
     * to be added to the sync dump.
     *
     * @return array
     */
    protected function getModels()
    {
        return [];
    }

    /**
     * Get the client's datetime as sent in the header.
     * @return \DateTime
     * @throws MissingCurrentDatetimeException
     */
    protected function getClientDateTime()
    {
        $currentDate = Yii::$app->getRequest()->getHeaders()->get('X-CURRENT-DATETIME');
        if (empty($currentDate) || !is_string($currentDate)) {
            //throw new MissingCurrentDatetimeException('Missing X-CURRENT-DATETIME header in request.');
        }

        return new \DateTime($currentDate);
    }
}
