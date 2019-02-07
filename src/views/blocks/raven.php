<?php

// Raven is Sentry's library to flag JS errors. Should only be enabled if sentry is set and external
// calls aren't disabled.
if (getenv('SENTRY_DSN') != '' && Yii::$app->params['disable_external'] === false) {
    $this->registerJs("var sentryDsn = '" . getenv('SENTRY_DSN') . "' ;\n", \yii\web\View::POS_HEAD);
    $this->registerJs("Raven.config(sentryDsn).install();", \yii\web\View::POS_END);
    $this->registerJsFile('https://cdn.ravenjs.com/3.8.1/raven.min.js');
}
