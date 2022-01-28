<?php

namespace andrej2013\yiiboilerplate;

use app\models\User;
use dektrium\rbac\RbacWebModule;
use yii\base\BootstrapInterface;
use yii\base\Application;
use Yii;
use schmunk42\giiant\generators\crud\callbacks\base\Callback;
use schmunk42\giiant\generators\crud\callbacks\yii\Db;
use schmunk42\giiant\generators\crud\callbacks\yii\Html;
use yii\helpers\ArrayHelper;
use andrej2013\yiiboilerplate\modules\user\controllers\SecurityController;
use yii\base\Event;
use dektrium\user\events\AuthEvent;
use yii\web\UrlRule;


class Bootstrap implements BootstrapInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param $app Application
     */
    public function bootstrap($app)
    {
        $this->app = $app;
        \dmstr\widgets\Menu::$iconClassPrefix = '';
        // Before the rest, we want to set up some aliases
        $this->setupAliases();
        Yii::$classMap['yii\helpers\Html'] = '@vendor/andrej2013/yii-boilerplate/src/helpers/Html.php';
        Yii::$classMap['yii\bootstrap\Html'] = '@vendor/andrej2013/yii-boilerplate/src/helpers/BootstrapHtml.php';
        $this->setupCommonConfig($app);
        if ($app instanceof \yii\web\Application) {
            $this->setupWeb($app);
        }
        if ($app instanceof \yii\console\Application) {
            $this->configureConsole($app);
        }
        // Register Settings
        $this->registerConfig($app);
        $this->registerViews($app);

        $this->setupParams($app);
        $this->setupMigrations($app);
    }

    /**
     * Register Aliases for the app
     */
    protected function setupAliases()
    {
        Yii::setAlias('@andrej2013-boilerplate', '@vendor/andrej2013/yii-boilerplate/src/');
        Yii::setAlias('@andrej2013-views', '@vendor/andrej2013/yii-boilerplate/src/views');
        Yii::setAlias('@andrej2013-backend-views', '@vendor/andrej2013/yii-boilerplate/src/modules/backend/views');
    }

    /**
     * @param $app Application
     *             Moved all general common configuration from starter-kit to here
     */
    protected function setupCommonConfig($app)
    {

        $this->setupCommonComponents($app);

        $this->setupCommonModules($app);

        // Register Gii and Giiant
        $this->configGiiant($app);
        $this->configGii($app);
    }

    /**
     * @param $app Application
     *             Moved all general web configuration from starter-kit to here
     */
    protected function setupWeb($app)
    {
        $app->get('request')->cookieValidationKey = getenv('APP_COOKIE_VALIDATION_KEY');
        $app->get('user')->identityClass = User::class;
        $this->setupWebComponents($app);
        $this->setupWebModules($app);
        $this->setupElfinder($app);
    }

    /**
     * @param $app Application
     *             Moved all general console configuration from starter-kit to here
     */
    protected function configureConsole($app)
    {
        if (Yii::$app instanceof \yii\console\Application) {
            $this->registerConsoleComponents($app);
            $this->registerConsoleModules($app);
            $app->controllerNamespace = 'app\commands';
            $app->controllerMap = array_merge([
                'db'        => \dmstr\console\controllers\MysqlController::class,
                'migrate'   => [
                    'class'        => \dmstr\console\controllers\MigrateController::class,
                    'templateFile' => '@andrej2013-boilerplate/views/migration.php',
                ],
                'translate' => '\lajax\translatemanager\commands\TranslatemanagerController',
            ], $app->controllerMap);
        }
    }

    /**
     * Register View data
     * @param Application $app
     */
    protected function registerViews(Application $app)
    {
        // Override dektrium user views with ours
        if (! isset($app->view->theme->pathMap['@dektrium/user/views/admin'])) {
            $app->view->theme->pathMap['@dektrium/user/views/admin'] = '@andrej2013-backend-views/users/admin';
        }
    }

    /**
     * Register changed to the application
     * @param Application $app
     */
    protected function registerConfig(Application $app)
    {

    }

    public function setupParams(Application $app)
    {
        $app->params += [
            'adminEmail'        => getenv('APP_ADMIN_EMAIL'),
            'adminSkin'         => 'skin-blue',
            'style'             => [
                'flat'            => true,
                'primary_color'   => \yii\helpers\Html::TYPE_PRIMARY,
                'secondary_color' => \yii\helpers\Html::TYPE_SUCCESS,
                'danger_color'    => \yii\helpers\Html::TYPE_DANGER,
            ],
            'yii.migrations'    => [
                getenv('APP_MIGRATION_LOOKUP'),
                '@yii/rbac/migrations',
                '@dektrium/user/migrations',
                '@vendor/lajax/yii2-translate-manager/migrations',
                '@vendor/pheme/yii2-settings/migrations',
            ],
            // Presets for CkEditor
            'richTextSimple'    => [
                'source',
                '|',
                'undo',
                'redo',
                'selectall',
                '|',
                'preview',
                'print',
                '|',
                'fontname',
                'fontsize',
                'bold',
                'italic',
                'underline',
                'strikethrough',
                '|',
                'justifyleft',
                'justifycenter',
                'justifyright',
                '|',
            ],
            'richTextStandard'  => [
                'insertorderedlist',
                'insertunorderedlist',
                'indent',
                'outdent',
                '|',
                'formatblock',
                'forecolor',
                'hilitecolor',
                'hr',
                '|',
            ],
            'richTextAdvanced'  => [
                'subscript',
                'superscript',
                'clearhtml',
                'quickformat',
                '|',
                'image',
                'multiimage',
                'flash',
                'media',
                'insertfile',
                '|',
                'table',
                'emoticons',
                'pagebreak',
                'anchor',
                'link',
                'unlink',
                '|',
            ],
            'GoogleCredentials' => '@app/config/secret/client_secret.json',
            'disable_external'  => false, // Disable all external calls for apps behind proxies and such
        ];
    }

    /**
     * Override the configuration of the Giiant package
     */
    protected function configGiiant(Application $app)
    {
        if (YII_ENV == 'dev' || YII_ENV == 'test') {

            Yii::$container->set('andrej2013\yiiboilerplate\templates\crud\providers\CallbackProvider', [
                'columnFormats' => [
                    // hide system fields, but not ID in table
                    'created_at$|updated_at$|created_by$|updated_by$|deleted_at$|deleted_by$' => Callback::false(),
                    // hide all TEXT or TINYTEXT columns
                    '.*'                                                                      => Db::falseIfText(),
                ],
                'activeFields'  => [
                    // hide system fields in form
                    'id$'                                                                         => Db::falseIfAutoIncrement(),
                    'id$|created_at$|updated_at$|created_by$|updated_by$|deleted_at$|deleted_by$' => Callback::false(),
                ],
                /*'attributeFormats' => [
                    // render HTML output
                    '_html$' => Html::attribute(),
                ],*/
            ]);

            $app->controllerMap = array_merge($app->controllerMap, [
                'batch' => [
                    'class'                    => \schmunk42\giiant\commands\BatchController::class,
                    'overwrite'                => true,
                    'singularEntities'         => false,
                    'modelNamespace'           => 'app\\models',
                    'modelQueryNamespace'      => 'app\\models\\query',
                    'crudTidyOutput'           => true,
                    'crudAccessFilter'         => true,
                    'crudControllerNamespace'  => 'app\\controllers',
                    'crudSearchModelNamespace' => 'app\\models\\search',
                    'crudViewPath'             => '@app/views',
                    'crudPathPrefix'           => null,
                    'crudMessageCategory'      => 'app',
                    'crudProviders'            => [
                        'andrej2013\\yiiboilerplate\\templates\\crud\\providers\\OptsProvider',
                        'andrej2013\\yiiboilerplate\\templates\\crud\\providers\\CallbackProvider',
                        'andrej2013\\yiiboilerplate\\templates\\crud\\providers\\DateTimeProvider',
                        'andrej2013\\yiiboilerplate\\templates\\crud\\providers\\DateProvider',
                        'andrej2013\\yiiboilerplate\\templates\\crud\\providers\\EditorProvider',
                        'andrej2013\\yiiboilerplate\\templates\\crud\\providers\\RelationProvider',
                    ],
                ],
            ]);
        }
        return true;
    }

    protected function configGii(Application $app)
    {
        if (YII_ENV == 'dev' || YII_ENV == 'test') {
            $this->registerModule('gii', [
                'layout'     => Yii::$app->layout,
                'class'      => \andrej2013\yiiboilerplate\modules\gii\Module::class,
                'allowedIPs' => explode(',', getenv('ALLOWED_IPS')),
                'generators' => [
                    //                    'migrik'         => 'andrej2013\yiiboilerplate\templates\migration\TwGenerator',
                    'giiant-model' => [
                        'class'     => 'andrej2013\yiiboilerplate\templates\model\Generator',
                        'templates' => [
                            'andrej2013' => '@andrej2013-boilerplate/templates/model/default',
                        ],
                    ],
                    'giiant-crud'  => [
                        'class'           => 'andrej2013\yiiboilerplate\templates\crud\Generator',
                        'messageCategory' => 'app',
                        'templates'       => [
                            'andrej2013' => '@andrej2013-boilerplate/templates/crud/default',
                            'adminlte'   => '@andrej2013-boilerplate/templates/crud/adminlte',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * @param Application $app
     * Register migrations lookup paths
     */
    protected function setupMigrations(Application $app)
    {
        $app->params = ArrayHelper::merge([
            'yii.migrations' => [
                getenv('APP_MIGRATION_LOOKUP'),
                '@yii/rbac/migrations',
                '@vendor/lajax/yii2-translate-manager/migrations',
                '@dektrium/user/migrations',
                '@bupy7/activerecord/history/migrations',
                '@andrej2013-boilerplate/migrations',
                '@andrej2013-boilerplate/modules/user/migrations',
            ],
        ], $app->params);
    }

    protected function setupElfinder($app)
    {
        $app->controllerMap += [
            // used for backend (logged users only)
            'elfinder-backend' => [
                'class'            => \mihaildev\elfinder\Controller::class,
                'disabledCommands' => ['netmount'],
                'access'           => ['?', '@'],
                'roots'            => [
                    [
                        'class'     => \andrej2013\yiiboilerplate\components\elfinder\flysystem\Volume::class,
                        'component' => 'fs',
                        'name'      => 'Storage',
                    ],
                    [
                        'class'     => \andrej2013\yiiboilerplate\components\elfinder\flysystem\Volume::class,
                        'component' => 'fs-faq',
                        'name'      => 'Faq',
                    ],
                ],
            ],
            // used for frontend
            'elfinder'         => [
                'class'            => \andrej2013\yiiboilerplate\controllers\ElFinderController::class,
                'disabledCommands' => ['netmount'],
                'access'           => ['?', '@'],
                'roots'            => [
                    [
                        'class'     => \andrej2013\yiiboilerplate\components\elfinder\flysystem\Volume::class,
                        'component' => 'fs',
                        'name'      => 'Storage',
                        'options'   => [
                            'dispInlineRegex' => '^(?:(?:image|text)|application/x-shockwave-flash|application/pdf$)',
                        ],
                    ],
                    [
                        'class'     => \andrej2013\yiiboilerplate\components\elfinder\flysystem\Volume::class,
                        'component' => 'fs_deploy',
                        'name'      => 'Deploy Storage',
                        'options'   => [
                            'dispInlineRegex' => '^(?:(?:image|text)|application/x-shockwave-flash|application/pdf$)',
                        ],
                    ],
                ],
            ],
            // used for Sharing
            'file'             => [
                'class'            => \andrej2013\yiiboilerplate\controllers\ElFinderShareController::class,
                'disabledCommands' => ['netmount'],
                'access'           => ['?', '@'],
                'roots'            => [
                    [
                        'class'     => \andrej2013\yiiboilerplate\components\elfinder\flysystem\Volume::class,
                        'component' => 'fs_seafile',
                        'name'      => 'Storage',
                        'options'   => [
                            'dispInlineRegex' => '^(?:(?:image|text)|application/x-shockwave-flash|application/pdf$)',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param Application $app
     * Components that need to be defined for Web and Console applications
     */
    private function setupCommonComponents(Application $app)
    {
        $this->registerComponent('authManager', [
            'class' => \dektrium\rbac\components\DbManager::class,
        ]);
        // Set the default file cache
        $this->registerComponent('cache', [
            'class' => \yii\caching\FileCache::class,
        ]);

        // Set the filesystem cache. Needs to be DbCache on balanced environments.
        $this->registerComponent('fs_cache', [
            'class' => \yii\caching\FileCache::class,
        ]);
        
        // Note: enable db sessions, if multiple containers are running
        /*$this->registerComponent('session', [
            'class' => \yii\web\DbSession::class
        ]);*/

        $this->registerComponent('fs', [
            'class' => \creocoder\flysystem\LocalFilesystem::class,
            'path'  => '@app/../storage',
        ]);

        $this->registerComponent('fs-faq', [
            'class' => \creocoder\flysystem\LocalFilesystem::class,
            'path'  => '@app/../storage/faq',
        ]);

        $disabledFs = ! empty($app->params['disabled_fs']) && in_array('assetsprod', $app->params['disabled_fs']);
        if (! $disabledFs) {
            $this->registerComponent('fs_assetsprod', [
                'class' => \creocoder\flysystem\LocalFilesystem::class,
                'path'  => '@webroot/storage-public',
            ]);
        }
    }

    /**
     * @param Application $app
     * Modules that need to be defined for Web and Console applications
     */
    private function setupCommonModules(Application $app)
    {
        $this->registerModule('logreader', [
            'class' => \andrej2013\yiiboilerplate\modules\logreader\Module::class,
        ]);
        $this->registerModule('gridview', [
            'class' => \kartik\grid\Module::className(),
        ]);

        $this->registerModule('arhistory', [
            'class' => \bupy7\activerecord\history\Module::className(),
        ]);
        $this->registerModule('attachments', [
            'class'     => \andrej2013\yiiboilerplate\modules\attachments\Module::class,
            'tempPath'  => '@root/runtime/documents_temp',
            'storePath' => '@root/storage/uploads/documents',
            'rules'     => [
                'maxFiles'  => 9999,
                // Allow to upload maximum 99 files, default to 3
                'mimeTypes' => 'image/*, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, text/*',
                // Allow file types
                'maxSize'   => 1024 * 1024 * 10
                // 1 MB
            ],
            'tableName' => '{{%attachments}}' // Optional, default to 'attach_file'
        ]);
    }

    /**
     * @param Application $app
     * Modules that need to be defined only for Web applications
     */
    private function setupWebModules(Application $app)
    {
        $this->registerModule('backend', [
            'class'  => \andrej2013\yiiboilerplate\modules\backend\Module::class,
            'layout' => '@andrej2013-backend-views/layouts/main',
        ]);

        $this->registerModule('datecontrol', [
            'class'           => \kartik\datecontrol\Module::class,
            'ajaxConversion'  => true,
            'displaySettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE     => Yii::$app->formatter->dateFormat,
                \kartik\datecontrol\Module::FORMAT_TIME     => Yii::$app->formatter->timeFormat,
                \kartik\datecontrol\Module::FORMAT_DATETIME => Yii::$app->formatter->datetimeFormat,
            ],
            'saveSettings'    => [
                \kartik\datecontrol\Module::FORMAT_DATE     => 'php:Y-m-d',
                \kartik\datecontrol\Module::FORMAT_TIME     => 'php:H:i:s',
                \kartik\datecontrol\Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],
            'saveTimezone'    => 'UTC',
            'displayTimezone' => 'UTC',
        ]);

        $this->registerModule('rbac', [
            'class'     => RbacWebModule::class,
            'layout'    => '@andrej2013-backend-views/layouts/main',
            'as access' => $app->getBehavior('access'),
        ]);

        $this->registerModule('adminer', [
            'class' => \andrej2013\yiiboilerplate\modules\adminer\Module::class,
        ]);

        $this->registerModule('webshell', [
            'class'               => \samdark\webshell\Module::className(),
            'allowedIPs'          => ['*', '127.0.0.1', '192.168.50.1', '178.220.62.51'],
            'checkAccessCallback' => function (\yii\base\Action $action) {
                return Yii::$app->user->can('Authority');
            },
            'controllerNamespace' => '\andrej2013\yiiboilerplate\modules\webshell\controllers',
            'defaultRoute'        => 'index',
            'viewPath'            => '@andrej2013-boilerplate/modules/webshell/view',
            'yiiScript'           => Yii::getAlias('@root') . '/yii',
        ]);
        $this->registerModule('deploy', [
            'class'          => \app\modules\deploy\DeployModule::class,
            'token'          => getenv('DEPLOY_SECRET_KEY') ?? '0e05da967238924eb92fd8b71bb7a199',
            'enableComposer' => true,
            'gitBin'         => getenv('GIT_PATH') ?? '/usr/bin/git',
            'phpBin'         => getenv('PHP_PATH') ?? '/usr/bin/php',
            'composerBin'    => getenv('COMPOSER_PATH') ?? '/usr/bin/composer',
            'branch'         => getenv('GIT_BRANCH') ?? 'master',
        ]);
    }

    /**
     * @param Application $app
     * @return bool
     * Components that need to be defined only for Web applications
     */
    private function setupWebComponents(Application $app)
    {
        $this->setupAuthClients();
        return false;
    }

    /**
     * @param Application $app
     * @return bool
     * Modules that need to be defined only for Console applications
     */
    private function registerConsoleModules(Application $app)
    {
        return false;
    }

    /**
     * @param Application $app
     * @return bool
     * Components that need to be defined only for Console applications
     */
    private function registerConsoleComponents(Application $app)
    {
        return false;
    }

    /**
     * @param      $component
     * @param      $options
     * @param bool $override We at most times check if component is already defined in application config, so not to
     *                       override, but sometimes we need because some of components are always defined in application
     *                       core
     * @param bool $production
     *                       Should this component need to be enabled for production environment or only for dev and test
     * @return bool
     */
    private function registerComponent($component, $options, $override = false)
    {
        if ($override || ! $this->app->has($component)) {
            $this->app->setComponents([$component => $options]);
        } else {
            $this->app->setComponents([$component => ArrayHelper::merge($options, $this->app->getComponents(true)[$component])]);
        }
    }

    /**
     * @param      $module
     * @param      $options
     * @param bool $override
     *                      We at most times check if module is already defined in application config, so not to
     *                      override, but sometimes we need because some of module are defined by 3rd party vendor
     *                      extensions
     * @return bool
     */
    private function registerModule($module, $options, $override = false)
    {
        if ($override || ! $this->app->hasModule($module)) {
            $this->app->setModules([$module => $options]);
        } else {
            $this->app->setModules([$module => ArrayHelper::merge($options, $this->app->getModules(false)[$module])]);
        }
    }

    /**
     *
     */
    private function setupAuthClients()
    {
        $clients = [];
        if (getenv('FACEBOOK_APP_ID') && getenv('FACEBOOK_SECRET_KEY')) {
            $clients['facebook'] = [
                'class'        => \dektrium\user\clients\Facebook::class,
                'clientId'     => getenv('FACEBOOK_APP_ID'),
                'clientSecret' => getenv('FACEBOOK_SECRET_KEY'),
            ];
        }
        if (getenv('TWITTER_CONSUMER_KEY') && getenv('TWITTER_CONSUMER_SECRET')) {
            $clients['twitter'] = [
                'class'          => \dektrium\user\clients\Twitter::class,
                'consumerKey'    => getenv('TWITTER_CONSUMER_KEY'),
                'consumerSecret' => getenv('TWITTER_CONSUMER_SECRET'),
            ];
        }
        if (getenv('GOOGLE_CLIENT_ID') && getenv('GOOGLE_CLIENT_SECRET')) {
            $clients['google'] = [
                'class'        => \dektrium\user\clients\Google::class,
                'clientId'     => getenv('GOOGLE_CLIENT_ID'),
                'clientSecret' => getenv('GOOGLE_CLIENT_SECRET'),
            ];
        }
        if (getenv('LINKEDIN_CLIENT_ID') && getenv('LINKEDIN_CLIENT_SECRET')) {
            $clients['linkedin'] = [
                'class'        => \dektrium\user\clients\LinkedIn::class,
                'clientId'     => getenv('LINKEDIN_CLIENT_ID'),
                'clientSecret' => getenv('LINKEDIN_CLIENT_SECRET'),
            ];
        }
        if (isset(\Yii::$app->params['authClients'])) {
            $clients = ArrayHelper::merge($clients, Yii::$app->params['authClients']);
        }
        $options = [
            'class'   => \yii\authclient\Collection::class,
            'clients' => $clients,
        ];
        $this->registerComponent('authClientCollection', $options, true);

        // Register events
        Event::on(SecurityController::className(), SecurityController::EVENT_AFTER_AUTHENTICATE, function (AuthEvent $e) {
            // if user account was not created we should not continue
            if ($e->account->user === null) {
                return;
            }
            // we are using switch here, because all networks provide different sets of data
            switch ($e->client->getName()) {
                case 'facebook':
                    $e->account->user->profile->updateAttributes([
                        'name' => $e->client->getUserAttributes()['name'],
                    ]);
                case 'google':
                    $e->account->user->profile->updateAttributes([
                        'name' => $e->client->getUserAttributes()['displayName'],
                    ]);
                case 'twitter':
                    //@TODO
                case 'linkedin':
                    //@TODO
            }

            // after saving all user attributes will be stored under account model
            // Yii::$app->identity->user->accounts['facebook']->decodedData
        });
    }
}
