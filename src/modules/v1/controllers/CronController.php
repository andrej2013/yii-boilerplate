<?php

namespace andrej2013\yiiboilerplate\modules\v1\controllers;

use andrej2013\yiiboilerplate\TwXAuth;
use yii\rest\Controller;
use Yii;
use yii\web\Response;
use Exception;

/**
 * Class CronController
 * @package andrej2013\yiiboilerplate\modules\v1\controllers
 */
abstract class CronController extends Controller
{
    /**
     * Available commands
     * @var array
     */
    protected $availableCommands = ['queue/process'];

    /**
     * @var string path to `yii` script
     */
    public $yiiScript = '@app/../yii';

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
            'only' => ['index'],  // in a controller
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

        $whitelist = getenv('APP_API_WHITELIST');
        if (!empty($whitelist)) {
            $ips = explode(',', $whitelist);
            if (in_array(Yii::$app->getRequest()->getUserIP(), $ips)) {
                unset($behaviors['authenticator']);
            }
        }

        return $behaviors;
    }

    /**
     * Execute a command
     * @param string $command
     * @param array $arguments
     */
    public function actionIndex($command, array $arguments = [])
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!in_array($command, $this->availableCommands)) {
            throw new Exception(Yii::t('app', 'Unavailable command {command}', ['command' => $command]));
        }

        list ($status, $output) = $this->runConsole($command, $arguments);

        echo date('d.m.Y H:i:s') . " job done: $command\nResult: $output\n";
    }

    /**
     * Runs console command
     *
     * @param string $command
     *
     * @return array [status, output]
     */
    private function runConsole($command, array $arguments = [])
    {
        $args = '';
        if (!empty($arguments) || $arguments != null) {
            foreach ($arguments as $key => $value) {
                $args .= '--' . $key . '=' . $value . ' ';
            }
            $args = rtrim($args, ' ');
        }

        // Move to the correct place
        $root = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/..';
        $phpBin = getenv('PHP_BIN') ?: 'php';
        $cmd = "cd $root && $phpBin yii $command $args";

        $output = shell_exec($cmd);
        $status = 0;

        return [$status, $output];
    }
}
