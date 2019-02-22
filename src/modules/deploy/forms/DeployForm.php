<?php

namespace andrej2013\yiiboilerplate\modules\deploy\forms;

use yii\base\Model;

class DeployForm extends Model
{
    /**
     * @var array
     */
    private $commands = [];

    /**
     * @var string
     */
    private $execText = '';

    /**
     * @var string
     */
    public $branch;

    /**
     * @var bool
     */
    public $enableComposer;

    /**
     * @var bool
     */
    public $forceExecuteCommands;

    /**
     * @var bool
     */
    public $installComposerAssetPlugin;

    /**
     * @var string
     */
    public $phpBin;

    /**
     * @var string
     */
    public $composerBin;

    /**
     * @var string
     */
    public $gitBin;

    /**
     * @var string
     */
    public $composerHome;

    public function init()
    {
        $module = \Yii::$app->controller->module;

        $this->attributes = [
            'branch'                     => $module->branch,
            'enableComposer'             => $module->enableComposer,
            'forceExecuteCommands'       => $module->forceExecuteCommands,
            'installComposerAssetPlugin' => $module->installComposerAssetPlugin,
            'phpBin'                     => $module->phpBin,
            'composerBin'                => $module->composerBin,
            'gitBin'                     => $module->gitBin,
            'composerHome'               => $module->composerHome,
        ];
    }

    public function rules()
    {
        return [
            [['branch', 'enableComposer', 'forceExecuteCommands', 'phpBin', 'composerBin', 'gitBin', 'composerHome'], 'required'],
            [['branch', 'phpBin', 'composerBin', 'gitBin', 'composerHome'], 'string'],
            [['enableComposer', 'forceExecuteCommands', 'installComposerAssetPlugin'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'branch'                     => Yii::t('app', 'Branch'),
            'enableComposer'             => Yii::t('app', 'Execute command `composer update`'),
            'forceExecuteCommands'       => Yii::t('app', 'Force execute of commands'),
            'installComposerAssetPlugin' => Yii::t('app', 'Install packgage `fxp/composer-asset-plugin`'),
            'phpBin'                     => Yii::t('app', 'Path to PHP'),
            'composerBin'                => Yii::t('app', 'Path to Composer'),
            'gitBin'                     => Yii::t('app', 'Path to GIT'),
            'composerHome'               => Yii::t('app', 'Directory of composer'),
        ];
    }


    public function deploy()
    {
        if (YII_ENV_PROD)
            $this->forceExecuteCommands = true;

        $path = \Yii::getAlias('@root');

        $this->commands = [
            "cd {$path}",
            "{$this->gitBin} checkout -f 2>&1",
            "{$this->gitBin} pull origin {$this->branch} 2>&1",
        ];

        $this->addComposerCommands();
        $this->addYiiMigration();
        $this->executeCommands();
    }

    private function addYiiMigration()
    {
        $path = \Yii::getAlias('@root/');
        $this->commands[] = "cd {$path} && {$this->phpBin} yii migrate --interactive=0 2>&1";
    }

    private function addComposerCommands()
    {
        if (! $this->enableComposer)
            return;

        $path = \Yii::getAlias('@root');
        $composerLockFile = \Yii::getAlias('@root/../composer.lock');

        if (! file_exists($composerLockFile))
            $this->commands[] = "cd {$path} && {$this->phpBin} {$this->composerBin} install --no-interaction 2>&1"; else
            $this->commands[] = "cd {$path} && {$this->phpBin} {$this->composerBin} update --no-interaction 2>&1";
    }

    private function executeCommands()
    {
        //        if (YII_ENV_DEV && !$this->forceExecuteCommands) {
        //            $this->execText .= '[INFO] Ambiente de desenvolvimento não faz execução dos comandos. =)';
        //            return;
        //        }

        foreach ($this->commands as $command) {
            $this->execText .= "<strong>" . $command . "</strong>" . PHP_EOL;
            $this->execText .= shell_exec($command) . PHP_EOL;
            $this->execText .= '-----------------------------------' . PHP_EOL;
        }
        $this->execText .= 'Current commit hash: ' . shell_exec("{$this->gitBin} rev-parse --short HEAD");
        $this->registerLog();
    }

    private function registerLog()
    {
        $logPath = \Yii::getAlias('@runtime/deploy');

        if (! is_dir($logPath))
            mkdir($logPath, 0777, true);

        $filename = "deploy-" . date('Y-m-d-H:i:s') . '.txt';
        $logText = "Date/Time: " . date('Y-m-d H:i:s') . PHP_EOL;
        $logText .= $this->execText;

        $handle = fopen($logPath . DIRECTORY_SEPARATOR . $filename, 'a+');
        fwrite($handle, $logText);
        fclose($handle);
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @return string
     */
    public function getExecText()
    {
        return $this->execText;
    }

    private function addComposerAssetPlugin()
    {
        if (! $this->installComposerAssetPlugin)
            return;

        $this->commands[] = "{$this->phpBin} {$this->composerBin} global require \"fxp/composer-asset-plugin:^1.3.1\" 2>&1";
    }


}