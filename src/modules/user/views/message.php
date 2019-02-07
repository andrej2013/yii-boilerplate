<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var yii\web\View         $this
 * @var dektrium\user\Module $module
 */

$this->title = $title;
if (isset($layout)) {
    $this->context->layout = $layout;
}
?>
<div class="login-box">
    <div class="login-logo logo-image">

    </div>
    <?php
    echo \dmstr\widgets\Alert::widget();
    ?>
</div>
