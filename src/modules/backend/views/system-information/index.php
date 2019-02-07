<?php
/**
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Author: Eugine Terentev <eugine@terentev.net>
 * @var $this \yii\web\View
 * @var $provider \probe\provider\ProviderInterface
 */
//use common\models\FileStorageItem;
use app\models\User;

$this->title = Yii::t('app', 'System Information');
$this->registerJs("window.paceOptions = { ajax: false }", \yii\web\View::POS_HEAD);
\yii\web\JqueryAsset::register($this);
\andrej2013\yiiboilerplate\modules\backend\assets\SystemInformationAsset::register($this);
?>
<div id="system-information-index">
    <div class="row connectedSortable">
        <div class="col-lg-6 col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-hdd-o"></i>
                    <h3 class="box-title"><?php echo Yii::t('app', 'Processor') ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo Yii::t('app', 'Processor') ?></dt>
                        <dd><?php echo $provider->getCpuModel() ?></dd>

                        <dt><?php echo Yii::t('app', 'Processor Architecture') ?></dt>
                        <dd><?php echo $provider->getArchitecture() ?></dd>

                        <dt><?php echo Yii::t('app', 'Number of cores') ?></dt>
                        <dd><?php echo $provider->getCpuCores() ?></dd>
                    </dl>
                </div><!-- /.box-body -->
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-hdd-o"></i>
                    <h3 class="box-title"><?php echo Yii::t('app', 'Operating System') ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo Yii::t('app', 'OS') ?></dt>
                        <dd><?php echo $provider->getOsType() ?></dd>

                        <dt><?php echo Yii::t('app', 'OS Release') ?></dt>
                        <dd><?php echo $provider->getOsRelease() ?></dd>

                        <dt><?php echo Yii::t('app', 'Kernel version') ?></dt>
                        <dd><?php echo $provider->getOsKernelVersion() ?></dd>
                    </dl>
                </div><!-- /.box-body -->
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-hdd-o"></i>
                    <h3 class="box-title"><?php echo Yii::t('app', 'Time') ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo Yii::t('app', 'System Date') ?></dt>
                        <dd><?php echo Yii::$app->formatter->asDate(time()) ?></dd>

                        <dt><?php echo Yii::t('app', 'System Time') ?></dt>
                        <dd><?php echo Yii::$app->formatter->asTime(time()) ?></dd>

                        <dt><?php echo Yii::t('app', 'Timezone') ?></dt>
                        <dd><?php echo date_default_timezone_get() ?></dd>
                    </dl>
                </div><!-- /.box-body -->
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-hdd-o"></i>
                    <h3 class="box-title"><?php echo Yii::t('app', 'Network') ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo Yii::t('app', 'Hostname') ?></dt>
                        <dd><?php echo $provider->getHostname() ?></dd>

                        <dt><?php echo Yii::t('app', 'Internal IP') ?></dt>
                        <dd><?php echo $provider->getServerIP() ?></dd>

                        <dt><?php echo Yii::t('app', 'External IP') ?></dt>
                        <dd><?php echo $provider->getExternalIP() ?></dd>

                        <dt><?php echo Yii::t('app', 'Port') ?></dt>
                        <dd><?php echo $provider->getServerVariable('SERVER_PORT') ?></dd>
                    </dl>
                </div><!-- /.box-body -->
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-hdd-o"></i>
                    <h3 class="box-title"><?php echo Yii::t('app', 'Software') ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo Yii::t('app', 'Web Server') ?></dt>
                        <dd><?php echo $provider->getServerSoftware() ?></dd>

                        <dt><?php echo Yii::t('app', 'PHP Version') ?></dt>
                        <dd><?php echo $provider->getPhpVersion() ?></dd>

                        <dt><?php echo Yii::t('app', 'DB Type') ?></dt>
                        <dd><?php echo $provider->getDbType(Yii::$app->db->pdo) ?></dd>

                        <dt><?php echo Yii::t('app', 'DB Version') ?></dt>
                        <dd><?php echo $provider->getDbVersion(Yii::$app->db->pdo) ?></dd>
                    </dl>
                </div><!-- /.box-body -->
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-hdd-o"></i>
                    <h3 class="box-title"><?php echo Yii::t('app', 'Memory') ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo Yii::t('app', 'Total memory') ?></dt>
                        <dd><?php echo Yii::$app->formatter->asSize($provider->getTotalMem()) ?></dd>

                        <dt><?php echo Yii::t('app', 'Free memory') ?></dt>
                        <dd><?php echo Yii::$app->formatter->asSize($provider->getFreeMem()) ?></dd>

                        <dt><?php echo Yii::t('app', 'Total Swap') ?></dt>
                        <dd><?php echo Yii::$app->formatter->asSize($provider->getTotalSwap()) ?></dd>

                        <dt><?php echo Yii::t('app', 'Free Swap') ?></dt>
                        <dd><?php echo Yii::$app->formatter->asSize($provider->getFreeSwap()) ?></dd>
                    </dl>
                </div><!-- /.box-body -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>
                        <?php echo gmdate('H:i:s', $provider->getUptime()) ?>
                    </h3>
                    <p>
                        <?php echo Yii::t('app', 'Uptime') ?>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-clock-o"></i>
                </div>
                <div class="small-box-footer">
                    &nbsp;
                </div>
            </div>
        </div><!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>
                        <?php echo $provider->getLoadAverage() ?>
                    </h3>
                    <p>
                        <?php echo Yii::t('app', 'Load average') ?>
                    </p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <div class="small-box-footer">
                    &nbsp;
                </div>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>
                        <?php echo User::find()->count() ?>
                    </h3>
                    <p>
                        <?php echo Yii::t('app', 'User Registrations') ?>
                    </p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="<?php echo Yii::$app->urlManager->createUrl(['/user/admin']) ?>" class="small-box-footer">
                    <?php echo Yii::t('app', 'More info') ?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div><!-- ./col -->
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div id="cpu-usage" class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">
                        <?php echo Yii::t('app', 'CPU Usage') ?>
                    </h3>
                    <div class="box-tools pull-right">
                        <?php echo Yii::t('app', 'Real time') ?>
                        <div class="realtime btn-group" data-toggle="btn-toggle">
                            <button type="button" class="btn btn-default btn-xs active" data-toggle="on">
                                <?php echo Yii::t('app', 'On') ?>
                            </button>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="off">
                                <?php echo Yii::t('app', 'Off') ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="chart" style="height: 300px;">
                    </div>
                </div><!-- /.box-body-->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div id="memory-usage" class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">
                        <?php echo Yii::t('app', 'Memory Usage') ?>
                    </h3>
                    <div class="box-tools pull-right">
                        <?php echo Yii::t('app', 'Real time') ?>
                        <div class="btn-group realtime" data-toggle="btn-toggle">
                            <button type="button" class="btn btn-default btn-xs active" data-toggle="on">
                                <?php echo Yii::t('app', 'On') ?>
                            </button>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="off">
                                <?php echo Yii::t('app', 'Off') ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="chart" style="height: 300px;">
                    </div>
                </div><!-- /.box-body-->
            </div>
        </div>
    </div>
</div>
