<div class="row">
    <div class="col-md-3">
        <?php
        $this->beginBlock('profileBody');
        ?>

        <img class="profile-user-img img-responsive img-circle" src="<?php echo Yii::$app->user->identity->getProfilePicture(); ?>" alt="User profile picture">

        <h3 class="profile-username text-center"><?php echo Yii::$app->user->identity->toString; ?></h3>

        <p class="text-muted text-center"><?php echo Yii::t('app', 'Member since {date}', ['date' => date('M.Y', Yii::$app->user->identity->created_at)]) ?></p>


        <?php
        $this->endBlock();
        ?>
        <?php
        echo \insolita\wgadminlte\LteBox::widget([
            'body'        => $this->blocks['profileBody'],
            'type'        => \Yii::$app->params['style']['primary_color'],
            'topTemplate' => '<div {options}>
<div class="box-body">',
        ]);
        ?>
    </div>
    <div class="col-md-9">
        <div class="nav-tabs-custom">
            <?php
            echo \dmstr\bootstrap\Tabs::widget([
                'items' => [
                    [
                        'url'    => ['/user/settings/profile'],
                        'label'  => Yii::t('app', 'Profile'),
                        'active' => Yii::$app->controller->getRoute() == 'user/settings/profile',
                    ],
                    [
                        'url'    => ['/user/settings/account'],
                        'label'  => Yii::t('app', 'Account'),
                        'active' => Yii::$app->controller->getRoute() == 'user/settings/account',
                    ],
                    [
                        'label'   => Yii::t('app', 'Networks'),
                        'url'     => ['/user/settings/networks'],
                        'visible' => $networksVisible,
                        'active'  => Yii::$app->controller->getRoute() == 'user/settings/networks',
                    ],
                ],
            ]);
            ?>
            <div class="tab-content">
                <?php
                echo $this->render($section, [
                    'model' => $model,
                    'user'  => isset($user) ? $user : null,
                ]);
                ?>
            </div>
        </div>
    </div>

</div>